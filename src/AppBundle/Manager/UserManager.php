<?php

namespace AppBundle\Manager;

use AppBundle\Model\User;
use AppBundle\Entity\User as UserEntity;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserManager
{
    /**
     * @var RegistryInterface
     */
    private $registryInterface;

    /**
     * UserManager constructor.
     *
     * @param RegistryInterface $registryInterface
     */
    public function __construct(RegistryInterface $registryInterface)
    {
        $this->registryInterface = $registryInterface;
    }

    public function findOrCreate(string $shop): User
    {
        $user = $this->getRepository()->findOneBy([
            'shop' => $shop
        ]);

        if (null !== $user) {
            return $user;
        }

        $user = new UserEntity($shop);
        $em = $this->registryInterface->getManager();
        $em->persist($user);
        $em->flush($user);

        return $user;
    }

    public function update(User $user)
    {
        $this->registryInterface->getManager()->flush($user);
    }

    private function getRepository()
    {
        return $this->registryInterface->getManager()->getRepository(UserEntity::class);
    }
}
