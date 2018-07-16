<?php
/**
 * @author		tyler 
 * @date		2018-06-13
 * @desc		Redis静态化封装类
 */
namespace Apps\Library;

class RedisCache {
	private static $_host; // 主机名
	private static $_port; // 端口
	private static $_timeout; // 服务器连接限制时间 (秒)
	private static $_expire; // 缓存有效时间 (秒)
	private static $_prefix; // 前缀
	private static $_instance; // 缓存实例
	function __construct() {
	}
	function __destruct() {
		self::close ();
	}
	
	/**
	 * 连接redis服务器
	 * @return booble/resource
	 * @author tyler
	 * @date 2018-06-13
	 */
	private static function _connect() {
		if (self::$_instance === NULL) {
			$func = REDIS_CONFIG['persistent'] ? 'pconnect' : 'connect';
			self::$_host = REDIS_CONFIG['host'];
			self::$_port = REDIS_CONFIG['port'];
			self::$_prefix = REDIS_CONFIG['prefix'];
			self::$_timeout = REDIS_CONFIG['timeout'];
			self::$_expire = REDIS_CONFIG['expire'];
			self::$_instance = new \Redis();
			if (self::$_timeout === false) {
				self::$_instance->$func ( self::$_host, self::$_port );
			} else {
				self::$_instance->$func ( self::$_host, self::$_port, self::$_timeout );
			}
			if(!empty($server['password'])){
			    self::$_instance->auth($server['password']);
			}
			return self::$_instance;
		}
		return true;
	}
	
