<?php

namespace Bee\Game\Center;

/**
 * TCP server
 *
 * @package Bee\Game\Center
 */
class TCP extends \Bee\Server\TCP
{
    public function onConnect($server, $fd, $reactorId)
    {
    }

    public function onReceive($server, $fd, $reactorId, $data)
    {
    }

    public function onClose($server, $fd, $reactorId)
    {
    }
}

