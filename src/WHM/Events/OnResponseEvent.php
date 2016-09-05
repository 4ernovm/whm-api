<?php

namespace Chernoff\WHM\Events;

use Chernoff\WHM\Interfaces\DeployerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnResponseEvent
 * @package Chernoff\WHM\Events
 */
class OnResponseEvent extends Event
{
    const NAME = 'whm.response';

    /**
     * @var mixed
     */
    private $response;

    /**
     * @param mixed $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}
