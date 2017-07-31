<?php

namespace AppBundle\Notifier;

use AppBundle\Entity\User;
use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Transport\ApiClient;

class SlackNotifier
{
    /**
     * @var ApiClient
     */
    private $slackClient;

    /**
     * SlackNotifier constructor.
     *
     * @param ApiClient $slackClient
     */
    public function __construct(ApiClient $slackClient)
    {
        $this->slackClient = $slackClient;
    }

    public function notifyNewClient(User $user)
    {
        $payload = new ChatPostMessagePayload();
        $payload->setChannel('#import-tracking');
        $payload->setText('New user https://'. $user->getShopName() . '.myshopify.com');
        $payload->setUsername('Bot');
        $payload->setIconEmoji('smile');

        $this->slackClient->send($payload);

    }

}