	/**
	 * 检查当前连接实例的状态
	 *
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function ping() {
		self::_connect ();
		try {
			$_ping = self::$_instance->ping ();
		} catch ( Exception $e ) {
			throw new \Exception('redis connect error');
			return false;
		}
		return true;
	}
	
	/**
	 * 关闭Redis的连接实例,但是不能关闭用pconnect连接的实例
	 *
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function close() {
		return self::$_instance->close ();
	}
	
	/**
	 * 清除当前连接缓存数据
	 *
	 * @return boolean
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function clear() {
		$_ping = self::ping ();
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->flushDB ();
	}
	
	/**
	 * 获取string数据类型缓存
	 *prefix
	 * @param string $key
	 *        	缓存变量名
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function get($key) {
		$_ping = self::ping ();
		$key = self::$_prefix.$key;
		if ($_ping === false) {
			return false;
		}
		$value = self::$_instance->get ( $key );
		$jsonData = json_decode ( $value, true );
		return ($jsonData === NULL) ? $value : $jsonData; // 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
	}
	
	/**
	 * 写入string数据类型缓存
	 *
	 * @param string $key
	 *        	缓存变量名
	 * @param string $value
	 *        	缓存数据
	 * @param booblean $expire
	 *        	是否设置有效时间 (秒)
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function set($key, $value, $expire = FALSE) {
		$_ping = self::ping ();
		$key = self::$_prefix.$key;
		if ($_ping === false) {
			return false;
		}
		$value = (is_object ( $value ) || is_array ( $value )) ? json_encode ( $value ) : $value; // 对数组/对象数据进行缓存处理，保证数据完整性
		if ($expire === FALSE) {
			$result = self::$_instance->set ( $key, $value );
		} else {
			if ($expire === TRUE) {
				$result = self::$_instance->setex ( $key, self::$_expire, $value );
			} else {
				$result = self::$_instance->setex ( $key, $expire, $value );
			}
		}
		return $result;
	}
	
	/**
	 * string数据类型值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
	 *
	 * @param string $key
	 *        	缓存变量名
	 * @param int $default
	 *        	操作时的默认值
	 * @return int
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function incr($key, $default = 1) {
		$_ping = self::ping ();
		$key = self::$_prefix.$key;
		if ($_ping === false) {
			return false;
		}
		if ($default == 1) {
			return self::$_instance->incr ( $key );
		} else {
			return self::$_instance->incrBy ( $key, $default );
		}
	}
	
	/**
	 * string数据类型值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
	 *
	 * @param string $key
	 *        	缓存变量名
	 * @param int $default
	 *        	操作时的默认值
	 * @return int
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function decr($key, $default = 1) {
		$_ping = self::ping ();
		$key = self::$_prefix.$key;
		if ($_ping === false) {
			return false;
		}
		if ($default == 1) {
			return self::$_instance->decr ( $key );
		} else {
			return self::$_instance->decrBy ( $key, $default );
		}
	}
	
	/**
	 * 删除string数据类型$key的缓存
	 *
	 * @param
	 *        	string || array $key 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
	 * @return boolean
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function del($key) {
		$_ping = self::ping ();
		$key = self::$_prefix.$key;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->delete ( $key );
	}
	/**
	 * 队列左边出队
	 *
	 * @param [type] $course
	 *        	[description]
	 * @return [type] [description]
	 */
	public static function lPop($course) {
		$_ping = self::ping ();
		$course = self::$_prefix.$course;
		if ($_ping === false) {
			return false;
		}
		$result = self::$_instance->lPop ( $course );
		return @unserialize ( $result );
	}
	/**
	 * 队列右边入队
	 *
	 * @param [type] $course
	 *        	[description]
	 * @param [type] $value
	 *        	[description]
	 * @return [type] [description]
	 */
	public static function rPush($course, $value) {
		$_ping = self::ping ();
		$course = self::$_prefix.$course;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->rPush ( $course, serialize ( $value ) );
	}
	/**
	 * 返回队列长度
	 *
	 * @param [type] $course
	 *        	[description]
	 * @return [type] [description]
	 */
	public static function lLen($course) {
		$_ping = self::ping ();
		$course = self::$_prefix.$course;
		if ($_ping === false) {
			return false;
		}
		return ( int ) self::$_instance->lLen ( $course );
	}
	/**
	 * 获取hash数据类型hash表对应的$key的缓存值
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @param string $key
	 *        	hash表中缓存变量名
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hGet($hkey, $key) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		$value = self::$_instance->hGet ( $hkey, $key );
		$jsonData = json_decode ( $value, true );
		return ($jsonData === NULL) ? $value : $jsonData; // 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
	}
	
	/**
	 * 写入hash数据类型hash表对应key的缓存值
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @param string $key
	 *        	hash表中缓存变量名
	 * @param string $value
	 *        	缓存数据
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hSet($hkey, $key, $value) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		$value = (is_object ( $value ) || is_array ( $value )) ? json_encode ( $value ) : $value; // 对数组/对象数据进行缓存处理，保证数据完整性
		$result = self::$_instance->hSet ( $hkey, $key, $value );
		return $result;
	}
	
	/**
	 * 批量取得HASH表中的VALUE。
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @param string $keys
	 *        	例:Array('field1', 'field2')
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hMget($hkey, $keys) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		$values =  self::$_instance->hMget ( $hkey, $keys );
		foreach ($values as $key=>$val){
			$jsonData = json_decode ( $val, true );// 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
			$values[$key] = ($jsonData === NULL) ? $val : $jsonData;
		}
		return $values; 
	}
	
	/**
	 * 批量填充HASH表。不是字符串类型的VALUE，自动转换成字符串类型。使用标准的值。NULL值将被储存为一个空的字符串。
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @param string $members
	 *        	array('field1'=>$value1,'field2'=>$value2)
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hMset($hkey, $members) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		$values = array();
		foreach ($members as $key=>$val){
			$values[$key] = (is_object ( $val ) || is_array ( $val )) ? json_encode ( $val ) : $val; // 对数组/对象数据进行缓存处理，保证数据完整性
		}
		return self::$_instance->hMset ( $hkey, $values );
	}
	
	/**
	 * 删除hash数据类型hash表对应的$key的缓存值
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @param string $key
	 *        	hash表中缓存变量名
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hDel($hkey, $key) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->hDel ( $hkey, $key );
	}
	
	/**
	 * 取得hash数据类型hash表所有的key值,以数组形式返回
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hKeys($hkey) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->hKeys ( $hkey );
	}
	
	/**
	 * 取得hash数据类型hash表所有的value值,以数组形式返回
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hVals($hkey) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->hVals ( $hkey );
	}
	
	/**
	 * 取得hash数据类型hash表所有的key/value键值对,以数组形式返回
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hGetAll($hkey) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		$result = self::$_instance->hGetAll ( $hkey );
		if (! empty ( $result )) {
			foreach ($result as $key=>$val){
				$jsonData = json_decode ( $val, true );// 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
				$result[$key] = ($jsonData === NULL) ? $val : is_numeric($val)? $val : $jsonData;
			}
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * 删除hash数据类型hash表对应hkey所有的键值对
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hDelAll($hkey) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->delete ( $hkey );
	}
	
	/**
	 * 返回hash数据元素个数
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @return mixed
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hLen($hkey) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		return ( int ) self::$_instance->hLen ( $hkey );
	}
	
	/**
	 * hash数据类型值hash表对应$hkey=>$key=>$value值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
	 *
	 * @param string $hkey
	 *        	hash表key
	 * @param string $key
	 *        	缓存变量名
	 * @param int $default
	 *        	操作时的默认值
	 * @return int
	 * @author winter 
	 *         @date 2016-11-12
	 */
	public static function hIncrBy($hkey, $key, $default = 1) {
		$_ping = self::ping ();
		$hkey = self::$_prefix.$hkey;
		if ($_ping === false) {
			return false;
		}
		return self::$_instance->hIncrBy ( $hkey, $key, $default );
	}

	/**
	 * 设置过期时间
	 * @param $key
	 * @param $expire
	 * @author: qing.yang
	 * @return bool
	 */
	public static function expire($key,$expire){
		$_ping = self::ping();
		$key = self::$_prefix.$key;
		if($_ping === false){
			return false;
		}
		return self::$_instance->expire($key,$expire);
	}
	
	/**
	 * 获取过期时间
	 * @param $key
	 * @author: tyler.liu
	 * @return int
	 */
	public static function ttl($key){
	    $_ping = self::ping();
	    $key = self::$_prefix.$key;
	    if($_ping === false){
	        return false;
	    }
	    return self::$_instance->ttl($key);
	}
}