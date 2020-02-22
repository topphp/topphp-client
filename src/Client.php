<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\TopphpClient;


use Topphp\TopphpClient\guzzle\GuzzleClient;
use Topphp\TopphpClient\redis\RedisClient;
use Topphp\TopphpClient\socket\SocketClient;

class Client extends ClientDriver
{
    private static $instance;// 私有化对象属性
    private static $guzzleConfig;
    private static $redisConfig;
    private static $socketConfig;
    private static $type;

    /**
     * 私有化构造
     * Client constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        if (!empty($config)) {
            if (array_key_exists("Http", $config)) {
                self::$guzzleConfig = $config['Http'];
            }
            if (array_key_exists("Redis", $config)) {
                self::$redisConfig = $config['Redis'];
            }
            if (array_key_exists("Socket", $config)) {
                self::$socketConfig = $config['Socket'];
            }
        } elseif (class_exists(\think\App::class)) {
            self::$guzzleConfig = config("topphpClientHttp.Http");
            self::$redisConfig  = config("topphpClientRedis.Redis");
            self::$socketConfig = config("topphpClientSocket.Socket");
        }
    }

    /**
     * 公有化静态方法【实例化自己】
     * @param $config
     * @return bool|Client
     * @author bai
     */
    public static function getInstance($config = [])
    {
        try {
            if (!(self::$instance instanceof self)) {
                self::$instance = new self($config);
            }
            return self::$instance;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 私有化克隆
     * @author bai
     */
    private function __clone()
    {
    }

    /**
     * 获取指定客户端对象
     * @param $type
     * @return object
     * @author bai
     */
    public function cli($type)
    {
        self::$type = $type;
        return self::connect();
    }

    /**
     * 连接客户端
     * @return bool|GuzzleClient|RedisClient|SocketClient
     * @author bai
     */
    protected function connect()
    {
        try {
            if (empty(self::$type)) {
                self::$errorLog = "Client does not exist";
                return false;
            }
            $obj  = false;
            $type = strtolower(self::$type);
            switch ($type) {
                case "http":
                    if (empty(self::$guzzleConfig)) {
                        self::$errorLog = "Http config error";
                        return $obj;
                    }
                    $obj = new GuzzleClient(self::$guzzleConfig);
                    break;
                case "redis":
                    if (empty(self::$redisConfig)) {
                        self::$errorLog = "Redis config error";
                        return $obj;
                    }
                    $obj = new RedisClient(self::$redisConfig);
                    break;
                case "socket":
                    if (empty(self::$socketConfig)) {
                        self::$errorLog = "Socket config error";
                        return $obj;
                    }
                    $obj = new SocketClient(self::$socketConfig);
                    break;
                default:
                    self::$errorLog = "Client does not exist";
                    return $obj;
            }
            $client = $obj->connect();
            if ($client === false) {
                self::$errorLog = $obj->getErrorMsg();
                return false;
            }
            $this->handler = $client;
            return $obj;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 返回客户端句柄对象，可执行其它高级方法
     * @param string $type 客户端名称
     * @return object
     * @author bai
     */
    public function handler($type)
    {
        self::$type = $type;
        self::connect();
        return $this->handler;
    }
}