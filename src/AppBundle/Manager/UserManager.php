<?php

namespace AppBundle\Manager;

use AppBundle\Model\User;
use AppBundle\Entity\User as UserEntity;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserManager
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * UserManager constructor.
     *
     * @param ManagerRegistry $registryInterface
     */
    public function __construct(ManagerRegistry $registryInterface)
    {
        $this->doctrine = $registryInterface;
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
        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush($user);

        return $user;
    }

    public function update(User $user)
    {
        $this->doctrine->getManager()->flush($user);
    }

    private function getRepository()
    {
        return $this->doctrine->getManager()->getRepository(UserEntity::class);
    }
}
