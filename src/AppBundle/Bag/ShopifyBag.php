<?php

namespace AppBundle\Bag;

use Symfony\Component\HttpFoundation\ParameterBag;

class ShopifyBag extends ParameterBag
{
    public function __call($name, $arguments)
    {
        return $this->get($name);
    }
}
