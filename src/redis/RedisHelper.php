<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);
/**
 * Class RedisHelper
 *
 * @package RedisClient
 * @method mixed get(string $name, mixed $default = false) static 读取name值，值不存在时返回默认值 $redis->get('k', 'normal')
 * @method mixed set(string $name, mixed $value, int $expire = null) static 设置name值 单位s $redis->set('k', 'value', 60)
 * @method bool exists(string|string[] $key) static 是否存在key $redis->exists('k')
 * @method mixed sAdd(string $key, string|array $members) static 添加集合中的值【无序】 $redis->sAdd('k', 'v1', 'v2', 'v3')
 * @method mixed sRem(string $key, string|array $members) static 删除集合中的值 $redis->sRem('k', 'v1', 'v2')
 * @method mixed sMembers(string $key) static 列出集合中的值 $redis->sMembers('k')
 * @method int del(string|string[] $key) static 删除指定key值 $redis->del('k')
 * @method int inc(string $key, int $step) static 自增值（带步进） $redis->inc('k', 3)
 * @method int dec(string $key, int $step) static 自减值（带步进） $redis->dec('k', 2)
 * @method bool expire(string $key, int $ttl) static 设置过期时间 $redis->expire('k', 90)
 * @method int|bool rPush(string $key, string $value) static 有序列表【尾部追加】 $redis->rPush('list', 'v1')
 * @method int|bool rPop(string $key) static 有序列表【尾部剔除】 $redis->rPop('list')
 * @method int|bool lPush(string $key, string $value) static 有序列表【头部追加】 $redis->rPush('list', 'v1')
 * @method int|bool lPop(string $key) static 有序列表【头部剔除】 $redis->rPop('list')
 * @method mixed db(int $dbIndex) static 选择库 $redis->db(1)
 * @method mixed hSet(string $key, string $field, string $value) static 写入哈希值 $redis->hSet('order', 'order_sn', 'OD201910291929515008')
 * @method mixed hGet(string $key, string $field) static 读取哈希值 $redis->hGet('order', 'order_sn')
 * @method mixed hMSet(string $key, array $array) static 向名称为 key 的hash中批量添加元素【批量写入哈希值】 $redis->hMSet('order:1', ['order_sn' => 'OD201910291929515008', 'buy_num' => 2])
 * @method mixed hMGet(string $key, array $array) static 返回名称为 key 的hash中field1, field2对应的值【读取批量哈希值】 $redis->hMGet('order:1', ['order_sn', 'buy_num'])
 * @method int hLen(string $key) static 返回 key 中元素个数【哈希存储的元素个数】 $redis->hLen('order:1')
 * @method int hDel(string $key, string $field) static 删除名称为 $key 的hash中键为 $field 的域【删除哈希key中的某个字段】 $redis->hDel('order:1', 'order_sn')
 * @method mixed hGetAll(string $key) static 获取指定key的全部哈希值 $redis->hGetAll('order:1')
 * @method mixed hExists(string $key, string $field) static 指定key的哈希值中是否存在field字段 $redis->hExists('order:1', 'order_sn')
 * @method int|bool hInc(string $key, string $field, int $step) static 指定key的哈希值中field字段值自增 $redis->hInc('order:1', 'buy_num', 3)
 * @method int|bool hDec(string $key, string $field, int $step) static 指定key的哈希值中field字段值自减 $redis->hDec('order:1', 'buy_num', 2)
 * @method mixed startTrans() static 开启事务 $redis->startTrans()
 * @method mixed rollback() static 取消事务 $redis->rollback()
 * @method mixed commit() static 执行事务 $redis->commit()
 * @method mixed watchKeys(array $keyArr) static 监视键，并执行事务 $redis->watchKeys(['key'])
 * @method mixed handler() static 返回redis客户端原始句柄，可调用更多redis高级方法
 */

namespace Topphp\TopphpClient\redis;


use Topphp\TopphpClient\Client;

class RedisHelper
{
    private static $driver = "redis";

    /**
     * 写入redis缓存
     * @param string $name 名称
     * @param string|array $value 值
     * @param int|null $expire 有效期s
     * @return mixed
     * @author bai
     */
    public static function set(string $name, $value, int $expire = null)
    {
        return Client::getInstance()->cli(self::$driver)->set($name, $value, $expire);
    }

    /**
     * 获取redis缓存
     * @param string $name 名称
     * @param bool $default 不存在时，返回默认值
     * @return mixed
     * @author bai
     */
    public static function get(string $name, $default = false)
    {
        return Client::getInstance()->cli(self::$driver)->get($name, $default);
    }

