<?php
namespace Process\Template;

class Template
{
    private $_config = [
        'suffix' => '.php',//文件后缀名
        'templateDir' => '../views/',//模板所在文件夹
        'compileDir' => '../runtime/cache/views/',//编译后存放的目录
        'suffixCompile' => '.php',//编译后文件后缀
        'isReCacheHtml' => false,//是否需要重新编译成静态html文件
        'isSupportPhp' => true,//是否支持php的语法
        'cacheTime' => 0,//缓存时间,单位秒
    ];
    private $_file;//带编译模板文件
    private $_valueMap = [];//键值对
    private $_compiler;//编译器
    
    public function __construct($compiler, $config = [])
    {
        $this->_compiler = $compiler;
        $this->_config = array_merge($this->_config, $config);
    }
    
    /**
     * [assign 存储控制器分配的键值]
     * @param  [type] $values [键值对集合]
     * @return [type]         [description]
     */
    public function assign($values)
    {
        if (is_array($values)) {
            $this->_valueMap = $values;
        } else {
            throw new \Exception('控制器分配给视图的值必须为数组!');
        }
        return $this;
    }
    
    /**
     * [show 展现视图]
     * @param  [type] $file [带编译缓存的文件]
     * @return [type]       [description]
     */
    public function show($file)
    {
        $this->_file = $file;
        if (!is_file($this->path())) {
            throw new \Exception('模板文件'. $file . '不存在!');
        }
    
        $compileFile = $this->_config['compileDir'] . md5($file) . $this->_config['suffixCompile'];
        $cacheFile = $this->_config['compileDir'] . md5($file) . '.html';
    
        //编译后文件不存在或者缓存时间已到期,重新编译,重新生成html静态缓存
        if (!is_file($compileFile) || $this->isRecompile($compileFile)) {
            $this->_compiler->compile($this->path(), $compileFile, $this->_valueMap);
            $this->_config['isReCacheHtml'] = true;
    
            if ($this->isSupportPhp()) {
                extract($this->_valueMap, EXTR_OVERWRITE);//从数组中将变量导入到当前的符号表
            }
    
        }
    
        if ($this->isReCacheHtml()) {
            ob_start();
            ob_clean();
            include($compileFile);
            file_put_contents($cacheFile, ob_get_contents());
            ob_end_flush();
        } else {
            readfile($cacheFile);
        }
    }
    
    /**
     * [isRecompile 根据缓存时间判断是否需要重新编译]
     * @param  [type]  $compileFile [编译后的文件]
     * @return boolean              [description]
     */
    private function isRecompile($compileFile)
    {
        return time() - filemtime($compileFile) > $this->_config['cacheTime'];
    }
    
    /**
     * [isReCacheHtml 是否需要重新缓存静态html文件]
     * @return boolean [description]
     */
    private function isReCacheHtml()
    {
        return $this->_config['isReCacheHtml'];
    }
    
    /**
     * [isSupportPhp 是否支持php语法]
     * @return boolean [description]
     */
    private function isSupportPhp()
    {
        return $this->_config['isSupportPhp'];
    }
    
    /**
     * [path 获得模板文件路径]
     * @return [type] [description]
     */
    private function path()
    {
        return $this->_config['templateDir'] . $this->_file . $this->_config['suffix'];
    }
}

