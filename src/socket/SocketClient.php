<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\TopphpClient\socket;

use Topphp\TopphpClient\ClientDriver;

class SocketClient extends ClientDriver
{

    private $client;
    private $coroutine = false;// 是否开启协程
    private $config = [

    ];

    public function __construct($config)
    {
    }

    protected function connect()
    {
    }

    /**
     * 返回客户端句柄对象，可执行其它高级方法
     * @param string $type 客户端名称
     * @return object
     * @author bai
     */
    public function handler($type = "socket")
    {
        return $this->client;
    }

    //*************************************** --- 助手函数 --- *******************************************//
}
