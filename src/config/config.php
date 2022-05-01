<?php

declare(strict_types=1);

use Hyperf\Framework\Bootstrap\FinishCallback;
use Hyperf\Framework\Bootstrap\TaskCallback;
use Hyperf\Watcher\Driver\ScanFileDriver;
use Hyperf\Server\Event;
use function Huid\Pusher\Support\storage_path;

return [
    'server' => [
        'settings' => [
            'pid_file' => BASE_PATH . '/pusher.pid',
            // Task Worker 数量，根据您的服务器配置而配置适当的数量
            'task_worker_num' => 3,
            // 因为 `Task` 主要处理无法协程化的方法，所以这里推荐设为 `false`，避免协程下出现数据混淆的情况
            'task_enable_coroutine' => false,

        ],
        'callbacks' => [
            // Task callbacks
            Event::ON_TASK => [TaskCallback::class, 'onTask'],
            Event::ON_FINISH => [FinishCallback::class, 'onFinish'],
        ],
    ],

    'watcher' => [
        'driver' => ScanFileDriver::class,
        'bin' => 'php',
        'command' => 'index.php start',
        'watch' => [
            'dir' => ['src/*'],
            'file' => ['index.php'],
            'scan_interval' => 2000,
        ],
    ],

    'apns' => [
        'key_path' => env('APNS_KEY_PATH', storage_path('c.p8')),
        'key_type' => env('APNS_KEY_TYPE', 'p8'),
        'password' => env('APNS_PASSWORD', ''),
        'sandbox' => env('APNS_SANDBOX', false),
        'key_id'  => env('APNS_KEY_ID', 'LH4T9V5U4R'),
        'team_id' => env('APNS_TEAM_ID', '5U8LBRXG3A'),
        'topic' => env('APNS_TOPIC', 'me.fin.bark'),
    ]
];
