<?php

namespace AppBundle\Factory;

use AppBundle\Entity\User;
use GuzzleHttp\Client;

class GuzzleFactory
{
    public static function create(User $user): Client
    {
        return new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Shopify-Access-Token' => $user->getAccessToken(),
            ],
            'base_uri' => "https://{$user->getShop()}",
        ]);
    }
}
