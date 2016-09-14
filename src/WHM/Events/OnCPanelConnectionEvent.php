<?php

namespace Chernoff\WHM\Events;

use Chernoff\WHM\CPanel;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnCPanelConnectionEvent
 * @package Chernoff\WHM\Events
 */
class OnCPanelConnectionEvent extends Event
{
    const NAME = 'whm.connect.cpanel';

    /**
     * @var CPanel
     */
    private $connection;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param CPanel $connection
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     */
    public function __construct(CPanel $connection, $host, $port, $username, $password)
    {
        $this->connection = $connection;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return CPanel
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param CPanel $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}
