<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\TopphpClient\guzzle;

use GuzzleHttp\Client;
use Topphp\TopphpClient\ClientDriver;

class GuzzleClient extends ClientDriver
{
    /** @var Client */
    private $client;
    private $baseUri;
    private $coroutine = false;// 是否开启协程
    private $config = [
        'timeout'      => 300,
        'is_coroutine' => false,
        'base_uri'     => ''
    ];

    /**
     * 构造配置
     * GuzzleClient constructor.
     * @param $config
     */
    public function __construct($config)
    {
        // 检查配置
        if (isset($config['is_coroutine']) && $config['is_coroutine'] === true) {
            $this->coroutine = true;
        }
        $this->config  = array_merge($this->config, $config);
        $this->baseUri = $this->config['base_uri'];
    }

    /**
     * 数组转XML
     *
     * @param $arr
     * @return string
     * @author bai
     */
    private static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 是否是json数据
     *
     * @param $str
     * @return bool
     * @author bai
     */
    private static function isJsonStr($str)
    {
        return is_string($str) && !is_null(json_decode($str));
    }

    /**
     * 返回原始数据
     *
     * @param $response
     * @return array
     * @author bai
     */
    private function returnData($response)
    {
        //获取响应状态码 如 200
        $response_code = $response->getStatusCode();
        //校验返回值编码类型并编码为utf-8
        $type          = $response->getHeader('content-type');
        $parsed        = \GuzzleHttp\Psr7\parse_header($type);
        $original_body = (string)$response->getBody()->getContents();
        $utf8_body     = mb_convert_encoding($original_body, 'UTF-8', $parsed[0]['charset'] ?? 'UTF-8');
        if (self::isJsonStr($utf8_body)) {
            $ret = \GuzzleHttp\json_decode($utf8_body, true);
        } else {
            $ret = $utf8_body;
        }
        if (is_string($ret) && $this->config['filter_html'] === true) {
            $ret = htmlspecialchars($ret);
        }
        return $ret;
    }

    /**
     * 连接Guzzle返回实例
     * @return bool|Client
     * @author bai
     */
    protected function connect()
    {
        // $options 配置参数
        $options['base_uri'] = $this->config['base_uri'];
        $options['timeout']  = $this->config['timeout'];
        $options['verify']   = false;//默认不开启验证https 如果需要https验证，则传入证书路径 例如：/full/path/to/cert.pem
        $clientHandler       = false;
        if ($this->coroutine) {
            if (!extension_loaded('swoole')) {
                self::$errorLog = "Please open the swoole extension";
                return false;
            }
            \Swoole\Coroutine::create(function () use (&$clientHandler, $options) {
                $clientHandler = new Client($options);
            });
        } else {
            $clientHandler = new Client($options);
        }
        $this->client = $clientHandler;
        return $clientHandler;
    }

    /**
     * 返回客户端句柄对象，可执行其它高级方法
     * @param string $type 客户端名称
     * @return \GuzzleHttp\Client
     * @author bai
     */
    public function handler($type = "http")
    {
        return $this->client;
    }

    //*************************************** --- 助手函数 --- *******************************************//

    /**
     * GET 请求
     *
     * @param $api
     * @param $param
     * @param array $headers
     * @return array|bool
     * @author bai
     */
    public function get($api, $param, $headers = [])
    {
        $client      = $this->client;
        $requestTime = time();
        try {
            $data     = [
                "headers" => $headers,
                "query"   => $param
            ];
            $response = $client->get($api, $data);
            return self::returnData($response);
        } catch (\Exception $e) {
            if (is_array($e->getMessage())) {
                $err = json_encode($e->getMessage());
            } else {
                $err = $e->getMessage();
            }
            if (time() > $requestTime + $this->config['timeout']) {
                self::$errorLog = "Http 请求超时";
                return false;
            }
            self::$errorLog = "Http 调用异常--" . $err;
            return false;
        }
    }

