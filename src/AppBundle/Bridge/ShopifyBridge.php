<?php

namespace AppBundle\Bridge;

use AppBundle\Entity\Fulfillment;
use AppBundle\Entity\User;
use AppBundle\Exception\TooManyRequestException;
use AppBundle\Factory\GuzzleFactory;
use GuzzleHttp\Exception\BadResponseException;

class ShopifyBridge
{
    public function exportFulfillment(Fulfillment $fulfillment): string
    {
        $client = GuzzleFactory::create($fulfillment->getUser());
        $apiEndPoint = "/admin/orders/{$fulfillment->getOrder()}/fulfillments.json";
        $fulfillmentArray = array_filter([
            'tracking_number' => $fulfillment->getTrackingNumber(),
            'tracking_company' => $fulfillment->getShipperName(),
            'tracking_url' => $fulfillment->getTrackingLink(),
        ]);

        try {
            $response = $client->post($apiEndPoint, ['body' => json_encode(['fulfillment' => $fulfillmentArray])]);
        } catch (BadResponseException $exception) {
            $this->handleBadResponse($exception);
        }

        return $response->getBody()->getContents();
    }

    public function getDetails(User $user): array
    {
        $client = GuzzleFactory::create($user);
        return json_decode($client->get('/admin.json')->getBody()->getContents());
        return [];
    }

    private function handleBadResponse(BadResponseException $exception)
    {
        if ($exception->getCode() === 429) {
            throw new TooManyRequestException();
        }

        throw $exception;
    }
}
