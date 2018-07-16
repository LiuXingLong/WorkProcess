<?php
namespace Apps\Common\Models;

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
        } catch (PDOException $e) {
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
     * 获取当前表明
     */
    public function getSource($table){
        $this->_tableName = $table;
    }
    /**
     *获取PDO连接
     */
    public function getDB(){
        return $this->dbh;
    }
    public function insert($parameters=null){
    
    }
    
    public function delete($parameters=null){
    
    }
    
    public function update($parameters=null){
    
    }
    
    public function find($parameters=null){
    
    }
    
    public function findFirst($parameters=null){
    
        array (
            "columns" => "*",
            "conditions" => "game_id = ?1 and player_id = ?2 and channel_id = ?3 and activity_id = ?4",
            "bind" => array (
                1 => $game_id,
                2 => $player_id,
                3 => $channel_id,
                4 => $activity_id,
            ),
            "order" => "lottery_time DESC",
        ) ;
    
    
    }
    
    public function count($parameters=null){
        
    }
    
    /**
     * SQL查询
     */
    public function query($sql,$bind){
        
    }
    /**
     * 开启事务
     */
    public function begin(){
        $this->dbh->beginTransaction();
    }
    /**
     * 事务提交
     */
    public function commit(){
        $this->dbh->commit();
    }
    /**
     * 事务回滚
     */
    public function rollback(){
        $this->dbh->rollBack();
    }
    /**
     * $_instance 信息
     */
    public function instanceInfo(){
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