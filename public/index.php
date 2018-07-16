<?php
date_default_timezone_set('Asia/Shanghai');
define('DS',DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . DS .'apps');
if (file_exists(APP_PATH . '/config/prod/config.php')) {
    define('SYSTEM_MODE', 'prod'); // dev开发模式, prod生产模式
    error_reporting(0);
}
if (!defined('SYSTEM_MODE')) {
    define('SYSTEM_MODE', 'dev');
    error_reporting(E_ALL);
}
define('CLI', php_sapi_name() == 'cli' ? TRUE : FALSE);
try {
    include APP_PATH.DS.'core'.DS.'loader.php';
    $ROUTER = include APP_PATH.DS.'core'.DS.'router.php';// 加载路由配置
    if(CLI){
        include APP_PATH.DS.'core'.DS.'cli.php';// CLI 处理请求
    }else {
        define('PARAMS', $_REQUEST);        
        $URL = explode('/',$_REQUEST['_url']);        
        $_REQUEST['module'] = !empty($URL[1])? $URL[1] : 'backend';
        if($_REQUEST['module'] == 'api'){
            $_REQUEST['controller'] = !empty($URL[2])? $URL[2] : '';
            $_REQUEST['action'] = !empty($URL[3])? $URL[3] : '';
        }elseif($_REQUEST['module'] == 'backend'){
            $_REQUEST['controller'] = !empty($URL[2])? $URL[2] : 'index';
            $_REQUEST['action'] = !empty($URL[3])? $URL[3] : 'index';
        }else{
            die('模块不存在');
        }
        if( (empty($URL[1]) || empty($URL[2]) || empty($URL[3])) && $_SERVER['REQUEST_METHOD'] == 'GET' && $_REQUEST['module'] == 'backend'){
            $LOCATION = 'http://'.$_SERVER['HTTP_HOST'].'/'.$_REQUEST['module'].'/'.$_REQUEST['controller'].'/'.$_REQUEST['action'];
            header('location: '.$LOCATION);
        }
        include APP_PATH.DS.'core'.DS.'web.php'; // WEB 处理请求
    }
    //var_dump(get_included_files());
} catch (Exception $e) {
    echo $e->getMessage();
}