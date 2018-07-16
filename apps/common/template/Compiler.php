<?php
namespace Apps\Common\Template;

class Compiler
{
    private $_content;
    private $_valueMap = [];
    private $_patten = [
        '#\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#',
        '#\{if (.*?)\}#',
        '#\{(else if|elseif) (.*?)\}#',
        '#\{else\}#',
        '#\{foreach \\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}#',
        '#\{\/(foreach|if)}#',
        '#\{\\^(k|v)\}#',
    ];
    private $_translation = [
        "<?php echo \$this->_valueMap['\\1']; ?>",
        '<?php if (\\1) {?>',
        '<?php } else if (\\2) {?>',
        '<?php }else {?>',
        "<?php foreach (\$this->_valueMap['\\1'] as \$k => \$v) {?>",
        '<?php }?>',
        '<?php echo \$\\1?>'
    ];
    
    /**
     * [compile 编译模板文件]
     * @param  [type] $source   [模板文件]
     * @param  [type] $destFile [编译后文件]
     * @param  [type] $values   [键值对]
     * @return [type]           [description]
     */
    public function compile($source, $destFile, $values)
    {
        $this->_content = file_get_contents($source);
        $this->_valueMap = $values;
    
        if (strpos($this->_content, '{$') !== false) {
            $this->_content = preg_replace($this->_patten, $this->_translation, $this->_content);
        }
        file_put_contents($destFile, $this->_content);
    }
}

