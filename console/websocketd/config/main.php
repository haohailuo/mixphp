<?php

// Console应用配置
$database = require __DIR__ . '/../../common/config/database.php';
return [

    // 基础路径
    'basePath'            => dirname(__DIR__) . DIRECTORY_SEPARATOR,

    // 控制器命名空间
    'controllerNamespace' => 'console\websocketd\command',

    // 组件默认命名空间
    'componentDefaultNamespace'  => 'console',

    // 组件配置
    'components'          => [

        // 路由
        'console\route'      => [
            // 类路径
            'class'          => 'mix\base\Route',
            // 默认变量规则
            'defaultPattern' => '[\w-]+',
            // 路由变量规则
            'patterns'       => [
                'id' => '\d+',
            ],
            // 路由规则
            'rules'          => [
            ],
        ],

        // 请求
        'console\request'    => [
            // 类路径
            'class' => 'mix\console\Request',
        ],

        // 响应
        'console\response'   => [
            // 类路径
            'class' => 'mix\console\Response',
        ],

        // 错误
        'console\error'      => [
            // 类路径
            'class' => 'mix\console\Error',
        ],

        // 日志
        'console\log'        => [
            // 类路径
            'class'       => 'mix\base\Log',
            // 日志记录级别
            'level'       => ['error', 'info', 'debug'],
            // 日志目录
            'logDir'      => 'logs',
            // 日志轮转类型
            'logRotate'   => mix\base\Log::ROTATE_DAY,
            // 最大文件尺寸
            'maxFileSize' => 2048 * 1024,
            // 换行符
            'newline'     => PHP_EOL,
        ],

        // 数据库
        'console\rdb'        => array_merge(
            $database['mysql'],
            [
                // 类路径
                'class'     => 'mix\client\Pdo',
                // 设置PDO属性: http://php.net/manual/zh/pdo.setattribute.php
                'attribute' => [
                    // 设置默认的提取模式: \PDO::FETCH_OBJ | \PDO::FETCH_ASSOC
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ],
            ]
        ),

        // redis
        'console\redis'      => array_merge(
            $database['redis'],
            [
                // 类路径
                'class' => 'mix\client\Redis',
            ]
        ),

        // 请求
        'webSocket\request'  => [
            // 类路径
            'class' => 'mix\swoole\Request',
        ],

        // 响应
        'webSocket\response' => [
            // 类路径
            'class'         => 'mix\swoole\Response',
            // 默认输出格式
            'defaultFormat' => mix\swoole\Response::FORMAT_JSON,
            // json
            'json'          => [
                // 类路径
                'class' => 'mix\web\Json',
            ],
            // jsonp
            'jsonp'         => [
                // 类路径
                'class'        => 'mix\web\Jsonp',
                // callback名称
                'callbackName' => 'callback',
            ],
            // xml
            'xml'           => [
                // 类路径
                'class' => 'mix\web\Xml',
            ],
        ],

        // Session
        'webSocket\session'  => [
            // 类路径
            'class'         => 'mix\websocket\SessionReader',
            // 保存处理者
            'saveHandler'   => array_merge(
                $database['redis'],
                [
                    // 类路径
                    'class' => 'mix\client\Redis',
                ]
            ),
            // 保存的Key前缀
            'saveKeyPrefix' => 'MIXSSID:',
            // session名
            'name'          => 'mixssid',
        ],

        // Token
        'webSocket\token'    => [
            // 类路径
            'class'         => 'mix\websocket\TokenReader',
            // 保存处理者
            'saveHandler'   => array_merge(
                $database['redis'],
                [
                    // 类路径
                    'class' => 'mix\client\Redis',
                ]
            ),
            // 保存的Key前缀
            'saveKeyPrefix' => 'MIXTKID:',
            // token键名
            'name'          => 'access_token',
        ],

    ],

    // 对象配置
    'objects'             => [

        // WebSocketServer
        'webSocketServer' => [

            // 类路径
            'class'   => 'mix\server\WebSocketServer',
            // 主机
            'host'    => 'localhost',
            // 端口
            'port'    => 9502,

            // 运行时的各项参数：https://wiki.swoole.com/wiki/page/274.html
            'setting' => [
                // 连接处理线程数
                'reactor_num' => 8,
                // 工作进程数
                'worker_num'  => 8,
                // 设置worker进程的最大任务数
                'max_request' => 10000,
                // 日志文件路径
                'log_file'    => __DIR__ . '/../runtime/logs/mix-websocketd.log',
                // 子进程运行用户
                'user'        => 'www',
            ],

        ],

    ],

];
