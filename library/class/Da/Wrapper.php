<?php
/**
 *  数据库访问的包装类
 *
 * @author liut
 * @version $Id$
 * @created 13:30 2009-04-21
 */

/**
 * Da_Wrapper
 */
final class Da_Wrapper
{
	private static $settings = array();
	private static $dbos = array();
	private static $logs = array();
	
	const CONF_NAME = 'da';
	
	/**
	 * constructor
	 * 
	 * @return void
	 */
	private function __construct()
	{
	}

	/**
	 * function description
	 * 
	 * @param string $ns
	 * @param string $db
	 * @param string $node, table
	 * @return PDO
	 */
	public static function dbo($table_key)
	{
		$arr = self::parseConfigKey($table_key);
		if ($arr === FALSE) {
			$cfg = Loader::config(self::CONF_NAME);
			if (isset($cfg['default'])) {
				$arr = self::parseConfigKey($cfg['default']);
			}
		}
		if ($arr === FALSE) {
			throw new InvalidArgumentException('invalid table key');
		}
		//list($ns, $db, $node) = $arr;
		$cfg = self::configArray($arr);

		$key = $arr['key'];

		if(!isset(self::$dbos[$key]) || self::$dbos[$key] == null) {
			if(!is_array($cfg) || !isset($cfg['dsn']) || !isset($cfg['username'])) {
				throw new Exception('config dsn ['.$table_key.'] not found!');
			}
			//echo '<!--', $key, print_r(debug_backtrace(), TRUE), '-->', PHP_EOL;
			self::$dbos[$key] = new PDO($cfg['dsn'], $cfg['username'], $cfg['password']);
			if(!empty($cfg['charset'])) {
				if(self::$dbos[$key]->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
					self::$dbos[$key]->exec('SET character_set_connection='.$cfg['charset'].', character_set_results='.$cfg['charset'].', character_set_client=binary');
				}
			}
			if(defined('_DB_DEBUG') && TRUE === _DB_DEBUG) {
				self::$dbos[$key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		}

		return self::$dbos[$key];
	}
	
	/**
	 * 切分出数据库配置项名称
	 * @param string $table_key format: ns.db[.node][[.schema].table]
	 * @return array
	 */
	public static function parseConfigKey($str)
	{
		if (preg_match("#^([a-z]{2,9})\.([a-z][a-z0-9_]{1,9})(\.[a-z])?((\.\w{4,9})?\.([a-z][a-z0-9_]{1,9}))?$#i", $str, $match)) {
			
			$arr = array(
				'ns' => $match[1],
				'db' => $match[2]
			);
			$arr['node'] = isset($match[3]) ? $match[3] : '';
			if (isset($match[5])) {
				$arr['schema'] = $match[5];
				$arr['table'] = $match[6];
			}
			$arr['key'] = $arr['ns'] . '.' . $arr['db'] . $arr['node']; // ns.db.node
			!empty($arr['node']) && $arr['node'] = substr($arr['node'], 1);
			!empty($arr['schema']) && $arr['schema'] = substr($arr['schema'], 1);
			return $arr;
		}
		return FALSE;
	}

	/**
	 * function description
	 * 
	 * @param string $config
	 * @return void
	 */
	public static function configArray($arr)
	{
		extract($arr);
		$cfg = Loader::config(self::CONF_NAME);
		if(isset($cfg[$ns])) $cfg = & $cfg[$ns];
		if(isset($cfg[$db])) $cfg = & $cfg[$db];
		if(!empty($node) && isset($cfg[$node])) $cfg = & $cfg[$node];

		return $cfg;
	}
	
	/**
	 * @deprecated
	 */
	public static function configValue($arr, $name)
	{
		extract($arr);
		$cfg = Loader::config(self::CONF_NAME);
		$ret = & $cfg[$name];
		if(isset($cfg[$ns])) $cfg = & $cfg[$ns];
		if(isset($cfg[$name])) $ret = & $cfg[$name];
		if(isset($cfg[$db])) {
			$cfg = & $cfg[$db];
			if(isset($cfg[$name])) $ret = & $cfg[$name];
		}
		if(!empty($node) && isset($cfg[$node])) {
			$cfg = & $cfg[$node];
			if(isset($cfg[$name])) $ret = & $cfg[$name];
		}
		return $ret;
	}
	
	public static function getDbos() {
		return self::$dbos;
	}

	/**
	 * function description
	 * 
	 * @return this
	 */
	public static function select($cond = array())
	{
		return Da_Wrapper_Abstract::select($cond);
	}

	/**
	 * function description
	 * 
	 * @return this
	 */
	public static function update()
	{
		return Da_Wrapper_Abstract::update();
	}

	/**
	 * function description
	 * 
	 * @return this
	 */
	public static function insert()
	{
		return Da_Wrapper_Abstract::insert();
	}

	/**
	 * function description
	 * 
	 * @return this
	 */
	public static function delete()
	{
		return Da_Wrapper_Abstract::delete();
	}

	/**
	 * 返回一行作为数组
	 * 
	 * @param string $table_key
	 * @param string $sql
	 * @param array $params
	 * @param int $fetch_style
	 * @return array
	 */
	public static function getRow($table_key, $sql, $params = null, $fetch_style = PDO::FETCH_ASSOC)
	{
		$dbh = self::dbo($table_key);self::injectLog($dbh, $sql);
		if(is_null($params) || ! is_array($params) || count($params) == 0) {
			$sth = $dbh->query($sql);
			if($sth) return $sth->fetch($fetch_style);
		} else {
			$sth = $dbh->prepare($sql);
			if($sth && $ret = $sth->execute($params)) {
				return $sth->fetch($fetch_style);
			}
		}
		if(!$sth) {
			Log::warning(__CLASS__ . '::'. __FUNCTION__ .' : '.print_r($dbh->errorInfo(),true) . "\n" . $table_key . ': ' . $sql);
			return false;
		}
		if(isset($ret) && !$ret) {
			Log::warning(__CLASS__ . '::'. __FUNCTION__ .' : '.print_r($sth->errorInfo(),true) . "\n" . $table_key . ': ' . $sql);
			return false;
		}
		return false;
	}

	/**
	 * 返回全部结果集
	 * 
	 * @param string $table_key
	 * @param string $sql
	 * @param array $params
	 * @param int $fetch_style
	 * @return array
	 */
	public static function getAll($table_key, $sql, $params = null, $fetch_style = PDO::FETCH_ASSOC)
	{
		$dbh = self::dbo($table_key);self::injectLog($dbh, $sql);
		if(is_null($params) || ! is_array($params) || count($params) == 0) {
			$sth = $dbh->query($sql);
			if($sth) return $sth->fetchAll($fetch_style);
		} else {
			$sth = $dbh->prepare($sql);
			if($sth && $ret = $sth->execute($params)) {
				return $sth->fetchAll($fetch_style);
			}
		}
		if(!$sth) {
			Log::warning(__CLASS__ . '::'. __FUNCTION__ .' : '.print_r($dbh->errorInfo(),true) . "\n" . $table_key . ': ' . $sql);
			return false;
		}
		if(isset($ret) && !$ret) {
			Log::warning(__CLASS__ . '::'. __FUNCTION__ .' : '.print_r($sth->errorInfo(),true) . "\n" . $table_key . ': ' . $sql);
			return false;
		}
		return false;
	}

	/**
	 * 返回第一行第一列的值
	 * 
	 * @param string $table_key
	 * @param string $sql
	 * @param array $params
	 * @return string
	 */
	public static function getOne($table_key, $sql, $params = null)
	{
		$dbh = self::dbo($table_key);self::injectLog($dbh, $sql);
		if(is_null($params) || ! is_array($params) || count($params) == 0) {
			$sth = $dbh->query($sql);
			if($sth) return $sth->fetchColumn(0);
		} else {
			$sth = $dbh->prepare($sql);	//var_dump($sth, $sql, $params);
			if($sth && $ret = $sth->execute($params)) {
				return $sth->fetchColumn(0);
			}
		}
		return false;
	}

	/**
	 * 返回所有行第一列的值作为数组，或返回分组
	 * 
	 * @param string $table_key
	 * @param string $sql
	 * @param array $params
	 * @param boolean $group 是否按第一列分组
	 * @return array
	 */
	public static function getFlat($table_key, $sql, $params = null, $group = FALSE)
	{
		$dbh = self::dbo($table_key);self::injectLog($dbh, $sql);
		if(is_null($params) || ! is_array($params) || count($params) == 0) {
			$sth = $dbh->query($sql);
			if($sth) return $group ? $sth->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP) : $sth->fetchAll(PDO::FETCH_COLUMN, 0);
		} else {
			$sth = $dbh->prepare($sql);
			if($sth && $ret = $sth->execute($params)) {
				return $group ? $sth->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP) : $sth->fetchAll(PDO::FETCH_COLUMN, 0);
			}
		}
		if(!$sth) {
			Log::warning(__CLASS__ . '::'. __FUNCTION__ .' : '.print_r($dbh->errorInfo(),true) . "\n" . $table_key . ': ' . $sql);
			return false;
		}
		return false;
	}
	
