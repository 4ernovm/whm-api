<?php

namespace Chernoff\WHM\Events;

use Chernoff\WHM\Interfaces\DeployerInterface;
use Chernoff\WHM\Interfaces\ValidationRuleInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnValidationEvent
 * @package Chernoff\WHM\Events
 */
class OnValidationEvent extends Event
{
    const NAME = 'whm.validation';

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var ValidationRuleInterface[]
     */
    private $rules;

    /**
     * @param mixed $response
     * @param ValidationRuleInterface[] $rules
     */
    public function __construct($response, array $rules)
    {
        $this->response = $response;
        $this->rules = $rules;
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
     * @return ValidationRuleInterface[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param ValidationRuleInterface[] $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }
}
