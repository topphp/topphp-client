# topphp-client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

>这是一个客户端管理工具，包含Http请求客户端Guzzle，Redis客户端，Socket客户端等

## 包含方法

 - Http客户端请求 GET POST PUT PATCH DELETE
 - Redis客户端 普通set get存取，list存取，哈希存取等

## 组件结构


```
config/     
src/     
tests/
vendor/
```


## 安装

安装组件

``` bash
    composer require topphp/topphp-client
```

## 用法

```php
    全客户端命名空间引用：
        use Topphp\TopphpClient\Client;
    Redis客户端助手类命名空间引用：
        use Topphp\TopphpClient\redis\RedisHelper;
    Http客户端助手类命名空间引用：
        use Topphp\TopphpClient\guzzle\HttpHelper;
    调用方式有两种：
        1、通过原始客户端单例调用指定的客户端
            Client::getInstance()->cli("redis")->set("arr", ["这是一个数组"], 90);
        2、通过助手类直接调用【推荐】
            RedisHelper::set("arr", ["这是一个数组"], 90);
            HttpHelper::get("https://www.baidu.com");
    注意事项：
        1、写好对应的配置后，在骨架即可直接调用，无需传入配置，需要动态切换配置时，只需要通过骨架的config()方法动态修改即可。
        2、还提供一种直接传入配置的方式
            Client::getInstance($config)->cli("http")->get("https://www.baidu.com");
        3、组价同时也提供直接暴露客户端对象句柄的方式直接调用原生客户端对象，完成更多高级操作
            Client::getInstance()->cli("redis")->handler()->publish( $channel, $message );
            RedisHelper::handler()->publish( $channel, $message );
        4、默认的助手类已经提供了大部分常用场景所需的方法，更多用法可以参看单元测试文件和对应的官方文档
```

## 修改日志

有关最近更改的内容的详细信息，请参阅更改日志（[CHANGELOG](CHANGELOG.md)）。

## 测试

``` bash
    ./vendor/bin/phpunit tests/ClientTest.php
```

## 贡献

详情请参阅贡献（[CONTRIBUTING](CONTRIBUTING.md)）和行为准则（[CODE_OF_CONDUCT](CODE_OF_CONDUCT.md)）。


## 安全

如果您发现任何与安全相关的问题，请发送电子邮件至sleep@kaitoocn.com，而不要使用问题跟踪器。

## 信用

- [topphp][link-author]
- [All Contributors][link-contributors]

## 许可证

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/topphp/component-builder.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/topphp/component-builder/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/topphp/component-builder.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/topphp/component-builder.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/topphp/component-builder.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/topphp/component-builder
[link-travis]: https://travis-ci.org/topphp/component-builder
[link-scrutinizer]: https://scrutinizer-ci.com/g/topphp/component-builder/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/topphp/component-builder
[link-downloads]: https://packagist.org/packages/topphp/component-builder
[link-author]: https://github.com/topphp
[link-contributors]: ../../contributors
