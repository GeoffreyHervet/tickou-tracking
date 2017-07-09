<?php

namespace AppBundle\Controller;

use AppBundle\Bag\ShopifyBag;
use AppBundle\Provider\AccessTokenProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController
{
    /**
     * @Route("/install", name="homepage")
     */
    public function indexAction(Request $request, RouterInterface $router, ShopifyBag $shopifyBag): RedirectResponse
    {
        $backUrl = 'https://' . $request->get('shop') . '/admin/oauth/authorize'
            . '?client_id=' . $shopifyBag->get('key')
            . '&scope=' . $shopifyBag->get('scope')
            . '&redirect_uri=' . $router->generate('auth', [], RouterInterface::ABSOLUTE_URL);

        return new RedirectResponse($backUrl);
    }

    /**
     * @Route("/auth", name="auth")
     */
    public function authAction(Request $request, AccessTokenProvider $accessTokenProvider, UserInterface $user)
    {
        $accessTokenProvider->refreshAccessToken($user, $request->get('code'));

        return new RedirectResponse('https://' . $user->getShop() . '/admin');
    }
}
