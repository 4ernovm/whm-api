<?php

namespace Chernoff\WHM\Events;

use Chernoff\WHM\Interfaces\DeployerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnRequestEvent
 * @package Chernoff\WHM\Events
 */
class OnRequestEvent extends Event
{
    const NAME = 'whm.request';

    /**
     * @var DeployerInterface
     */
    private $deployer;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $args;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @param DeployerInterface $deployer
     * @param $method
     * @param array $args
     */
    public function __construct(DeployerInterface $deployer, $method, array $args)
    {
        $this->deployer = $deployer;
        $this->method = $method;
        $this->args = $args;
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

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
