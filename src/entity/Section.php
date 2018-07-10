<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings\entity;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Section
 * @package Ceive\Frontend
 */
class Section implements \ArrayAccess, \IteratorAggregate{
	
	public $title;
	
	public $settings = [];
	
	public function getIterator(){
		return new \ArrayIterator($this->settings);
	}
	
	public function offsetExists($offset){
		return $this->settings[$offset];
	}
	
	public function offsetGet($offset){
		return isset($this->settings[$offset])?$this->settings[$offset]:null;
	}
	
	public function offsetSet($offset, $value){
		$this->settings[$offset] = $value;
	}
	
	public function offsetUnset($offset){
		unset($this->settings[$offset]);
	}
}


