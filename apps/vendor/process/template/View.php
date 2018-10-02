<?php
namespace Process\Template;

class View
{
    public function __construct($file,$ViewValue)
    {
        $FilePath = VIEW_PATH.DS.'backend'.DS.$file.'.html';
        if (!is_file($FilePath)) {
            throw new \Exception('模板文件'.$file .'不存在!');
        }
        include($FilePath);
    }
}