	/**
	 * 直接执行一条SQL，如 UPDATE、INSERT、DELETE等
	 * 
	 * @param string $table_key
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public static function execute($table_key, $sql, $params = null)
	{
		$dbh = self::dbo($table_key);self::injectLog($dbh, $sql);
		if(is_null($params) || ! is_array($params) || count($params) == 0) {
			return $dbh->exec($sql);
		} else {
			$sth = $dbh->prepare($sql);
			return $sth->execute($params);
		}
		return false;
	}
	
	/**
	 * 执行一条SQL，并返回 Statment 对象
	 * 
	 * @param string $table_key
	 * @param string $sql
	 * @param array $params
	 * @return object
	 */
	public static function query($table_key, $sql, $params = null)
	{
		$dbh = self::dbo($table_key);self::injectLog($dbh, $sql);
		if(is_null($params) || ! is_array($params) || count($params) == 0) {
			$sth = $dbh->query($sql);
		} else {
			$sth = $dbh->prepare($sql);
			if($sth) $sth->execute($params);
		}
		if(!$sth) {
			Log::warning(__CLASS__ . '::'. __FUNCTION__ .' : '.print_r($dbh->errorInfo(),true) . "\n" . $table_key . ': ' . $sql);
			return false;
		}
		return $sth;
	}
	
