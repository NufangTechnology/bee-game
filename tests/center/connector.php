<?php
use Bee\Server\ProcessInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

class connector implements ProcessInterface
{
    public function handle($server, $process)
    {
        file_put_contents(__DIR__ . '/http-process.log', time() . PHP_EOL, 8);
    }
}
