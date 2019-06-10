<?php

namespace Bee\Game\Node;

use Bee\Server\CustomProcess;
use Bee\Game\Context;
use Bee\Game\Application;
use Bee\Game\Code;

/**
 * Websocket 服务基类
 *
 * @package Bee\Game\Node
 */
class Websocket extends \Bee\Server\Websocket
{
    /** @var Connector */
    protected $connector;

    /** @var ClientPool */
    protected $clientPool;

    /**
     * 自定义进程初始化
     *
     * @return void
     */
    public function init()
    {
        // 初始化当前服务通信进程
        // 负载处理当前节点与中心节点通信业务
        // 实例将会被传递至各个工作进程中
        $connector         = new CustomProcess($this->swoole, Connector::class);
        // 保存至进程列表
        // 等待 Server 进程启动时进行挂载
        $this->processes[] = $connector;
        $this->connector   = $connector->getInstance();

        // 客户端连接对象池
        $this->clientPool  = new ClientPool;
    }

    /**
     * 客户端连接打开时回调
     *
     * @param \Bee\Server\Websocket $server
     * @param \Swoole\Http\Request $request
     * @return void
     */
    public function onOpen($server, $request)
    {
        // 初始化上下文，构造身份鉴别请求参数
        $context = new Context(
            $request->fd,
            [
                'c' => '1,1', // 1,1 默认为身份鉴定路由地址（从0开始会路由转换异常）
                'd' => [
                    'token' => $request->header['x-token'] ?? ''
                ]
            ]
        );

        // 执行业务处理
        (new Application)
            ->setContext($context)
            ->handle()
        ;


        // 获取设备(用户)唯一标识
        $uuid = $context->get('uuid');
        // 身份识别失败
        // 关闭连接，节省连接，防止恶意占用连接
        if (empty($uuid)) {
            $server->push($request->fd, 'unauthorized');
            $server->close($request->fd);
        }

        // 连接与用户映射
        $this->clientPool->set($request->fd, $uuid);
    }

    /**
     * 客户端消息接收时回调方法
     *  $frame->data = [
     *      c: 动作码（0,0/主码,子码）
     *      d: 数据体
     *  ]
     *
     * @param \Swoole\WebSocket\Server $server
     * @param Frame $frame
     * @return void
     */
    public function onMessage($server, $frame)
    {
        $fd      = $frame->fd;
        // 客户端请求数据格式强制为 json
        $data    = json_decode($frame->data, true);

        // 初始化上下文，注入客户端数据
        $context = new Context($fd, $data);

        // 还原当前连接标识
        $mapping = $this->clientPool->get($fd);
        $context->set('uuid', $mapping['u']);

        // 执行业务处理
        (new Application)
            ->setContext($context)
            ->handle()
        ;
    }

    /**
     * 连接关闭
     *
     * @param \Swoole\WebSocket\Server $server
     * @param string $fd
     * @param int $reatorId
     * @return void
     */
    public function onClose($server, $fd, $reatorId)
    {
        // 从客户端连接池移除该连接
        $uuid = $this->clientPool->del($fd);
        // 通过通信进程转发断开消息至服务中心
        $this->connector->write(json_encode(['c' => Code::N_1000, 'd' => $uuid]));
    }
}

