<?php

use Swoole\Event;
use Swoole\Client;
use Swoole\Process as SwooleProcess;
use Bee\Server\ProcessInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

class connector implements ProcessInterface
{
    protected $server;
    protected $process;

    /**
     * @param SwooleProcess $process
     */
    public function handle($server, $process)
    {
        $this->server = $server;
        $this->process = $process;

        file_put_contents(__DIR__ . '/heart.txt', time() . PHP_EOL, 8);

        $client = new Client(SWOOLE_SOCK_TCP);
        $client->connect('0.0.0.0', 9007);
        $client->send('hello');

        Event::add($this->process->pipe, function () use ($client) {
            $recv = $this->process->read();
            $client->send($recv);
        });

        Event::add($client, function () use ($client) {
            $data = $client->recv();
            var_dump($data);
        });
    }
}
