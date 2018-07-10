<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Frontend
 * @package Ceive\Frontend
 */
abstract class SettingsAbstract implements \ArrayAccess, SettingsRegistry, \IteratorAggregate{
	
	/**
	 * @param array|string $key
	 */
	abstract public function get($key);
	
	/**
	 * @param array|string $key
	 * @param $value
	 */
	abstract public function set($key, $value = null);
	
	/**
	 * @param array|string $key
	 */
	abstract public function has($key);
	
	/**
	 * @param array|string $key
	 */
	abstract public function delete($key);
	
	/**
	 * @param null $limit
	 * @param null $offset
	 * @param null|string $query
	 * @return array
	 */
	abstract public function toArray($limit = null, $offset = null, $query = null);
	
	public function offsetExists($offset){
		return $this->has($offset);
	}
	
	public function offsetGet($offset){
		return $this->get($offset);
	}
	
	public function offsetSet($offset, $value){
		return $this->set($offset, $value);
	}
	
	public function offsetUnset($offset){
		return $this->delete($offset);
	}
	
	public function getIterator(){
		return new \ArrayIterator($this->toArray());
	}
	
	
}


