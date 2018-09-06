<?php

namespace mix\client;

use mix\helpers\CoroutineHelper;

/**
 * RedisCoroutine组件
 * @author 刘健 <coder.liu@qq.com>
 */
class RedisCoroutine extends BaseRedisPersistent
{

    /**
     * 连接池
     * @var \mix\pool\ConnectionPool
     */
    public $connectionPool;

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        // 开启协程
        CoroutineHelper::enableCoroutine();
    }

    // 析构事件
    public function onDestruct()
    {
        parent::onDestruct();
        // 关闭连接
        $this->disconnect();
    }

    // 连接
    protected function connect()
    {
        $this->_redis = $this->connectionPool->getConnection(function () {
            return parent::createConnection();
        });
    }

    // 关闭连接
    public function disconnect()
    {
        if (isset($this->_redis)) {
            $this->connectionPool->push($this->_redis);
        }
        parent::disconnect();
    }

    // 重新连接
    protected function reconnect()
    {
        parent::disconnect();
        $this->connectionPool->activeCountDecrement();
        $this->connect();
    }

}
