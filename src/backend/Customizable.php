<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings\backend;

use Ceive\settings\Backend;


/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Customizable
 * @package Ceive\Frontend\backend
 */
class Customizable extends Backend{
	
	
	protected $set;// ($key, $value)
	protected $get;// ($key)
	protected $has;// ($key)
	protected $delete;// ($key)
	protected $list;// ($limit = null, $offset = null, $query = null)
	
	public function __construct(
		callable $set = null,
		callable $get = null,
		callable $has = null,
		callable $delete = null,
		callable $list = null
	){
		$this->set = $set;
		$this->get = $get;
		$this->has = $has;
		$this->delete = $delete;
		$this->list = $list;
	}
	
	/**
	 * @param array|string $key
	 * @return mixed
	 */
	public function get($key){
		return call_user_func($this->get, $key, $this);
	}
	
	/**
	 * @param array|string $key
	 * @param $value
	 * @return mixed
	 */
	public function set($key, $value = null){
		return call_user_func($this->set, $key, $value, $this);
	}
	
	/**
	 * @param array|string $key
	 * @return mixed
	 */
	public function has($key){
		return call_user_func($this->has, $key, $this);
	}
	
	/**
	 * @param array|string $key
	 * @return mixed
	 */
	public function delete($key){
		return call_user_func($this->delete, $key, $this);
	}
	
	/**
	 * @param null $limit
	 * @param null $offset
	 * @param null $query
	 * @return array
	 */
	public function toArray($limit = null, $offset = null, $query = null){
		return call_user_func($this->list, $limit, $offset, $query, $this);
	}
}