    /**
     * 检查key是否存在
     * @param string ...$key 支持传入多个key值，如：'k1','k2','k3'
     * @return int 返回 0 或 1
     * @author bai
     */
    public static function exists(string ...$key)
    {
        return Client::getInstance()->cli(self::$driver)->exists(...$key);
    }

    /**
     * 向key中添集合值（如果集合中已存在该值，将不会重复添加）【无序的】
     * @param string $key
     * @param mixed ...$value 支持传入多个value值，如：'v1','v2','v3'
     * @return int 返回 添加成功的条数
     * @author bai
     */
    public static function sAdd(string $key, ...$value)
    {
        return Client::getInstance()->cli(self::$driver)->sAdd($key, ...$value);
    }

    /**
     * 删除key集合中的值
     * @param string $key
     * @param mixed ...$value 支持传入多个value值，如：'v1','v2','v3'
     * @return int 返回 成功删除的条数
     * @author bai
     */
    public static function sRem(string $key, ...$value)
    {
        return Client::getInstance()->cli(self::$driver)->sRem($key, ...$value);
    }

    /**
     * 获取集合中的值
     * @param string $key
     * @return array 成功会返回数组
     * @author bai
     */
    public static function sMembers(string $key)
    {
        return Client::getInstance()->cli(self::$driver)->sMembers($key);
    }

    /**
     * 删除指定key值
     * @param string ...$key 支持传入多个key值，如：'k1','k2','k3'
     * @return int 返回 删除记录条数
     * @author bai
     */
    public static function del(string ...$key)
    {
        return Client::getInstance()->cli(self::$driver)->del(...$key);
    }

    /**
     * 指定key值递增
     * @param string $key
     * @param int $step 设置步进/增量，不传默认 1
     * @return int 返回 步进/增量后的数
     * @author bai
     */
    public static function inc(string $key, int $step = 1)
    {
        return Client::getInstance()->cli(self::$driver)->inc($key, $step);
    }

    /**
     * 指定key值递减
     * @param string $key
     * @param int $step 设置步进/减量，不传默认 1
     * @return int 返回 步进/减量后的数
     * @author bai
     */
    public static function dec(string $key, int $step = 1)
    {
        return Client::getInstance()->cli(self::$driver)->dec($key, $step);
    }

    /**
     * 设置key过期时间（支持指定过期时间戳设置）
     * @param string $key
     * @param int $time 1、可直接传入过期秒s数 2、也可传入到期时间戳
     * @return mixed
     * @author bai
     */
    public static function setExp(string $key, int $time = 0)
    {
        return Client::getInstance()->cli(self::$driver)->setExp($key, $time);
    }

    /**
     * 列表【尾部追加】
     * @param string $key
     * @param string ...$value 支持传入多个value值，如：'v1','v2','v3'
     * @return int 返回 添加后的列表记录数
     * @author bai
     */
    public static function rPush(string $key, string ...$value)
    {
        return Client::getInstance()->cli(self::$driver)->rPush($key, ...$value);
    }

    /**
     * 列表【尾部剔除】
     * @param string $key
     * @return mixed 返回 被剔除的元素值
     * @author bai
     */
    public static function rPop(string $key)
    {
        return Client::getInstance()->cli(self::$driver)->rPop($key);
    }

    /**
     * 列表【头部追加】
     * @param string $key
     * @param string ...$value 支持传入多个value值，如：'v1','v2','v3'
     * @return int 返回 添加后的列表记录数
     * @author bai
     */
    public static function lPush(string $key, string ...$value)
    {
        return Client::getInstance()->cli(self::$driver)->lPush($key, ...$value);
    }

    /**
     * 列表【头部剔除】
     * @param string $key
     * @return mixed 返回 被剔除的元素值
     * @author bai
     */
    public static function lPop(string $key)
    {
        return Client::getInstance()->cli(self::$driver)->lPop($key);
    }

    /**
     * 切换db库写数据
     * @param int $dbIndex
     * @return mixed 返回 redis客户端（可链式调用其他方法）
     * @author bai
     */
    public static function db(int $dbIndex)
    {
        return Client::getInstance()->cli(self::$driver)->db($dbIndex);
    }

    /**
     * 哈希存储（支持数组）
     * Tips：返回为 0 不代表失败，有可能键不变，但是值改变的情况
     * @param string $key
     * @param string $field
     * @param string|array $value 参数允许为数组['v1','v2']
     * @return int 返回 新增记录数 0 或 1
     * @author bai
     */
    public static function hSet(string $key, string $field, $value)
    {
        return Client::getInstance()->cli(self::$driver)->hSet($key, $field, $value);
    }

