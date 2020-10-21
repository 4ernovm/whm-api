<?php

namespace Chernoff\WHM\AuthorizationAdapters;

use Chernoff\WHM\Interfaces\AuthorizationInterface;

class ApiToken implements AuthorizationInterface
{
    /** @var string */
    private $username;

    /** @var string */
    protected $token;

    /**
     * ApiToken constructor.
     * @param $username
     * @param $token
     */
    public function __construct($username, $token)
    {
        $this->username = $username;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array("Authorization" => "whm {$this->username}:{$this->token}");
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "Authorization: whm {$this->username}:{$this->token}";
    }
}
