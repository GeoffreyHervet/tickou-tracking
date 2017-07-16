<?php

namespace AppBundle\Factory;

use AppBundle\Entity\Fulfillment;
use AppBundle\Entity\User;

class FulfillmentFactory
{
    public static function createFromArray(array $data, User $user)
    {
        $fulfillment = self::create();

        $fulfillment
            ->setUser($user)
            ->setType(Fulfillment::TYPE_IN_PROGRESS)
            ->setOrder($data['order'])
            ->setTrackingNumber($data['tracking'])
            ->setShipperName($data['shipper'] ?? 'Default')
        ;
        if (isset($data['link'])) {
            $fulfillment->setTrackingLink($data['link']);
        }
        if (isset($data['supplier'])) {
            $fulfillment->setSupplier($data['supplier']);
        }

        return $fulfillment;
    }

    public static function create()
    {
        return new Fulfillment();
    }
}
