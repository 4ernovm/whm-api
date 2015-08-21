<?php

namespace Chernoff\WHM\AuthorizationAdapters;

use Chernoff\WHM\Interfaces\AuthorizationInterface;

class WHM implements AuthorizationInterface
{
    /** @var  string */
    protected $hash;

    /**
     * @param string $hash
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array("Authorization" => "WHM root:{$this->hash}");
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "Authorization: WHM root:{$this->hash}";
    }
}
