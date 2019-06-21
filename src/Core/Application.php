<?php

namespace Mix\Core;

use Mix\Bean\Beans;
use Mix\Component\ComponentDisabled;
use Mix\Component\ComponentInitialize;
use Mix\Component\ComponentInterface;
use Mix\Container\ContainerManager;
use Mix\Helper\FileSystemHelper;
use Mix\Bean\Object\AbstractObject;
use Psr\Container\ContainerInterface;

/**
 * Class Application
 * @package Mix\Core
 * @author liu,jian <coder.keda@gmail.com>
 */
class Application extends AbstractObject implements ContainerInterface
{

    /**
     * 应用名称
     * @var string
     */
    public $appName = 'app-console';

    /**
     * 应用版本
     * @var string
     */
    public $appVersion = '0.0.0';

    /**
     * 应用调试
     * @var bool
     */
    public $appDebug = true;

    /**
     * 基础路径
     * @var string
     */
    public $basePath = '';

    /**
     * 配置路径
     * @var string
     */
    public $configPath = 'config';

    /**
     * 运行目录路径
     * @var string
     */
    public $runtimePath = 'runtime';

    /**
     * 组件配置
     * @var ContainerManager
     */
    public $components = [];

    /**
     * 依赖配置
     * @var Beans
     */
    public $beans = [];

    /**
     * 容器
     * @var \Mix\Core\Container\Container
     */
    public $container;

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        // 实例化Beans
        $this->beans = new Beans([
            'container' => $this,
            'config'    => $this->beans,
        ]);
        // 实例化容器
        $this->components = new ContainerManager([
            'beans'  => $this->beans,
            'config' => $this->components,
        ]);
        // 错误注册
        \Mix\Core\Error::register();
    }

    /**
     * 获取组件
     * @param $name
     * @return ComponentInterface|ComponentDisabled
     */
    public function get($name)
    {
        $component = $this->container->get($name);
        // 前置初始化
        ComponentInitialize::before($component);
        // 停用未初始化的组件
        if ($component->getStatus() != ComponentInterface::STATUS_RUNNING) {
            return new ComponentDisabled([
                'component' => $component,
                'name'      => $name,
            ]);
        }
        // 返回组件
        return $component;
    }

    /**
     * 判断组件是否存在
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->container->has($name);
    }

    /**
     * 判断组件是否存在
     * @param $name
     * @return bool
     */
    public function isRegistered($name)
    {
        return isset($this->components[$name]) ? true : false;
    }

    /**
     * 判断组件是否在执行
     * @param $name
     * @return bool
     */
    public function isRunning($name)
    {
        if (!$this->container->has($name)) {
            return false;
        }
        $component = $this->container->get($name);
        return $component->getStatus() == ComponentInterface::STATUS_RUNNING ? true : false;
    }

    /**
     * 获取配置
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        $message   = "Config does not exist: {$name}.";
        $fragments = explode('.', $name);
        // 判断一级配置是否存在
        $first = array_shift($fragments);
        if (!isset($this->$first)) {
            throw new \Mix\Exception\ConfigException($message);
        }
        // 判断其他配置是否存在
        $current = $this->$first;
        foreach ($fragments as $key) {
            if (!isset($current[$key])) {
                throw new \Mix\Exception\ConfigException($message);
            }
            $current = $current[$key];
        }
        return $current;
    }

    /**
     * 获取配置目录路径
     * @return string
     */
    public function getConfigPath()
    {
        if (!FileSystemHelper::isAbsolute($this->configPath)) {
            if ($this->configPath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->configPath;
        }
        return $this->configPath;
    }

    /**
     * 获取运行目录路径
     * @return string
     */
    public function getRuntimePath()
    {
        if (!FileSystemHelper::isAbsolute($this->runtimePath)) {
            if ($this->runtimePath == '') {
                return $this->basePath;
            }
            return $this->basePath . DIRECTORY_SEPARATOR . $this->runtimePath;
        }
        return $this->runtimePath;
    }

}
