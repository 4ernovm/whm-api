<?php

namespace Chernoff\WHM\Events;

use Chernoff\WHM\Interfaces\DeployerInterface;
use Chernoff\WHM\WHMBase;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnConnectionReleaseEvent
 * @package Chernoff\WHM\Events
 */
class OnConnectionReleaseEvent extends Event
{
    const NAME = 'whm.connection.release';

    /**
     * @var WHMBase
     */
    private $abstract;

    /**
     * @param WHMBase $abstract
     */
    public function __construct(WHMBase $abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * @return WHMBase
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param WHMBase $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }
}
