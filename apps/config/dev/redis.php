<?php
return [
    'host' => '192.168.143.210', // 地址
    'port' => 6379,        // 端口
    'password' => '',      // 密码
    'prefix' => 'process_', // 前缀
    'persistent' => false, // 是否长链接
    'expire' => '300',     // 缓存时间（秒）
    'timeout' => false,    // 服务器连接限制时间 (秒) ,false不设置超时
];