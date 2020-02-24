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
 * Topphp 客户端管理工具配置--Http
 */

return [
    'Http' => [
        // 请求超时时间
        'timeout'      => 300,
        // 是否过滤html标签（避免爬取网页出现被重定向）
        'filter_html'  => true,
        // 是否开启协程客户端（协程客户端需要基于swoole扩展，swoole服务环境下必开）
        'is_coroutine' => false
    ]
];