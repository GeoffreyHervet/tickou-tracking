<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fulfillment")
 * @ORM\Entity()
 */
class Fulfillment
{

    public const TYPE_IN_PROGRESS = 0;
    public const TYPE_DONE = 1;
    public const TYPE_FAILURE = 2;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $user;

    /**
     * @var int
     * @ORM\Column(name="`type`", type="integer", nullable=false)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="context", type="text", nullable=true)
     */
    protected $context;

    /**
     * @var int
     * @ORM\Column(name="order_number", type="bigint")
     */
    protected $order;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $trackingNumber;

    /**
     * @var string
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    protected $trackingLink;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $shipperName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $supplier;

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     *
     * @return Fulfillment
     */
    public function setTrackingNumber(string $trackingNumber): Fulfillment
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    /**
     * @param mixed $id
     *
     * @return Fulfillment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return Fulfillment
     */
    public function setUser(User $user): Fulfillment
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return Fulfillment
     */
    public function setType(int $type): Fulfillment
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $context
     *
     * @return Fulfillment
     */
    public function setContext(string $context): Fulfillment
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @param int $order
     *
     * @return Fulfillment
     */
    public function setOrder(int $order): Fulfillment
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param string $trackingLink
     *
     * @return Fulfillment
     */
    public function setTrackingLink(string $trackingLink): Fulfillment
    {
        $this->trackingLink = $trackingLink;

        return $this;
    }

    /**
     * @param string $shipperName
     *
     * @return Fulfillment
     */
    public function setShipperName(string $shipperName): Fulfillment
    {
        $this->shipperName = $shipperName;

        return $this;
    }

    /**
     * @param string $supplier
     *
     * @return Fulfillment
     */
    public function setSupplier(string $supplier): Fulfillment
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getTrackingLink(): string
    {
        return $this->trackingLink;
    }

    /**
     * @return string
     */
    public function getShipperName(): string
    {
        return $this->shipperName;
    }

    /**
     * @return string
     */
    public function getSupplier(): string
    {
        return $this->supplier;
    }

    public function isDone(): bool
    {
        return $this->type === self::TYPE_DONE;
    }

    public function isInProgress(): bool
    {
        return $this->type === self::TYPE_IN_PROGRESS;
    }

    public function isFailed(): bool
    {
        return $this->type === self::TYPE_FAILURE;
    }

}
