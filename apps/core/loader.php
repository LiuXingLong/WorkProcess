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
    $path = explode('\\',$class);
    $file = APP_PATH;
    $cnt = count($path);
    for($i = 1; $i < $cnt ; $i++){
        if($i == $cnt - 1){
            $file .= DS.$path[$i].'.php';
        }else{
            $file .= DS.strtolower($path[$i]);
        }
    }
    if (file_exists($file)) {
        include $file;
    }
});

/**
 * 加载文件（包括文件夹下面所有文件及文件夹）
 * @param $dir 文件夹路径
 */
function LoadDir($dir){
    if(!is_dir($dir)) return false;
    $handle = dir($dir);
    while(false !== ($filename = $handle->read())){
        if( $filename != '.' && $filename != '..'  ){
            if(is_file($dir.DS.$filename)){
                include $dir.DS.$filename;
            }else{
                LoadDir($dir.DS.$filename);
            }
        }
    }
    $handle->close();
    return true;
}

// 目录
$LoadArray = [
    'vendor',   // 加载第三方扩展
];

//加载目录
foreach($LoadArray as $load){
    LoadDir(APP_PATH.DS.$load);
}
