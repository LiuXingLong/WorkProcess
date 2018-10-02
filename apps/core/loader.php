<?php
// 定义视图路径
define('VIEW_PATH', BASE_PATH.DS.'public'.DS.'view');
// 定义静态资源路径
define('STATIC_PATH', BASE_PATH.DS.'public'.DS.'static');

// 加载配置文件
define('CONFIG', include APP_PATH.DS.'config'.DS.SYSTEM_MODE.DS.'config.php');
define('DB_CONFIG', include APP_PATH.DS.'config'.DS.SYSTEM_MODE.DS.'database.php');
define('REDIS_CONFIG', include APP_PATH.DS.'config'.DS.SYSTEM_MODE.DS.'redis.php');

//  注册给定的函数作为 __autoload 的实现
spl_autoload_register(function ($class) {
    $file = '';
    $path = explode('\\',$class);
    $cnt = count($path);
    for($i = 0; $i < $cnt ; $i++){
        if($i == 0){
            $file = (strtolower($path[$i]) != 'apps') ? strtolower($path[$i]):'';
        }elseif($i == $cnt - 1){
            $file .= DS.$path[$i].'.php';
        }else{
            $file .= DS.strtolower($path[$i]);
        }
    }  
    // 加载项目中文件
    if(file_exists(APP_PATH.DS.$file)){
        include APP_PATH.DS.$file;
    }
    // 加载vendor中文件
    if (file_exists(APP_PATH.DS.'vendor'.DS.$file)) {
        include APP_PATH.DS.'vendor'.DS.$file;
    }
});
