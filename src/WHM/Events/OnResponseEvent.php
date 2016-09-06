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
     * @var DeployerInterface
     */
    private $deployer;

    /**
     * @param DeployerInterface $deployer
     * @param $response
     */
    public function __construct(DeployerInterface $deployer, $response)
    {
        $this->response = $response;
        $this->deployer = $deployer;
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

    /**
     * @return DeployerInterface
     */
    public function getDeployer()
    {
        return $this->deployer;
    }

    /**
     * @param DeployerInterface $deployer
     */
    public function setDeployer($deployer)
    {
        $this->deployer = $deployer;
    }
}
