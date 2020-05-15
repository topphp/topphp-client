<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client RedisTest
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\Test;

use Topphp\TopphpClient\Client;

trait RedisTrait
{
    /**
     * 测试redis客户端句柄
     * @return object $client 返回 客户端句柄
     * @author bai
     */
    public function testRedisClient()
    {
        $errorMsg = null;
        // 下面的配置config在ThinkPHP骨架上会自动获取，不需要传
        $config = $this->GetConfig();
        // 下面是获取指定客户端句柄以便可以调用更多原生高级方法的两种方式（建议第一种）
        $client = Client::getInstance($config)->cli("redis")->handler();
        //$client   = Client::getInstance($config)->handler("redis");// 此种方式仅适用于不需要使用组件快捷方法直接获取客户端句柄的情况
        if ($client === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $client;
    }

    /**
     * 测试set方法
     * @return object $res 透传返回 句柄对象return
     * @author bai
     */
    public function testRedisSet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->set("arr", ["这是一个数组"], 120);// 有效期120s
        // 设置数据前缀写法
        //$res = Client::getInstance($config)->cli("redis")->setPrefix("topphp_")->set("arr", ["这是一个数组"], 120);
        // 这是助手类的写法（助手类仅限于TP6下使用，单元测试不适用）
        //$res = RedisHelper::set("arr", ["这是一个数组"], 120);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试get方法
     * @return mixed $res 返回 redis存储数据
     * @author bai
     */
    public function testRedisGet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->get("arr");
        // 设置数据前缀写法
        //$res   = Client::getInstance($config)->cli("redis")->setPrefix("topphp_")->get("arr");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis是否存在key
     * @return int $res 返回 0 或 1
     * @author bai
     */
    public function testRedisExists()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->exists("arr");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        $this->assertTrue($res === 1);
        return $res;
    }

