<?php

namespace Bee\Game\Node;

use Bee\Server\ProcessInterface;
use Swoole\Process as SwooleProcess;

/**
 * 子节点与中心节点通信进程
 *
 * @package Bee\Game\Node
 */
class Connector implements ProcessInterface
{
    /**
     * 业务执行
     *
     * @param \Bee\Server\Websocket $server
     * @param SwooleProcess $process
     * @return void
     */
    public function handle($server, $process)
    {
        $client = new Client(SWOOLE_SOCK_TCP);
        $client->connect('0.0.0.0', 9007);
        $client->send('hello');

        Event::add($client, function () use ($client) {
            $data = $client->recv();
            var_dump($data);
        });

        // 监听进程消息
        Event::add($this->process->pipe, function () use ($client) {
            $recv = $this->process->read();
            $client->send($recv);
        });
    }
}

