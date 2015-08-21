<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Interfaces\DeployerInterface;
use Chernoff\WHM\Interfaces\ValidationRuleInterface;
use Chernoff\WHM\ValidationRules\HasError;
use Chernoff\WHM\ValidationRules\IsNull;

/**
 * Class WHMBase
 * @package Chernoff\WHM
 */
abstract class WHMBase
{
    /** @var  DeployerInterface */
    protected $deployer;

    /** @var Validator  */
    protected $validator;

    /** @var ValidationRuleInterface[] */
    protected $defaultRules;

    /** @var int */
    protected $defaultPort;

    public function __construct(GuzzleDeployer $deployer, Validator $validator) {
        $this->deployer  = $deployer;
        $this->validator = $validator;

        $this->defaultRules = array(new IsNull, new HasError);
    }

    /**
     * @param array $credentails
     * @return $this
     */
    public function setCredentials(array $credentails)
    {
        if (empty($credentails['port'])) {
            $credentails['port'] = $this->defaultPort;
        }

        $this->deployer->setCredentials($credentails);

        return $this;
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return $this->deployer->getCredentials();
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

        $response = $this->deployer->send($method, $args);

        $this->validator->validate($response, $rules);

        return $response;
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
}
