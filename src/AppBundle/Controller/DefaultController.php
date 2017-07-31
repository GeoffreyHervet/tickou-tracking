<?php

namespace AppBundle\Controller;

use AppBundle\Bag\ShopifyBag;
use AppBundle\Notifier\SlackNotifier;
use AppBundle\Provider\AccessTokenProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController
{
    /**
     * @var ShopifyBag
     */
    private $shopifyBag;

    /**
     * DefaultController constructor.
     *
     * @param ShopifyBag $shopifyBag
     */
    public function __construct(ShopifyBag $shopifyBag)
    {
        $this->shopifyBag = $shopifyBag;
    }

    /**
     * @Route("/install", name="homepage")
     */
    public function indexAction(Request $request, RouterInterface $router): RedirectResponse
    {
        if ($request->get('protocol')) {
            return new RedirectResponse($router->generate('app_index'));
        }
        $shopifyBag = $this->shopifyBag;
        $backUrl = 'https://' . $request->get('shop') . '/admin/oauth/authorize'
            . '?client_id=' . $shopifyBag->get('key')
            . '&scope=' . $shopifyBag->get('scope')
            . '&redirect_uri=' . $router->generate('auth', [], RouterInterface::ABSOLUTE_URL);

        return new RedirectResponse($backUrl);
    }

    /**
     * @Route("/auth", name="auth")
     */
    public function authAction(Request $request, AccessTokenProvider $accessTokenProvider, UserInterface $user, SlackNotifier $notifier)
    {
        $shopifyBag = $this->shopifyBag;
        $accessTokenProvider->refreshAccessToken($user, $request->get('code'));
        $notifier->notifyNewClient($user);

        return new RedirectResponse('https://' . $user->getShop() . '/admin/apps/'. $shopifyBag->get('identifier'));
    }
}