	/**
	 * 释放 dbos 对象
	 * 
	 * @return void
	 */
	public static function destroy()
	{
		self::$dbos = array();
	}
	
	/**
	 * 返回 Mongo 对象
	 * 
	 * @param string $table_key
	 * @return object
	 */
	public static function mongo($table_key)
	{
		$arr = self::parseConfigKey($table_key);
		$cfg = self::configArray($arr);

		if(!is_array($cfg) || !isset($cfg['servers'])) {
			throw new Exception('config dsn ['.$table_key.'] not found!');
		}
		$key = $arr['key'];

		static $mgs = array();
		if(!isset($mgs[$key]) || $mgs[$key] == null) {
			$mgs[$key] = new Mongo($cfg['servers'], $cfg['options']);
		}
		return $mgs[$key];
	}
	
	/**
	 * 查找并返回指定的 MongoCollection 对象
	 * 
	 * @param mixed $mongo_db
	 * @param string $collection_name
	 * @return mixed
	 */
	public static function mongoCollection($mongo_db, $name)
	{
		if (is_string($mongo_db) && preg_match("/^\w+\.\w+/i", $mongo_db)) {
			list(, $db_name) = explode(".", $mongo_db, 3);
			$mongo_db = self::mongo($mongo_db)->selectDB($db_name);
		}
		if (!is_object($mongo_db)) return false;
		$list = $mongo_db->listCollections();
		foreach ($list as $collection) {
			if ($collection->getName() == $name) return $collection;
			//echo 'collection name: ', $collection->getName(), PHP_EOL;
		}

		//return false;
		// MongoDb 会自动建立所引用的 Collection, 但这里抛出异常, 是为了实现定制及优化, 所有的 Collection 必须提前建立
		throw new Exception('collection ['.$name.'] not found!');
	}
	
	private static function injectLog(& $dbh, $str)
	{
		if (defined('_DB_DEBUG') && TRUE === _DB_DEBUG) {
			if (!property_exists( $dbh, 'logs') ) {
				$dbh->logs = array();
			}
			$dbh->logs[] = $str;
		}
		
	}

}
