<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client GuzzleTest
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\Test;

use Topphp\TopphpClient\Client;

trait GuzzleTrait
{
    /**
     * 测试http客户端句柄
     * @return object $client 返回 客户端句柄
     * @author bai
     */
    public function testHttpClient()
    {
        $errorMsg = null;
        // 下面的配置config在ThinkPHP骨架上会自动获取，不需要传
        $config = $this->GetConfig();
        // 下面是获取指定客户端句柄以便可以调用更多原生高级方法的两种方式（建议第一种）
        $client = Client::getInstance($config)->cli("http")->handler();
        //$client   = Client::getInstance($config)->handler("http");// 此种方式仅适用于不需要使用组件快捷方法直接获取客户端句柄的情况
        if ($client === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $client;
    }

    /**
     * 测试http客户端【GET请求】
     * @return mixed
     * @author bai
     */
    public function testHttpGet()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $res      = Client::getInstance($config)->cli("http")->get("http://www.baidu.com");
        // 这是助手类的写法（助手类仅限于TP6下使用，单元测试不适用）
        //$res = HttpHelper::get("http://www.baidu.com");
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试http客户端【POST请求】
     * @return mixed
     * @author bai
     */
    public function testHttpPost()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $url      = "http://www.baidu.com";
        $param    = [
            "id" => 1
        ];
        $res      = Client::getInstance($config)->cli("http")->post($url, $param);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试http客户端【PUT请求】
     * @return mixed
     * @author bai
     */
    public function testHttpPut()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $url      = "http://www.baidu.com";
        $param    = [
            "id" => 1
        ];
        $res      = Client::getInstance($config)->cli("http")->put($url, $param);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试http客户端【DELETE请求】
     * @return mixed
     * @author bai
     */
    public function testHttpDelete()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $url      = "http://www.baidu.com";
        $param    = [
            "id" => 1
        ];
        $res      = Client::getInstance($config)->cli("http")->delete($url, $param);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }

    /**
     * 测试http客户端【PATCH请求】
     * @return mixed
     * @author bai
     */
    public function testHttpPatch()
    {
        $errorMsg = null;
        $config   = $this->GetConfig();
        $url      = "http://www.baidu.com";
        $param    = [
            "id" => 1
        ];
        $res      = Client::getInstance($config)->cli("http")->patch($url, $param);
        if ($res === false) {
            // 错误信息通过这种方式获取
            $errorMsg = Client::getInstance()->getErrorMsg();
        }
        $this->assertTrue($errorMsg === null);
        return $res;
    }
}