    /**
     * POST 请求
     *
     * @param $api
     * @param array $post_data
     * @param string $type
     * @param array $headers
     * @param string $cookie
     * @return array|bool
     * @author bai
     */
    public function post(
        $api,
        $post_data = [],
        $type = 'json',
        $headers = [],
        $cookie = ''
    ) {
        $client      = $this->client;
        $requestTime = time();
        try {
            if (empty($headers)) {
                switch ($type) {
                    case "json" :
                        $headers = [
                            "Content-Type" => "application/json;charset=utf-8"
                        ];
                        break;
                    case "body":
                        $post_data = json_encode($post_data, JSON_UNESCAPED_UNICODE);
                        $headers   = [
                            "Content-Type" => "application/json;charset=utf-8"
                        ];
                        break;
                    case "form_params":
                        $headers = [
                            "Content-Type" => "application/x-www-form-urlencoded;charset=utf-8"
                        ];
                        break;
                    case "multipart":
                        $headers = [
                            "Content-Type" => "multipart/form-data;charset=utf-8"
                        ];
                        break;
                    case "xml":
                        $post_data = self::ArrayToXml($post_data);
                        $headers   = [
                            "Content-Type" => "application/xml;charset=utf-8"
                        ];
                        array_push($headers, sprintf("Content-Length: %d", strlen($post_data)));
                        break;
                }
            }
            $data = [
                'headers' => $headers,
                $type     => $post_data,
                'cookies' => $cookie,
            ];

            $response = $client->post($api, $data);
            return self::returnData($response);
        } catch (\Exception $e) {
            if (is_array($e->getMessage())) {
                $err = json_encode($e->getMessage());
            } else {
                $err = $e->getMessage();
            }
            if (time() > $requestTime + $this->config['timeout']) {
                self::$errorLog = "Http 请求超时";
                return false;
            }
            self::$errorLog = "Http 调用异常--" . $err;
            return false;
        }
    }

    /**
     * PUT 请求
     *
     * @param $api
     * @param array $put_data
     * @return array|bool
     * @author bai
     */
    public function put($api, $put_data = [])
    {
        $client      = $this->client;
        $requestTime = time();
        try {
            $headers  = [
                "Content-Type" => "application/x-www-form-urlencoded;charset=utf-8"
            ];
            $data     = [
                "headers"     => $headers,
                'form_params' => $put_data,
            ];
            $response = $client->put($api, $data);
            return self::returnData($response);
        } catch (\Exception $e) {
            if (is_array($e->getMessage())) {
                $err = json_encode($e->getMessage());
            } else {
                $err = $e->getMessage();
            }
            if (time() > $requestTime + $this->config['timeout']) {
                self::$errorLog = "Http 请求超时";
                return false;
            }
            self::$errorLog = "Http 调用异常--" . $err;
            return false;
        }
    }

    /**
     * DELETE 请求
     *
     * @param $api
     * @param array $del_data
     * @return array|bool
     * @author bai
     */
    public function delete($api, $del_data = [])
    {
        $client      = $this->client;
        $requestTime = time();
        try {
            $headers  = [
                "Content-Type" => "application/x-www-form-urlencoded;charset=utf-8"
            ];
            $data     = [
                "headers"     => $headers,
                'form_params' => $del_data,
            ];
            $response = $client->delete($api, $data);
            return self::returnData($response);
        } catch (\Exception $e) {
            if (is_array($e->getMessage())) {
                $err = json_encode($e->getMessage());
            } else {
                $err = $e->getMessage();
            }
            if (time() > $requestTime + $this->config['timeout']) {
                self::$errorLog = "Http 请求超时";
                return false;
            }
            self::$errorLog = "Http 调用异常--" . $err;
            return false;
        }
    }

    /**
     * PATCH 请求
     *
     * @param $api
     * @param array $patch_data
     * @return array|bool
     * @author bai
     */
    public function patch($api, $patch_data = [])
    {
        $client      = $this->client;
        $requestTime = time();
        try {
            $headers  = [
                "Content-Type" => "application/x-www-form-urlencoded;charset=utf-8"
            ];
            $data     = [
                "headers"     => $headers,
                'form_params' => $patch_data,
            ];
            $response = $client->patch($api, $data);
            return self::returnData($response);
        } catch (\Exception $e) {
            if (is_array($e->getMessage())) {
                $err = json_encode($e->getMessage());
            } else {
                $err = $e->getMessage();
            }
            if (time() > $requestTime + $this->config['timeout']) {
                self::$errorLog = "Http 请求超时";
                return false;
            }
            self::$errorLog = "Http 调用异常--" . $err;
            return false;
        }
    }
}
