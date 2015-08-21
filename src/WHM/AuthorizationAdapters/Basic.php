<?php

namespace Chernoff\WHM\AuthorizationAdapters;

use Chernoff\WHM\Interfaces\AuthorizationInterface;

class Basic implements AuthorizationInterface
{
    /** @var  string */
    protected $username, $password;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array("Authorization" => "Basic " . base64_encode("{$this->username}:{$this->password}"));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "Authorization: Basic " . base64_encode("{$this->username}:{$this->password}");
    }
}
