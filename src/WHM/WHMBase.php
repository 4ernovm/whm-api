<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Events\OnConnectionReleaseEvent;
use Chernoff\WHM\Events\OnRequestEvent;
use Chernoff\WHM\Events\OnResponseEvent;
use Chernoff\WHM\Events\OnValidationEvent;
use Chernoff\WHM\Interfaces\AuthorizationInterface;
use Chernoff\WHM\Interfaces\DeployerInterface;
use Chernoff\WHM\Interfaces\ValidationRuleInterface;
use Chernoff\WHM\ValidationRules\HasError;
use Chernoff\WHM\ValidationRules\IsNull;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class WHMBase
 * @package Chernoff\WHM
 */
abstract class WHMBase
{
    /**
     * @var  DeployerInterface
     */
    protected $deployer;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var ValidationRuleInterface[]
     */
    protected $defaultRules;

    /**
     * @var int
     */
    protected $defaultPort;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param GuzzleDeployer $deployer
     * @param Validator $validator
     * @param EventDispatcher $dispatcher
     */
    public function __construct(GuzzleDeployer $deployer, Validator $validator, EventDispatcher $dispatcher) {
        $this->deployer  = $deployer;
        $this->validator = $validator;
        $this->dispatcher = $dispatcher;

        $this->defaultRules = array(new IsNull, new HasError);
    }

    /**
     * @param AuthorizationInterface $auth
     * @return $this
     */
    public function setAuth(AuthorizationInterface $auth)
    {
        $this->deployer->setAuth($auth);

        return $this;
    }

    /**
     * @return AuthorizationInterface
     */
    public function getAuth()
    {
        return $this->deployer->getAuth();
    }

    /**
     * @param $host
     * @param $port
     * @param bool $isSecure
     * @return $this
     */
    public function setTarget($host, $port, $isSecure = true)
    {
        $this->deployer->setHost($host)->setPort($port)->setProtocol(($isSecure) ? 'https' : 'http');

        return $this;
    }

    /**
     * @param $method
     * @param array $args
     * @param ValidationRuleInterface[] $rules
     * @return mixed
     */
    public function send($method, array $args = array(), array $rules = array())
    {
        if (empty($rules)) {
            $rules = $this->defaultRules;
        }

        /** @var OnRequestEvent $event */
        $event = $this->dispatcher->dispatch(OnRequestEvent::NAME, new OnRequestEvent($this->deployer, $method, $args));
        $response = $this->deployer->send($event->getMethod(), $event->getArgs(), array(), 'GET', $event->getOptions());

        /** @var OnResponseEvent $event */
        $event = $this->dispatcher->dispatch(OnResponseEvent::NAME, new OnResponseEvent($this->deployer, $response));
        $response = $event->getResponse();

        /** @var OnValidationEvent $event */
        $event = $this->dispatcher->dispatch(OnValidationEvent::NAME, new OnValidationEvent($response, $rules));
        $this->validator->validate($event->getResponse(), $event->getRules());

        return $event->getResponse();
    }

    /**
     * Use default rules set including the given one
     *
     * @param ValidationRuleInterface $rule
     * @return ValidationRuleInterface[]
     */
    public function addRule(ValidationRuleInterface $rule)
    {
        return $this->addRules(array($rule));
    }

    /**
     * @param array $rules
     * @return ValidationRuleInterface[]
     */
    public function addRules(array $rules)
    {
        return $this->defaultRules + $rules;
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return GuzzleDeployer|DeployerInterface
     */
    public function getDeployer()
    {
        return $this->deployer;
    }

    /**
     * Dispatches event which indicates that current connection is no longer needed.
     */
    public function release()
    {
        $this->dispatcher->dispatch(OnConnectionReleaseEvent::NAME, new OnConnectionReleaseEvent($this));
    }
}
