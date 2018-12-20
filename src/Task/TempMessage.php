<?php

namespace Mix\Task;

use Mix\Helpers\RandomStringHelper;

/**
 * 临时消息类
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class TempMessage
{

    // 文件
    public $file;

    // 构造
    public function __construct($content, $saveDir)
    {
        $this->setFile($saveDir);
        $this->save($content);
    }

    // 获取内容
    public function getContent()
    {
        $content = file_get_contents($this->file);
        unlink($this->file);
        return $content;
    }

    // 设置文件
    protected function setFile($saveDir)
    {
        $this->file = $saveDir . DIRECTORY_SEPARATOR . self::generateFileName();
    }

    // 生成文件名称
    protected static function generateFileName()
    {
        return RandomStringHelper::randomAlphanumeric(32);
    }

    // 保存
    protected function save($content)
    {
        $bytes = file_put_contents($this->file, $content);
        return $bytes === false ? false : true;
    }

}
