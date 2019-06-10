<?php

namespace Bee\Game\Node\Middleware;

use Bee\Game\Middleware;
use Bee\Game\Application;
use Bee\Game\Context;
use Bee\Game\RouteDispatch;
use Bee\Game\Exception;

/**
 * 路由中间件
 *
 * @package Bee\Game\Node\Middleware
 */
class Route extends Middleware
{
    /**
     * 中间件业务执行体
     *
     * @param Application $application
     * @param Context $context
     * @param mixed $parameters
     * @return mixed
     * @throws Exception
     * @throws \Bee\Router\Exception
     */
    public function call(Application $application, Context $context, $parameters = null)
    {
        // 将客户端请求码（主码/子码）转换为 URL 路径风格
        $path    = str_replace(',', '/', $context->getCode());

        $handler = RouteDispatch::match($path);
        // 不存在匹配路由，直接切断客户端连接
        if ($handler === false) {
            throw new Exception('Not match', 0, [$path]);
        }

        $handler->callMethod($application);

        return true;
    }
}
