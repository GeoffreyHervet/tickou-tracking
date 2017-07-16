<?php

namespace AppBundle\Consumer;

use AppBundle\Bridge\ShopifyBridge;
use AppBundle\Entity\Fulfillment;
use AppBundle\Exception\TooManyRequestException;
use GuzzleHttp\Exception\BadResponseException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FulfillmentConsumer implements ConsumerInterface
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var ShopifyBridge
     */
    private $shopifyBridge;

    /**
     * @var int
     */
    private $errorCount;

    public function __construct(RegistryInterface $doctrine, ShopifyBridge $shopifyBridge)
    {
        $this->doctrine = $doctrine;
        $this->shopifyBridge = $shopifyBridge;
        $this->errorCount = 0;
    }

    public function execute(AMQPMessage $msg)
    {
        $fulfillment = $this->loadFulfillment((int)$msg->getBody());
        $status = Fulfillment::TYPE_FAILURE;
        $returnValue = true;
        try {
            $context = $this->shopifyBridge->exportFulfillment($fulfillment);
            $status = Fulfillment::TYPE_DONE;
            $this->errorCount = 0;
        } catch (TooManyRequestException $exception) {
            sleep(1);

            return false;
        } catch (BadResponseException $exception) {
            $context = $exception->getResponse()->getBody()->getContents();
            $returnValue = false;
        } catch (\Exception $exception) {
            if (++$this->errorCount >= 5) {
                die;
            }
            $context = json_encode(['exception' => $exception->getMessage()]);
            $returnValue = false;
        }

        $em = $this->doctrine->getManager();
        $fulfillment->setContext($context);
        $fulfillment->setType($status);
        $em->flush($fulfillment);
        $em->clear();

        return $returnValue;
    }

    private function loadFulfillment(int $id): Fulfillment
    {
        return $this->doctrine->getManager()->find(Fulfillment::class, $id);
    }

}
