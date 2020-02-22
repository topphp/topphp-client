<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */
declare(strict_types=1);

namespace Topphp\Test;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    use GuzzleTrait;
    use RedisTrait;
    use SocketTrait;

    protected function GetConfig()
    {
        $config = [];
        if (class_exists(\think\App::class)) {
            $app              = new \think\App();
            $http             = $app->http;
            $response         = $http->run();
            $config['Http'] = $app->config->get("topphpClientHttp");
            $config['Redis']  = $app->config->get("topphpClientRedis");
            $config['Socket'] = $app->config->get("topphpClientSocket");
        } else {
            $configDir        = dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR;
            $config['Http'] = include $configDir . "topphpClientHttp.php";
            $config['Redis']  = include $configDir . "topphpClientRedis.php";
            $config['Socket'] = include $configDir . "topphpClientSocket.php";
        }
        return $config;
    }
}
