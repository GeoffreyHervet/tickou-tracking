<?php

namespace AppBundle\Provider;

use AppBundle\Bag\ShopifyBag;
use AppBundle\Manager\UserManager;
use AppBundle\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class AccessTokenProvider
{
    /**
     * @var ShopifyBag
     */
    private $shopifyBag;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * AccessTokenProvider constructor.
     *
     * @param ShopifyBag $shopifyBag
     * @param UserManager $userManager
     */
    public function __construct(ShopifyBag $shopifyBag, UserManager $userManager)
    {
        $this->shopifyBag = $shopifyBag;
        $this->userManager = $userManager;
    }

    public function refreshAccessToken(User $user, string $code)
    {
        $response = (new Client())->request('POST', 'https://' . $user->getShop() . '/admin/oauth/access_token', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'client_id' => $this->shopifyBag->get('key'),
                'client_secret' => $this->shopifyBag->get('secret'),
                'code' => $code,
            ])
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);
        $user->setAccessToken($responseData['access_token']);
        $this->userManager->update($user);
    }
}
