<?php

namespace Bee\Game\Node;

use Swoole\Table;

class ClientPool
{
    /** @var Table */
    private $table;

    /**
     * ClientPool constructor.
     */
    public function __construct()
    {
        $this->table = new Table(1024 * 200);
        $this->table->column('u', Table::TYPE_STRING, 24);
        $this->table->create();
    }

    /**
     * 添加记录
     *
     * @param string $key
     * @param string $uuid
     */
    public function set(string $key, string $uuid)
    {
        $this->table->set($key, ['u' => $uuid]);
    }

    /**
     * 获取记录
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->table->get($key);
    }

    /**
     * 删除记录
     *
     * @param string $key
     * @return mixed
     */
    public function del(string $key)
    {
        $uuid = $this->table->get($key);
        $this->table->del($key);

        return $uuid;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }
}

