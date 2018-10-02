<?php
namespace Process\Models;
use Process\Models\Model;

class BaseModel
{
    private $DB_OBJ;    // Model 实体对象
    private $TableName; // Model 当前操作表,必须设置私有
    
    // 初始化函数
    public function __construct($dbConfig = false)
    {
        $this->DB_OBJ = Model::getInstance($dbConfig);
        $this->TableName = $this->setSource();
    }
    
    // 处理非公有函数
    public function __call($method, $arguments){
        $parameters = !empty($arguments[0])?$arguments[0]:null;
        $this->DB_OBJ->setSource($this->TableName);
        return $this->DB_OBJ->$method($parameters);
    }
    
    // 获取当前基础Model表名,子类可重载此方法来设置表名
    public function setSource(){
        if(!empty($this->TableName)){
            return $this->TableName;
        }
        $table = explode('\\',get_class($this));
        $name = $table[count($table)-1];
        $tableName = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$name);
        return substr($tableName, 1 , -6);
    }
    
    /**
     * 插入数据
     * @param $inset 插入数据array(key => val)
     * @return string|boolean
     */
    final private function insert($inset = []){}
    
    /**
     * 删除数据
     * @param  $parameters   // array('conditions' => '', 'bind' => array(key => val),'group' => '', 'order' => '', 'limit' => '', 'offset' => '', 'for_update' => '', 'shared_lock' => '' )
     * @return string|number //删除影响条数
     */
    final private function delete($parameters=null){}
    
    /**
     * 更新数据
     * @param $parameters    // array( 'set' => array(key => val), 'conditions' => '', 'bind' => array(key => val),'group' => '', 'order' => '', 'limit' => '', 'offset' => '', 'for_update' => '', 'shared_lock' => '')
     * @return string|number //更新影响条数
     */
    final private function update($parameters=null){}
    
    /**
     * 查询全部数据 
     * @param $parameters  // array('columns' => '', 'conditions' => '', 'bind' => array(key => val), 'group' => '', 'order' => '', 'limit' => '', 'offset' => '', 'for_update' => '', 'shared_lock' => '' )
     * @return string|mixed
     */
    final private function find($parameters=null){}
    
    /**
     * 查询一条数据
     * @param $parameters   // array('columns' => '', 'conditions' => '', 'bind' => array(key => val), 'group' => '' ,'order' => '', 'limit' => '', 'offset' => '', 'for_update' => '', 'shared_lock' => '')
     * @return string|mixed
     */
    final private function findFirst($parameters=null){}
    
    /**
     * 查询数据条数
     * @param $parameters  // array('conditions' => '', 'bind' => array(key => val), 'group'=> '', 'order' => '', 'limit' => '' , 'offset' => '', 'for_update' => '', 'shared_lock' => '')
     * @return string|mixed
     */
    final private function count($parameters=null){}
    
    /**
     * SQL查询
     * @param $sql
     * @param $bind array(,,,)
     * @return string|boolean|number
     */
    final public function query($sql,$bind){
        return $this->DB_OBJ->query($sql,$bind);
    }
    
    /**
     * 开启事务
     */
    final public function begin(){
        $this->DB_OBJ->begin();
    }
    
    /**
     * 事务提交
     */
    final public function commit(){
        $this->DB_OBJ->commit();
    }
    
    /**
     * 事务回滚
     */
    final public function rollback(){
        $this->DB_OBJ->rollback();
    }
    
    /**
     *输出SQL
     */
    final public function echoSQL(){
        return $this->DB_OBJ->echoSQL();
    }
    
    /**
     *获取PDO连接
     */
    final public function getDB(){
        return $this->DB_OBJ->getDB();
    }
    
    /**
     * $_instance model_obj 信息
     */
    final public function instanceInfo($all = false){
        if($all === false){
            return $this->DB_OBJ;
        }else{
            return $this->DB_OBJ->instanceInfo();
        }
    }
}