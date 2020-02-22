<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\TopphpClient;


abstract class ClientDriver
{
    /**
     * 驱动句柄
     * @var object
     * @author bai
     */
    protected $handler = false;
    protected static $errorLog;

    abstract protected function connect();

    /**
     * 返回句柄对象，可执行其它高级方法
     *
     * @param string $type 客户端名称
     * @return object
     * @author bai
     */
    abstract public function handler($type);

    /**
     * 获取异常信息
     * @return mixed
     * @author bai
     */
    public function getErrorMsg()
    {
        return self::$errorLog;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }

}