<?php
/**
 * Data Object Model 数据模型基类，多表示为通用的、 以数字Id为主键的类的基类
 *
 * @author liut
 * @version $Id$
 * @created 2:20 2009年7月16日
 */

/**
 * Model Base
 * 
 */
abstract class Model implements ArrayAccess, Serializable
{
	// private vars
	private $_data = array();
	private $_key;
	private $_old_data = array();
	private $_new_data = array();

	//protected vars;
	/**
	 * @var  string  table name to overwrite assumption
	 */
	// protected static $_db_name;

	//protected vars;
	/**
	 * @var  string  table name to overwrite assumption
	 */
	// protected static $_table_name;
	
	/**
	 * @var  array  editable fields
	 */
	protected $_editables = array();
	
	/**
	 * @var  array  name or names of the primary keys
	 */
	protected static $_primary_key = array('id');

	/**
	 * farm a new class instance, 生产一个新实例
	 * 
	 * @param  	array or mixed $data
	 * @return  object
	 */
	public static function farm($data = array())
	{
		return new static($data);
	}

	/**
	 * Get the database name
	 */
	public static function db()
	{
		$class = get_called_class();
		// database name set in Model
		if (property_exists($class, '_db_name')) {
			return static::$_db_name;
		}
		else {
			$dac = Loader::config('da');
			if (isset($dac['default'])) {
				return $dac['default'];
			}
		}
		// TODO: exception?
		throw new Exception('property _db_name undefined or no default db');
	}

	/**
	 * Get the table name for this class
	 *
	 * @return  string
	 */
	public static function table()
	{
		$class = get_called_class();
		// Table name set in Model
		if (property_exists($class, '_table_name')) {
			return static::$_table_name;
		}
		else {
			$class_name = trim($class_name, '\\');
			if ($last_separator = strrpos($class_name, '\\'))
			{
				$class_name = substr($class_name, $last_separator + 1);
			}
			if (strncasecmp($class_name, 'Model_', 6) === 0)
			{
				$class_name = substr($class_name, 6);
			}
			return strtolower($class_name);
		}
	}

	/**
	 * Get the primary key(s) of this class
	 *
	 * @return  array
	 */
	public static function primaryKey()
	{
		return static::$_primary_key;
	}
	
	/**
	 * constructor
	 * 
	 * @param int $id
	 */
	protected function __construct($data = null)
	{
		$this->init($data);
	}

	/**
	 * initFromArgs
	 * 
	 * @param mixed $id
	 * @param string $col
	 * @return void
	 */
	protected function init($id, $col = 'id')
	{
		if(is_array($id)) {
			$this->_set($id);
		}
		elseif(is_int($id) || is_numeric($id) && $col == 'id') {
			$row = $this->_getRow($id, 'id');
			if(is_array($row)) {
				$this->_set('id', intval($id));
				$this->_set($row);
			}
		}
		elseif(!empty($id) && in_array($col, static::$_primary_key))
		{
			$row = $this->_getRow($id, $col);
			if(is_array($row)) {
				$this->_set($col, $id);
				$this->_set($row);
			}
		}
		$this->_key = $this->getKey();
	}

	/**
	 * 经过包装的属性获取方法
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		$_name = '_'.$name;
		if (property_exists($this, $_name)) {
			//echo 'exists ', $name, "\n";
			return $this->$_name;
		}
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
		$method = 'get'.ucfirst($name);
		if(method_exists($this, $method)) 
		{
			$ret = $this->$method();
			$this->_data[$name] = $ret;
			return $ret;
		}
		return null;
	}

	/**
	 * Check if key exists in data
	 * 
	 * @param string $name
	 * @return void
	 */
	public function __isset($name)
	{
		$_name = '_'.$name;
		if (property_exists($this, $_name)) {
			return true;
		}
		return array_key_exists($name, $this->_data);
	}
	
	/**
	 * inner unset
	 * 
	 * @param string $name
	 * @return void
	 */
	protected function _unset($name)
	{
		unset($this->_data[$name]);
	}
	
	
	/**
	 * 设置成员值
	 * 
	 * @param string $name | array
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		if(!is_string($name) || empty($name)) return false;
		
		$method = 'set'.ucfirst($name);
		if(method_exists($this, $method)) {
			$old_value = $this->__get($name);
			$this->$method($value);
			$new_value = $this->__get($name);
			$this->_setChanged($name, $new_value, $old_value);
			return;
		}
		$_name = '_'.$name;
		if (property_exists($this, $_name)) {
			$this->$_name = $value;
		}
		else
		{
			$this->_setChanged($name, $value, $this->__get($name));
			$this->_data[$name] = $value;
		}
	}

	/**
	 * 修改某个值时做记录
	 * 
	 * @param string $name
	 * @param string $new_value
	 * @param string $old_value
	 * @return void
	 */
	private function _setChanged($name, $new_value, $old_value)
	{
		if (substr($name, 0, 1) == '_') return;
		if($this->checkEditable($name)) {
			if($new_value != $old_value) {
				$this->_old_data[$name] = $old_value;
				$this->_new_data[$name] = $new_value;
			}
		}
	}

