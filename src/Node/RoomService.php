<?php

namespace Bee\Game\Node;

use Swoole\Process as SwooleProcess;
use Bee\Game\Code;

/**
 * 房间服务
 *
 * @package Bee\Game\Node
 */
abstract class RoomService
{
    /** @var SwooleProcess */
    protected $connector;

    /** @var int */
    protected $roomId;

    abstract public function start();

    abstract public function run();

    abstract public function stop();

    abstract public function exit();

    /**
     * 从中心节点同步房间成员列表信息
     *
     * @return void
     */
    // public function syncMemberList()
    // {
        // $this->connector->write(
            // json_decode(
                // [
                    // 'c' => Code::N_1001,
                    // 'd' => $this->roomId,
                // ]
            // )
        // );
    // }
}
