<?php
// 检查控制器、方法    index.php index@index params
if( $argc < 2 ){
   exit('NO: task@action');
}
for($i = 2; $i < $argc; $i++ ){
    $params[] = $argv[$i];
}
if(empty($ROUTER['tasks'][$argv[1]])){
    exit('Error: task@action');
}
$Index = $ROUTER['tasks'][$argv[1]];
$task = 'Apps\\Tasks\\'.$Index['task'];
$action = $Index['action'];
try {
    $ref = new ReflectionClass($task);
} catch (Exception $e) {
    die("{$Index['task']} Task 不存在");
}
try {
    $method = $ref->getMethod($action);
} catch (Exception $e) {
    die("{$action} action不存在");
}
$Tasks = new $task();
$Tasks->$action();