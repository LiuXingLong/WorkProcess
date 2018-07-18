<?php
namespace Apps\Common\Models;

use Phalcon\Forms\Element\Select;

class Model
{
    protected $dsn;
    protected $dbh;
    protected $_tablePrefix;
    protected $_tableName;
    protected static $_instance = null;

    /**
     * 防止克隆
     */
    private function __clone() {}
    private function __construct($db_config)
    {
        $this->_tablePrefix = $db_config['tablePrefix'];
        $this->dsn = "{$db_config['adapter']}:dbname={$db_config['dbname']};host={$db_config['host']};port={$db_config['port']};charset={$db_config['charset']}";
        try {
            $this->dbh = new \PDO($this->dsn, $db_config['username'], $db_config['password']);
        } catch (\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }   
    /**
     * Singleton instance
     * @return Object
     */
    public static function getInstance($db_config)
    {
        $key = $db_config['host'].':'.$db_config['port'].':'.$db_config['dbname'];
        if ( empty(self::$_instance[$key]) ){
            self::$_instance[$key] = new self($db_config);
        }
        return self::$_instance[$key];
    }
    /**
     * 设置当前表明
     */
    public function setSource($table)
    {
        $this->_tableName = $table;
    }
    /**
     *获取PDO连接
     */
    public function getDB()
    {
        return $this->dbh;
    }
    /**
     * 封装where语句
     * @param $parameters
     * @return string
     */
    private function getWhere($parameters=null)
    {
        $where = '';
        if(!empty($parameters['conditions'])){
            $conditions = strtr($parameters['conditions'],['?' => ':'] );
            $where .= 'WHERE '.$conditions;
        }
        if(!empty($parameters['group'])){
            $where .= ' GROUP BY '.$parameters['group'];
        }
        if(!empty($parameters['order'])){
            $where .= ' ORDER BY '.$parameters['order'];
        }
        if(!empty($parameters['limit'])){
            $where .= ' LIMIT '.$parameters['limit'];
        }
        if(!empty($parameters['offset'])){
            $where .= ' OFFSET '.$parameters['offset'];
        }
        if(!empty($parameters['for_update'])){
            $where .= ' FOR UPDATE';
        }
        if(!empty($parameters['shared_lock'])){
            $where .= 'LOCK IN SHARE MODE';
        }
        return $where;
    }
    /**
     * 插入数据
     * @param $inset 插入数据array(key => val)
     * @return string|boolean
     */
    public function insert($inset = array())
    {
        if(empty($inset) || !is_array($inset)){
            return 'inset data empty';
        }
        $key_name = '';
        $val_name = '';
        foreach($inset as $key => $val){
            $key_name .= ','.$key;     //字符串前去逗号
            $val_name .= ':'.$key.','; //字符串后去逗号
        }
        $key_name = substr($key_name, 1);
        $val_name = substr($val_name, 0 , -1);
        $table = $this->_tablePrefix.$this->_tableName;
        $sql = "INSERT INTO ".$table." (".$key_name.") VALUES (".$val_name.")";
        $sth = $this->dbh->prepare($sql);
        foreach($inset as $key => $val){
            $sth->bindValue(":{$key}",$val);
        }
        return $sth->execute();
    }
    
    /**
     * 删除数据
     * @param  $parameters   // conditions group order limit offset for_update shared_lock bind
     * @return string|number //删除条数
     */
    public function delete($parameters=null)
    {
        if(!empty($parameters['for_update']) && !empty($parameters['shared_lock'])){
            return 'sql error';
        }
        $table = $this->_tablePrefix.$this->_tableName;
        $where = $this->getWhere($parameters);
        $sql = 'DELETE FROM '.$table.' '.$where;
        $sth = $this->dbh->prepare($sql);
        if(!empty($parameters['bind']) && is_array($parameters['bind'])){
            foreach ($parameters['bind'] as $key => $val){
                $sth->bindValue(":{$key}",$val);
            }
        }
        $sth->execute();    
        return $sth->rowCount();
    }
    
    // 更新数据
    public function update($update,$parameters)
    {
        
        
        
    }
     
    /**
     * 查询全部数据 
     * @param $parameters  // columns conditions group order limit offset for_update shared_lock bind
     * @return string|mixed
     */
    public function find($parameters=null)
    {
        if(!empty($parameters['for_update']) && !empty($parameters['shared_lock'])){
            return 'sql error';
        }
        if(empty($parameters['columns'])){
            $parameters['columns'] = '*';
        }        
        $table = $this->_tablePrefix.$this->_tableName;
        $where = $this->getWhere($parameters);
        $sql = 'SELECT '.$parameters['columns'].' FROM '.$table.' '.$where;
        $sth = $this->dbh->prepare($sql);
        if(!empty($parameters['bind']) && is_array($parameters['bind'])){
            foreach ($parameters['bind'] as $key => $val){
                $sth->bindValue(":{$key}",$val);
            }
        }
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
    
    /**
     * 查询一条数据
     * @param $parameters   // columns conditions group order limit offset for_update shared_lock bind
     * @return string|mixed
     */
    public function findFirst($parameters=null)
    {
        if(!empty($parameters['for_update']) && !empty($parameters['shared_lock'])){
            return 'sql error';
        }
        if(empty($parameters['columns'])){
            $parameters['columns'] = '*';
        }
        $parameters['limit'] = 1;
        $table = $this->_tablePrefix.$this->_tableName;
        $where = $this->getWhere($parameters);
        $sql = 'SELECT '.$parameters['columns'].' FROM '.$table.' '.$where;
        $sth = $this->dbh->prepare($sql);
        if(!empty($parameters['bind']) && is_array($parameters['bind'])){
           foreach ($parameters['bind'] as $key => $val){
               $sth->bindValue(":{$key}",$val);
           }
        }
        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }
    
    /**
     * 查询数据条数
     * @param $parameters  // conditions group order limit offset for_update shared_lock bind
     * @return string|mixed
     */
    public function count($parameters=null)
    {
        if(!empty($parameters['for_update']) && !empty($parameters['shared_lock'])){
            return 'sql error';
        }
        $parameters['columns'] = 'count(*)';
        $table = $this->_tablePrefix.$this->_tableName;
        $where = $this->getWhere($parameters);
        $sql = 'SELECT '.$parameters['columns'].' FROM '.$table.' '.$where;
        $sth = $this->dbh->prepare($sql);
        if(!empty($parameters['bind']) && is_array($parameters['bind'])){
            foreach ($parameters['bind'] as $key => $val){
                $sth->bindValue(":{$key}",$val);
            }
        }
        $sth->execute();
        $result = $sth->fetchColumn();
        return $result;
    }
    
    /**
     * SQL查询
     */
    public function query($sql,$bind)
    {
        
    }
    /**
     * 开启事务
     */
    public function begin()
    {
        $this->dbh->beginTransaction();
    }
    /**
     * 事务提交
     */
    public function commit()
    {
        $this->dbh->commit();
    }
    /**
     * 事务回滚
     */
    public function rollback()
    {
        $this->dbh->rollBack();
    }
    /**
     * $_instance 信息
     */
    public function instanceInfo()
    {
        return self::$_instance;
    }
    /**
     * destruct 关闭数据库连接
     */
    public function __destruct()
    {
        $this->dbh = null;
    }
}