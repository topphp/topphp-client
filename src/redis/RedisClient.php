<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\TopphpClient\redis;

use Topphp\TopphpClient\ClientDriver;

class RedisClient extends ClientDriver
{
    /** @var \Redis|\Predis\Client */
    private $client;
    private $serializePrefix = "";
    private $coroutine = false;// 是否开启协程redis
    private $config = [
        'host'         => '127.0.0.1',
        'auth'         => '',
        'port'         => 6379,
        'db'           => 0,
        'pool'         => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout'    => 3.0,
            'heartbeat'       => -1,
            'max_idle_time'   => 60,
        ],
        'is_coroutine' => false,
        'persistent'   => false,// 是否开启持久连接（有连接池了，所以此项可不用配）
        'timeout'      => 0,// 客户端连接redis服务器时，当闲置的时间为多少秒时，关闭连接
    ];

    /**
     * 构造配置
     * RedisClient constructor.
     * @param $config
     */
    public function __construct($config)
    {
        // 检查配置
        if (!empty($config['default_connect'])) {
            $defConnect = $config['default_connect'];
            if (!empty($config[$defConnect])) {
                $config = $config[$defConnect];
                if (isset($config['is_coroutine']) && $config['is_coroutine'] === true) {
                    $this->coroutine = true;
                }
            } elseif (!empty($config['default'])) {
                $config = $config['default'];
                if (isset($config['is_coroutine']) && $config['is_coroutine'] === true) {
                    $this->coroutine = true;
                }
            }
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 获取有效期
     * @param $expire
     * @return int
     * @author bai
     */
    private function getExpireTime($expire)
    {
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        return $expire;
    }

    /**
     * 是否是时间戳
     * @param $str
     * @return bool
     * @author bai
     */
    private function isTimestamp($str)
    {
        return ctype_digit($str) && $str <= 2147483647;
    }

    /**
     * 序列化数据
     * @param $data
     * @return string
     * @author bai
     */
    private function serialize($data)
    {
        if (is_scalar($data)) {
            return $data;
        }
        if (!empty($this->serializePrefix)) {
            return $this->serializePrefix . serialize($data);
        }
        return serialize($data);
    }

    /**
     * 反序列化数据
     * @param $data
     * @return mixed
     * @author bai
     */
    private function unserialize($data)
    {
        if (!empty($this->serializePrefix) && 0 === strpos($data, $this->serializePrefix)) {
            if (!$this->isSerialized($data)) {
                return $data;
            }
            $pos = strlen($this->serializePrefix);
            return unserialize(substr($data, $pos));
        } elseif ($this->isSerialized($data)) {
            return unserialize($data);
        } else {
            return $data;
        }
    }

    /**
     * 是否是序列化数据
     * @param $data
     * @return bool
     * @author bai
     */
    private function isSerialized($data)
    {
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }
        switch ($badions[1]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * 连接Redis返回实例
     * @return $this|bool
     * @author bai
     */
    protected function connect()
    {
        try {
            $redis = false;
            if ($this->coroutine) {
                if (!extension_loaded('swoole')) {
                    self::$errorLog = "Please open the swoole extension";
                    return false;
                }
            }
            if (extension_loaded('redis')) {
                $redis = new \Redis;

                if ($this->config['persistent']) {
                    $redis->pconnect($this->config['host'], $this->config['port'],
                        $this->config['timeout'], 'persistent_id_' . $this->config['select']);
                } else {
                    $redis->connect($this->config['host'], $this->config['port'],
                        $this->config['timeout']);
                }

                if ('' != $this->config['auth']) {
                    $redis->auth($this->config['auth']);
                }

                if (0 != $this->config['db']) {
                    $redis->select($this->config['db']);
                }
            } elseif (class_exists('\Predis\Client')) {
                $params                 = [];
                $this->config['select'] = $this->config['db'];
                foreach ($this->config as $key => $val) {
                    if (in_array($key, [
                        'aggregate',
                        'cluster',
                        'connections',
                        'exceptions',
                        'prefix',
                        'profile',
                        'replication',
                        'parameters',
                        'db',
                        'is_coroutine',
                        'pool',
                        'persistent'
                    ])) {
                        $params[$key] = $val;
                        unset($this->config[$key]);
                    }
                }
                if ('' == $this->config['auth']) {
                    unset($this->config['auth']);
                } else {
                    $this->config['password'] = $this->config['auth'];
                    unset($this->config['auth']);
                }
                $redis = new \Predis\Client($this->config, $params);
            }
            $this->client = $redis;
            return $redis;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 返回客户端句柄对象，可执行其它高级方法
     * @param string $type 客户端名称
     * @return \Redis|\Predis\Client
     * @author bai
     */
    public function handler($type = "redis")
    {
        return $this->client;
    }

    //*************************************** --- 助手函数 --- *******************************************//

    /**
     * 设置序列化前缀
     * @param string $prefix
     * @return $this
     * @author bai
     */
    public function setPrefix($prefix = "topphp_")
    {
        $this->serializePrefix = $prefix;
        return $this;
    }

    /**
     * 写入key-value
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer|\DateTime $expire 有效时间（秒）
     * @return bool|mixed
     * @author bai
     */
    public function set($name, $value, $expire = null)
    {
        try {
            $expire = $this->getExpireTime($expire);
            $value  = $this->serialize($value);
            if ($expire) {
                $result = $this->client->setex($name, $expire, $value);
            } else {
                $result = $this->client->set($name, $value);
            }
            return $result;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取指定key的值
     * @param string $name redis key
     * @param bool $default 没有获取到返回设置的默认值
     * @return bool|mixed
     * @author bai
     */
    public function get($name, $default = false)
    {
        try {
            $value = $this->client->get($name);
            if (is_null($value) || false === $value) {
                return $default;
            }
            return $this->unserialize($value);
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 是否存在key
     * @param mixed ...$key 两种方式：1 可变参数如【"v1","v2"】 2 一维数组方式【["v3","v4"]】
     * @return bool
     * @author bai
     */
    public function exists(...$key)
    {
        try {
            $exist = $this->client->exists(...$key);
            return $exist;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 向key中添集合值（如果集合中已存在该值，将不会重复添加）【无序的】
     * Tips：支持两种方式传入 1 可变参数如下 2 一维数组方式 ["v3","v4"]
     * @param string $key
     * @param mixed ...$value
     * @return bool|int
     * @author bai
     */
    public function sAdd($key, ...$value)
    {
        try {
            $membersInt = $this->client->sAdd($key, ...$value);
            return $membersInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除key集合中的值
     * Tips：支持两种方式传入 1 可变参数如下 2 一维数组方式 ["v3","v4"]
     * @param string $key
     * @param mixed ...$value
     * @return bool|int
     * @author bai
     */
    public function sRem($key, ...$value)
    {
        try {
            $membersInt = $this->client->sRem($key, ...$value);
            return $membersInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 列出集合中的值
     * @param string $key
     * @return bool|array
     * @author bai
     */
    public function sMembers($key)
    {
        try {
            $members = $this->client->sMembers($key);
            return $members;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除指定key值
     * @param mixed $key 两种方式：1 可变参数如【"v1","v2"】 2 一维数组方式【["v3","v4"]】
     * @return bool
     * @author bai
     */
    public function del(...$key)
    {
        try {
            $delInt = $this->client->del(...$key);
            return $delInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 指定key的值递增（支持步进/增量数）
     * @param string $key
     * @param int $step 步进数/增量数
     * @return bool|int
     * @author bai
     */
    public function inc($key, $step = 1)
    {
        try {
            $number = $this->client->incrby($key, (int)$step);
            return $number;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 指定key的值递减（支持步进/增量数）
     * @param string $key
     * @param int $step 步进数/增量数
     * @return bool|int
     * @author bai
     */
    public function dec($key, $step = 1)
    {
        try {
            $number = $this->client->decrby($key, (int)$step);
            return $number;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 设置key的过期时间（支持时间戳设置）
     * @param string $key
     * @param int $time 过期秒s数 或 过期时间戳
     * @return bool
     * @author bai
     */
    public function setExp($key, $time = 0)
    {
        try {
            if ($this->isTimestamp($time)) {
                $ttl = $time - time() > 0 ? $time - time() : 0;
            } else {
                $ttl = (int)$time;
            }
            $exp = $this->client->expire($key, $ttl);
            return $exp;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 列表【尾部追加】
     * @param string $key
     * @param mixed ...$value 可变参数如【"v1","v2"】但不支持数组
     * @return bool|int 当前记录数
     * @author bai
     */
    public function rPush($key, ...$value)
    {
        try {
            $rPushInt = $this->client->rPush($key, ...$value);
            return $rPushInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 列表【尾部剔除】
     * @param string $key
     * @return mixed 被剔除的元素值
     * @author bai
     */
    public function rPop($key)
    {
        try {
            $rPopMixed = $this->client->rPop($key);
            return $rPopMixed;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 列表【头部追加】
     * @param string $key
     * @param mixed ...$value 可变参数如【"v1","v2"】但不支持数组
     * @return bool|int 当前记录数
     * @author bai
     */
    public function lPush($key, ...$value)
    {
        try {
            $lPushInt = $this->client->lPush($key, ...$value);
            return $lPushInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 列表【头部剔除】
     * @param string $key
     * @return mixed 被剔除的元素值
     * @author bai
     */
    public function lPop($key)
    {
        try {
            $lPopMixed = $this->client->lPop($key);
            return $lPopMixed;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 切换db库
     * @param int $dbIndex
     * @return $this|bool
     * @author bai
     */
    public function db($dbIndex)
    {
        try {
            $this->client->select($dbIndex);
            return $this;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 哈希存储（value支持数组）
     * @param string $key
     * @param string $field
     * @param string|array $value
     * @return bool|int 返回 新增记录数 0 或 1，为 0 不代表失败，有可能$field不变，但是$value改变的情况
     * @author bai
     */
    public function hSet($key, $field, $value)
    {
        try {
            if (is_array($value)) {
                $value = $this->serialize($value);
            }
            $hSetInt = $this->client->hSet($key, $field, $value);
            return $hSetInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 哈希获取
     * @param string $key
     * @param string $field
     * @return bool|mixed 返回哈希存储的值
     * @author bai
     */
    public function hGet($key, $field)
    {
        try {
            $hGetMixed = $this->client->hGet($key, $field);
            $hGetMixed = $this->unserialize($hGetMixed);
            return $hGetMixed;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 哈希批量存储（$data支持多维数组）
     * @param string $key
     * @param array $data
     * @return bool|mixed
     * @author bai
     */
    public function hMSet($key, $data)
    {
        try {
            foreach ($data as $k => &$item) {
                if (is_array($item)) {
                    $item = $this->serialize($item);
                }
            }
            $hMSetInt = $this->client->hMSet($key, $data);
            return $hMSetInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取批量哈希数据
     * @param string $key
     * @param array $fieldData 要获取的字段数组
     * @return bool|array
     * @author bai
     */
    public function hMGet($key, $fieldData)
    {
        try {
            $hMGetMixed = $this->client->hMGet($key, $fieldData);
            if (is_array($hMGetMixed)) {
                foreach ($hMGetMixed as $k => &$item) {
                    $item = $this->unserialize($item);
                }
            }
            return $hMGetMixed;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取指定哈希key中元素个数
     * @param string $key
     * @return bool|int
     * @author bai
     */
    public function hLen($key)
    {
        try {
            $hLenInt = $this->client->hLen($key);
            return $hLenInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除哈希key中的某个字段
     * @param string $key
     * @param string $field
     * @return bool
     * @author bai
     */
    public function hDel($key, $field)
    {
        try {
            $hDelInt = $this->client->hDel($key, $field);
            return $hDelInt;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取指定key的全部哈希值
     * @param string $key
     * @return bool|array
     * @author bai
     */
    public function hGetAll($key)
    {
        try {
            $hGetAllMixed = $this->client->hGetAll($key);
            if (is_array($hGetAllMixed)) {
                foreach ($hGetAllMixed as $k => &$item) {
                    $item = $this->unserialize($item);
                }
            }
            return $hGetAllMixed;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 指定key的哈希值中是否存在field字段
     * @param string $key
     * @param string $field
     * @return bool
     * @author bai
     */
    public function hExists($key, $field)
    {
        try {
            $hExists = $this->client->hExists($key, $field);
            return $hExists;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 指定key的哈希值中field字段值自增
     * @param string $key
     * @param string $field
     * @param int $step
     * @return bool
     * @author bai
     */
    public function hInc($key, $field, $step = 1)
    {
        try {
            $hInc = $this->client->hIncrBy($key, $field, $step);
            return $hInc;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 指定key的哈希值中field字段值自减
     * @param string $key
     * @param string $field
     * @param int $step
     * @return bool
     * @author bai
     */
    public function hDec($key, $field, $step = 1)
    {
        try {
            $hDec = $this->client->hIncrBy($key, $field, -(int)$step);
            return $hDec;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 开启事务
     * @return $this|bool
     * @author bai
     */
    public function startTrans()
    {
        try {
            $this->client->multi();
            return $this;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 取消事务
     * @return $this|bool
     * @author bai
     */
    public function rollback()
    {
        try {
            $this->client->discard();
            return $this;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 执行事务
     * @return bool|array
     * @author bai
     */
    public function commit()
    {
        try {
            $ret = $this->client->exec();
            return $ret;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }

    /**
     * 监视键，并执行事务
     * @param array $keyArray
     * @return $this|bool
     * @author bai
     */
    public function watchKeys($keyArray)
    {
        try {
            $this->client->watch($keyArray);
            return $this;
        } catch (\Exception $e) {
            self::$errorLog = $e->getMessage();
            return false;
        }
    }
}
