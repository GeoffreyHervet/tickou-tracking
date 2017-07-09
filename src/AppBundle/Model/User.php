<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements UserInterface, EquatableInterface
{
    public const USERNAME = 'shopify';
    protected const PASSWORD = '******';

    /**
     * @var string
     */
    protected $shop;

    public function __construct(string $shop)
    {
        $this->shop = $shop;
    }

    public function getShop(): string
    {
        return $this->shop;
    }

    public function isEqualTo(UserInterface $user)
    {
        return
            $user instanceof self
            && $user->getShop() === $this->getShop()
        ;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return self::PASSWORD;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return self::USERNAME;
    }

    public function eraseCredentials()
    {

    }

}
