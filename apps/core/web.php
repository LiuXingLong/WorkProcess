<?php
// 检查控制器、方法
$_M = $_REQUEST['module'];
$_C = $_REQUEST['controller'];
$_A = $_REQUEST['action'];
$_ME = $_SERVER['REQUEST_METHOD'];
$_CA = $_C.'/'.$_A;
if(!empty($ROUTER[$_M][$_CA])){
    if($_ME != $ROUTER[$_M][$_CA]['method']){
        echo '请求的HTTP METHOD不支持'; 
    }
    $class = 'Apps\\'.ucfirst($_M).'\\Controllers\\'.$ROUTER[$_M][$_CA]['controller'];
    $action = $ROUTER[$_M][$_CA]['action'];
}else{
    $class = 'Apps\\'.ucfirst($_M).'\\Controllers\\'.ucfirst($_C).'Controller';
    $action = $_A;
}
try {
    $ref = new ReflectionClass($class);
} catch (Exception $e) {
    die("{$_C} Controller不存在");
}
try {
    $method = $ref->getMethod($action);
} catch (Exception $e) {
    die("{$action} action不存在");
}
$Controllers = new $class();
$Controllers->$action();