    /**
     * 哈希获取
     * @param string $key
     * @param string $field
     * @return string|array 返回数据
     * @author bai
     */
    public static function hGet(string $key, string $field)
    {
        return Client::getInstance()->cli(self::$driver)->hGet($key, $field);
    }

    /**
     * 哈希批量存储（支持多维数组）
     * @param string $key
     * @param array $data
     * @return mixed
     * @author bai
     */
    public static function hMSet(string $key, array $data)
    {
        return Client::getInstance()->cli(self::$driver)->hMSet($key, $data);
    }

    /**
     * 获取批量哈希数据
     * @param string $key
     * @param array $fieldData 字段数组
     * @return mixed
     * @author bai
     */
    public static function hMGet(string $key, array $fieldData)
    {
        return Client::getInstance()->cli(self::$driver)->hMGet($key, $fieldData);
    }

    /**
     * 哈希key中元素个数
     * @param string $key
     * @return int
     * @author bai
     */
    public static function hLen(string $key)
    {
        return Client::getInstance()->cli(self::$driver)->hLen($key);
    }

    /**
     * 删除哈希key中的某个字段
     * @param string $key
     * @param string $field
     * @return int 返回 0 失败(不存在该字段也会返回0) 或 1 成功
     * @author bai
     */
    public static function hDel(string $key, string $field)
    {
        return Client::getInstance()->cli(self::$driver)->hDel($key, $field);
    }

    /**
     * 获取指定key的全部哈希值
     * @param string $key
     * @return mixed 返回数据
     * @author bai
     */
    public static function hGetAll(string $key)
    {
        return Client::getInstance()->cli(self::$driver)->hGetAll($key);
    }

    /**
     * 指定key的哈希值中是否存在field字段
     * @param string $key
     * @param string $field
     * @return int 返回 0 不存在 或 1 存在
     * @author bai
     */
    public static function hExists(string $key, string $field)
    {
        return Client::getInstance()->cli(self::$driver)->hExists($key, $field);
    }

    /**
     * 指定key的哈希值中field字段值自增
     * @param string $key
     * @param string $field
     * @param int $step 支持设置步进/增量，不传默认 1
     * @return int 返回 自增后的值
     * @author bai
     */
    public static function hInc(string $key, string $field, int $step = 1)
    {
        return Client::getInstance()->cli(self::$driver)->hInc($key, $field, $step);
    }

    /**
     * 指定key的哈希值中field字段值自减
     * @param string $key
     * @param string $field
     * @param int $step 支持设置步进/减量，不传默认 1
     * @return int 返回 自减后的值
     * @author bai
     */
    public static function hDec(string $key, string $field, int $step = 1)
    {
        return Client::getInstance()->cli(self::$driver)->hDec($key, $field, $step);
    }

    /**
     * 开启redis事务
     * @return mixed 返回 redis客户端（可以调取其他方法，详细使用示例参看单元测试）
     * @author bai
     */
    public static function startTrans()
    {
        return Client::getInstance()->cli(self::$driver)->startTrans();
    }

    /**
     * redis回滚事务
     * @return mixed 返回 redis客户端（可以调取其他方法，详细使用示例参看单元测试）
     * @author bai
     */
    public static function rollback()
    {
        return Client::getInstance()->cli(self::$driver)->rollback();
    }

    /**
     * redis提交事务
     * @return mixed 返回 事务中所有redis上下文执行情况（不可以再调取其他redis客户端方法，详细使用示例参看单元测试）
     * @author bai
     */
    public static function commit()
    {
        return Client::getInstance()->cli(self::$driver)->commit();
    }

    /**
     * 监视键
     * @param array $keyArray
     * @return mixed 返回 redis客户端（可以调取其他方法，详细使用示例参看单元测试）
     * @author bai
     */
    public static function watchKeys(array $keyArray)
    {
        return Client::getInstance()->cli(self::$driver)->watchKeys($keyArray);
    }

    /**
     * 返回Redis客户端句柄（可以执行其它Redis高级方法）
     * @return \Redis
     * @author bai
     */
    public static function handler()
    {
        return Client::getInstance()->cli(self::$driver)->handler();
    }

    /**
     * 获取客户端端内部错误信息
     * @return mixed
     * @author bai
     */
    public static function getErrorMsg()
    {
        return Client::getInstance()->cli(self::$driver)->getErrorMsg();
    }

    public static function __callStatic($name, $arguments)
    {
        return Client::getInstance()->cli(self::$driver)->$name(...$arguments);
    }
}