    /**
     * 测试Redis向key中添集合值（如果集合中已存在该值，将不会重复添加）【无序的】
     * Tips：支持两种方式传入 1 可变参数如下 2 一维数组方式 ["val5","val6"]
     * @return int $res 返回 添加成功的条数
     * @author bai
     */
    public function testRedisSAdd()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->sAdd("members", "val1", "val2", "val3", "val4");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis删除key集合中的值
     * @return int $res 返回 成功删除的条数
     * @author bai
     */
    public function testRedisSRem()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->sRem("members", "val1", "val2");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis获取集合中的值
     * @return array $res 返回 集合数组
     * @author bai
     */
    public function testRedisSMembers()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->sMembers("members");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis删除指定key值
     * Tips：支持两种方式传入 1 可变参数如下 2 一维数组方式 ["arr","members"]
     * @return int $res 返回 删除记录条数
     * @author bai
     */
    public function testRedisDel()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->del("arr", "members");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis指定key值递增（支持设置步进/增量，不传默认 1）
     * @return int $res 返回 步进/增量后的数
     * @author bai
     */
    public function testRedisInc()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->inc("number", 3);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis指定key值递减（支持设置步进/减量，不传默认 1）
     * @return int $res 返回 步进/减量后的数
     * @author bai
     */
    public function testRedisDec()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->dec("number", 2);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis设置key过期时间（支持指定过期时间戳设置）
     * Tips：第二个time参数 1、可直接传入过期秒s数 2、也可传入到期时间戳
     * @return int $res 返回 0 失败 或 1 成功
     * @author bai
     */
    public function testRedisExpire()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->setExp("number", time() + 90);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis列表【尾部追加】
     * @return int $res 返回 添加后的列表记录数
     * @author bai
     */
    public function testRedisRPush()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->rPush("list", "now_" . time());
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis列表【尾部剔除】
     * @return int $res 返回 被剔除的元素值
     * @author bai
     */
    public function testRedisRPop()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->rPop("list");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis列表【头部追加】
     * @return int $res 返回 添加后的列表记录数
     * @author bai
     */
    public function testRedisLPush()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->lPush("list", "now_" . time());
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis列表【头部剔除】
     * @return int $res 返回 被剔除的元素值
     * @author bai
     */
    public function testRedisLPop()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->lPop("list");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis切换db库写数据
     * @return object $res 透传返回 句柄对象return
     * @author bai
     */
    public function testRedisDb()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->db(1)->set("str", "这是一个切换一库的数据", 60);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis哈希存储（支持数组）
     * @return int $res 返回 新增记录数 0 或 1
     * Tips：1、第三个值参数允许为数组['OD201910291929515008','OD201910291929515009']
     *      2、返回为 0 不代表失败，有可能键不变，但是值改变的情况
     * @author bai
     */
    public function testRedisHSet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hSet("order", 'order_sn', 'OD201910291929515013');
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis哈希获取
     * @return mixed $res 返回 句柄对象return
     * @author bai
     */
    public function testRedisHGet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hGet("order", 'order_sn');
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis哈希批量存储（支持多维数组）
     * @return mixed $res 返回 句柄对象return
     * @author bai
     */
    public function testRedisHMSet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")
            ->hMSet("order:10001", ['order_sn' => 'OD201910291929515008', 'buy_num' => 2]);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis获取批量哈希数据
     * @return mixed $res 返回 句柄对象return
     * @author bai
     */
    public function testRedisHMGet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hMGet("order:10001", ['order_sn', 'buy_num']);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis哈希key中元素个数
     * @return int $res 返回 元素个数
     * @author bai
     */
    public function testRedisHLen()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hLen("order:10001");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis删除哈希key中的某个字段
     * @return int $res 返回 0 失败(不存在该字段也会返回0) 或 1 成功
     * @author bai
     */
    public function testRedisHDel()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hDel("order:10001", 'order_sn');
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis获取指定key的全部哈希值
     * @return array $res 返回 数据值
     * @author bai
     */
    public function testRedisHGetAll()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hGetAll("order:10001");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis指定key的哈希值中是否存在field字段
     * @return int $res 返回 0 不存在 或 1 存在
     * @author bai
     */
    public function testRedisHExists()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hExists("order:10001", "buy_num");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis指定key的哈希值中field字段值自增（支持设置步进/增量，不传默认 1）
     * @return int $res 返回 自增后的值
     * @author bai
     */
    public function testRedisHInc()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hInc("order:10001", "buy_num");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis指定key的哈希值中field字段值自减（支持设置步进/减量，不传默认 1）
     * @return int $res 返回 自减后的值
     * @author bai
     */
    public function testRedisHDec()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("redis")->hDec("order:10001", "buy_num", 2);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis【执行事务】
     * @return mixed $res 执行结果--以数组形式返回了每个命令执行结果
     * @author bai
     */
    public function testRedisCommitTrans()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        // 初始化客户端
        $trans = Client::getInstance($config)->cli("redis");
        // 开启事务
        $trans->startTrans();
        // 执行上下文
        $trans->set("test1", 1, 60);
        $trans->get("test1");
        $trans->inc("test1");
        $trans->get("test1");
        // 提交事务
        $res = $trans->commit();
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis【回滚事务】
     * @return mixed $res 执行结果--test1还是原值1，并未发生变化为10
     * @author bai
     */
    public function testRedisRollbackTrans()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        // 初始化客户端
        $trans = Client::getInstance($config)->cli("redis");
        // 先设置缓存test1为1
        $trans->set("test1", 1, 60);
        // 开启事务
        $trans->startTrans();
        // 执行上下文
        $trans->set("test1", 10, 60);
        $trans->get("test1");
        $trans->inc("test1");
        $trans->get("test1");
        // 取消事务
        $trans->rollback();
        // 获取数据
        $res = $trans->get("test1");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试Redis【监视键，并执行事务】
     * @return mixed $res 执行结果--$ret commit返回false或null
     * @author bai
     */
    public function testWatchTrans()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        // 初始化客户端
        $trans = Client::getInstance($config)->cli("redis");
        // 先设置缓存test1为1
        $trans->set("test1", 1, 60);
        // 监视test1
        $trans->watchKeys(["test1"]);
        // 假设在开始监视之后，执行事务之前，test1被并发操作redis的其他用户修改了
        $trans->set("test1", 10, 60);
        // 开启事务
        $trans->startTrans();
        // 执行上下文（在这个过程中，出现上述并发情况）
        $trans->inc("test1");
        // 提交事务
        $ret = $trans->commit();
        if (!empty($ret)) {
            // 由于并发被其他用户修改了，所以无法提交当前事务,正常应该返回false或null
            $errorMsg = "如果代码跑到这里，说明监视键未生效";
        }
        // 获取数据
        $res = $trans->get("test1");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 清理单元测试的redis永久缓存数据
     * @return mixed
     * @author bai
     */
    public function testClearCache()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        Client::getInstance($config)->cli("redis")->setExp("order", time() + 60);
        $res = Client::getInstance($config)->cli("redis")->setExp("order:10001", time() + 60);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }
}
