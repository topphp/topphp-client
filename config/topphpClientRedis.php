<?php
/**
 * 凯拓软件 [临渊羡鱼不如退而结网,凯拓与你一同成长]
 * Project: topphp-client GuzzleTest
 * Date: 2020/2/17 18:30
 * Author: bai <sleep@kaituocn.com>
 */

/**
 * Description - topphpClient.php
 *
 * Topphp 客户端管理工具配置--Redis
 */

return [
    // 客户端连接方式配置
    'Redis' => [
        // 支持多库配置（默认选择default,可以通过修改默认的connect来动态切换redis配置）
        'default_connect' => 'default',
        'default'         => [
            // 连接地址
            'host'         => '127.0.0.1',
            // 连接密码
            'auth'         => '',
            // 端口
            'port'         => 6379,
            // 选择库
            'db'           => 0,
            // 连接池配置（需要基于swoole扩展）
            'pool'         => [
                // 最小连接数
                'min_connections' => 1,
                // 最大连接数
                'max_connections' => 10,
                // 连接超时时间
                'connect_timeout' => 10.0,
                // 最大等待时间
                'wait_timeout'    => 3.0,
                // 心跳
                'heartbeat'       => -1,
                // 最大空闲连接数
                'max_idle_time'   => 60,
            ],
            // 是否开启协程客户端（协程客户端需要基于swoole扩展）
            'is_coroutine' => false,
        ]
    ]
];