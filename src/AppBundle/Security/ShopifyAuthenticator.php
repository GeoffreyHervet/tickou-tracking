<?php

namespace AppBundle\Security;

use AppBundle\Bag\ShopifyBag;
use AppBundle\Model\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ShopifyAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ShopifyBag
     */
    private $shopifyBag;

    public function __construct(ShopifyBag $shopifyBag)
    {
        $this->shopifyBag = $shopifyBag;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function getCredentials(Request $request)
    {
        return $request;
    }

    /**
     * @param Request $request
     * @param UserProviderInterface $userProvider
     *
     * @return User|null
     */
    public function getUser($request, UserProviderInterface $userProvider)
    {
        if ($request->hasSession() && $request->getSession()->get('user')) {
            return $userProvider->refreshUser($request->getSession()->get('user'));
        }
        if (!$this->validateHmac($request)) {
            return null;
        }

        $user = $userProvider->loadUserByUsername($request->get('shop'));
        $request->getSession()->set('user', $user);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['error_code' => 401], JsonResponse::HTTP_FORBIDDEN);
    }

    public function supportsRememberMe()
    {
        return false;
    }

    private function validateHmac(Request $request): bool
    {
        $hmac = $request->get('hmac');
        $query = clone $request->query;
        $query->remove('hmac');
        $query->remove('signature');
        $arr = $query->all();
        ksort($arr);
        $str = [];
        foreach ($arr as $k => $v) {
            $str[] = $k .'='. $v;
        }
        $str = implode('&', $str);
        $digest = hash_hmac('sha256', $str, $this->shopifyBag->get('secret'));

        return ($digest === $hmac);
    }

}
