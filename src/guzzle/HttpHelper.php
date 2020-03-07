<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);
/**
 * Class HttpHelper
 *
 * @package GuzzleClient
 * @method mixed get(string $url, array $data, array $headers = []) static 发送 GET 请求
 * @method mixed post(string $url, array $data, string $type = 'json', array $headers = []) static 发送 POST 请求
 * @method mixed put(string $url, array $data) static 发送 PUT 请求
 * @method mixed patch(string $url, array $data) static 发送 PATCH 请求
 * @method mixed delete(string $url, array $data) static 发送 DELETE 请求
 * @method mixed handler() static 返回http客户端Guzzle原始句柄，可调用更多高级方法
 */

namespace Topphp\TopphpClient\guzzle;


use Topphp\TopphpClient\Client;

class HttpHelper
{
    private static $driver = "http";

    /**
     * GET 请求
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return mixed
     * @author bai
     */
    public static function get(string $url, array $data = [], array $headers = [])
    {
        return Client::getInstance()->cli(self::$driver)->get($url, $data, $headers);
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $data
     * @param string $type 类型：在header头为空的情况下，自动根据类型填充header头信息，包含（json，body，form_params，multipart，xml）
     * @param array $headers 可自定义头信息内容
     * @return mixed
     * @author bai
     */
    public static function post(string $url, array $data, string $type = 'json', array $headers = [])
    {
        return Client::getInstance()->cli(self::$driver)->post($url, $data, $type, $headers);
    }

    /**
     * PUT 请求
     * @param string $url
     * @param array $data
     * @return mixed
     * @author bai
     */
    public static function put(string $url, array $data)
    {
        return Client::getInstance()->cli(self::$driver)->put($url, $data);
    }

    /**
     * PATCH 请求
     * @param string $url
     * @param array $data
     * @return mixed
     * @author bai
     */
    public static function patch(string $url, array $data)
    {
        return Client::getInstance()->cli(self::$driver)->patch($url, $data);
    }

    /**
     * DELETE 请求
     * @param string $url
     * @param array $data
     * @return mixed
     * @author bai
     */
    public static function delete(string $url, array $data)
    {
        return Client::getInstance()->cli(self::$driver)->delete($url, $data);
    }

    /**
     * 返回Http-Guzzle客户端句柄（可以执行其它Guzzle高级方法）
     * @return mixed
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