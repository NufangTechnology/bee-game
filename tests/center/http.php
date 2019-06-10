<?php
use Bee\Server\HTTP;
use Bee\Server\CustomProcess;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/connector.php';

class server extends HTTP
{
    public function initProcess()
    {
        $this->processes[] = new CustomProcess($this->swoole, connector::class);
    }

    public function onRequest($request, $response)
    {
        $response->end(time());
    }
}

$http = new server(
    [
        'host' => '0.0.0.0',
        'port' => 9008,
        'option' => [
            'pid_file'          => __DIR__ . '/http-server.pid',
            'log_file'          => __DIR__ . '/http_server.log',
            'worker_num'        => 1,
            'task_worker_num'   => 1,
        ]
    ]
);

switch ($_SERVER['argv'][1]) {
    case 'start':
        $http->start(false);
    break;

    case 'status':
        $http->status();
        break;
}

