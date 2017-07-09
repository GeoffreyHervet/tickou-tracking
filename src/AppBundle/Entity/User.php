<?php

namespace AppBundle\Entity;

use AppBundle\Model\User as UserModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity()
 */
class User extends UserModel
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="shop", unique=true, nullable=false)
     */
    protected $shop;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $accessToken;

    public function __construct(string $shop)
    {
        parent::__construct($shop);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

}