	/**
	 * [保护]返回修改过的值
	 * 
	 * @return array
	 */
	protected function _getChanged()
	{
		return $this->_new_data;
	}

	/**
	 * [保护]返回原来的值
	 * 
	 * @return array
	 */
	protected function _getOriginal()
	{
		return $this->_old_data;
	}

	/**
	 * internal set value only !!!
	 * 
	 * @param string $name | array
	 * @param string $value
	 * @return void
	 */
	protected function _set($name, $value = null)
	{
		if(is_array($name)) // init or load
		{
			foreach($name as $k => $v) {
				$this->_initSet($k, $v);
			}
		}
		elseif(is_string($name)) {
			$this->_initSet($name, $value);
		}
		return true;
	}

	/**
	 * init set data value
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	private function _initSet($name, $value)
	{
		$_name = '_'.$name;
		if (property_exists($this, $_name)) {
			$this->$_name = $value;
		}
		else $this->_data[$name] = $value;
	}

	/**
	 * 返回这个类是否为有效的类，以Id>0 且条目数多于1 为条件
	 * 
	 * @return boolean
	 */
	public function valid()
	{
		return $this->id > 0 && count($this->_data) > 1;
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	protected function clear()
	{
		unset($this->_data);
	}

	/**
	 * 根据 id 返回用来在 Cache 中使用的本类的 键名
	 * 
	 * @param int $id
	 * @return string
	 */
	public function getKey()
	{
		return get_class($this) . '_' . $this->id;
	}

	/**
	 * 根据 id 返回数据行，一般是数据库, deprecated
	 * 
	 * @param mixed $id, int or string
	 * @param string $key
	 * @return array
	 */
	protected function _getRow($id, $key = 'id')
	{
		return static::find()->where($key, $id)->getRow();
	}

	/**
	 * Find one or more entries
	 *
	 * @param   mixed
	 * @param   array
	 * @return  object|array
	 */
	public static function find($id = null, array $condition = array())
	{
		// Return Select object
		if (is_null($id) or $id == 'all' or $id == 'first' or $id == 'last')
		{
			$select = Da_Wrapper::select($condition)
				->db(static::db())
				->table(static::table());
			
			// Return all that match $options array
			if ($id == 'all') {
				return $select->getAll();
			}
			// Return first or last row that matches $options array
			elseif($id == 'first' or $id == 'last') {
				$pks = static::primaryKey();
				if (count($pks) > 0)
					$select->orderby($pks[0], $id == 'first' ? 'ASC' : 'DESC');
				return $select->getRow();
			}
			return $select;
		}
		
		// Return specific request row by ID
		else
		{
			$cache_pk = $where = array();
			$id = (array) $id;
			foreach (static::primaryKey() as $pk)
			{
				$where[] = array($pk, '=', current($id));
				$cache_pk[$pk] = current($id);
				next($id);
			}
			
			// TODO: cachable process
			
			array_key_exists('where', $condition) and $where = array_merge($condition['where'], $where);
			$condition['where'] = $where;
			return Da_Wrapper::select($condition)
				->db(static::db())
				->table(static::table())
				->limit(1)->getRow();
		}
	}

	/**
	 * 检查某个字段是否可编辑
	 * 
	 * @param string $name
	 * @return void
	 */
	public function checkEditable($name)
	{
		return in_array($name, $this->_editables);
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	protected function setEditable($name, $alias = null)
	{
		if(is_array($name)) {
			$this->_editables = array_merge($this->_editables, $name);
		}
		else {
			if(is_null($alias)) $alias = $name;
			$this->_editables[$name] = $alias;
		}
	}

	/**
	 * implements ArrayAccess
	 * 
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	/**
	 * implements ArrayAccess
	 * 
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * implements ArrayAccess
	 * 
	 * @param string $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	/**
	 * implements ArrayAccess
	 * 
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		$this->_unset($offset);
	}
	
	
	protected static function _genKey($id, $col = 'id')
	{
		return '_'.$col.$id;
	}
	
	// Serializable
    public function serialize() {
        return serialize($this->_data);
    }
	
	// Serializable
    public function unserialize($data) {
        $this->_data = unserialize($data);
    }
	
}

