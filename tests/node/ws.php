<?php
use Bee\Server\Websocket;
use Bee\Server\CustomProcess;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/process.php';

class WS extends Websocket
{
    public function initProcess()
    {
        $this->processes[] =  new CustomProcess($this->swoole, connector::class);
    }

    public function onOpen($server, $request)
    {
    }

    public function onMessage($server, $frame)
    {
        $server->push($frame->fd, $frame->data);
    }

    public function onClose($server, $fd, $reactorId)
    {
        var_dump($fd);
    }
}

$node = new WS(
    [
        'host' => '0.0.0.0',
        'port' => 9006,
        'option' => [
            'pid_file'          => __DIR__ . '/bee-server.pid',
            'log_file'          => __DIR__ . '/bee_server.log',
            'worker_num'        => 1,
            'task_worker_num'   => 1,
        ]
    ]
);

switch ($_SERVER['argv'][1]) {
    case 'start':
        $node->start(false);
    break;

    case 'status':
        $node->status();
        break;
}

