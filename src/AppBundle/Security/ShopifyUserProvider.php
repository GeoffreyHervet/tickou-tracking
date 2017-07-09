<?php


namespace AppBundle\Security;

use AppBundle\Manager\UserManager;
use AppBundle\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ShopifyUserProvider implements UserProviderInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * ShopifyUserProvider constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function loadUserByUsername($shop)
    {
        return $this->userManager->findOrCreate($shop);
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
