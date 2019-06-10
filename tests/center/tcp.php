<?php
use Bee\Server\TCP;

require __DIR__ . '/../../vendor/autoload.php';

class server extends TCP
{
    public function onConnect($server, $fd, $reactorId)
    {
    }

    public function onReceive($server, $fd, $reactorId, $data)
    {
        var_dump($data);
        var_dump($fd);
        $server->send($fd, $data);
    }

    public function onClose($server, $fd, $reactorId)
    {
    }
}

$tcp = new server(
    [
        'host' => '0.0.0.0',
        'port' => 9007,
        'option' => [
            'pid_file'          => __DIR__ . '/center-erver.pid',
            'log_file'          => __DIR__ . '/center_server.log',
            'worker_num'        => 1,
            'task_worker_num'   => 1,
        ]
    ]
);

switch ($_SERVER['argv'][1]) {
    case 'start':
        $tcp->start(false);
    break;

    case 'status':
        $tcp->status();
        break;
}
