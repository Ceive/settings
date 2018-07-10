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
class Frontend extends SettingsAbstract{
	
	const STATE_EXISTS = true;
	const STATE_ABSENT = false;
	
	
	public $state = [];
	public $settings = [];
	
	
	/** @var Schema */
	public $schema;
	
	/** @var  SettingsAbstract|array|\ArrayAccess|null */
	public $backend;
	
	/**
	 * @param array|string $key
	 * @return mixed
	 */
	public function get($key){
		if(!$this->schema->hasSetting($key)){
			$this->caseNotDefinedSetting($key);
			return null;
		}
		
		if(isset($this->settings[$key])){
			$this->state[$key] = true;
			return $this->settings[$key];
		}
		
		if(isset($this->backend)){
			
			if(!isset($this->state[$key]) && ($this->state[$key] = isset($this->backend[$key]))){
				return $this->settings[$key] = $this->_afterSettingFetch($key, $this->backend[$key]);
			}
			if($this->state[$key]){
				
				if(!isset($this->settings[$key])){
					return $this->settings[$key] = $this->_afterSettingFetch($key, $this->backend[$key]);
				}
				
			}else{
				$setting = $this->schema->getSetting($key);
				if($setting) return $setting->getDefaultSettingValue($key, $this);
			}
		}
		
		return isset($this->settings[$key]) ? $this->settings[$key] : null;
	}
	
	
	/**
	 * @param array|string $key
	 * @param $value
	 * @return $this
	 */
	public function set($key, $value = null){
		if(!$this->schema->hasSetting($key)){
			$this->caseNotDefinedSetting($key);
			return $this;
		}
		
		$value = $this->_beforeSettingSet($key, $value);
		
		$this->state[$key] = true;
		$this->settings[$key] = $value;
		if(isset($this->backend)){
			$this->backend[$key] = $value;
		}
		
		
		return $this;
	}
	
	/**
	 * @param array|string $key
	 * @return mixed
	 */
	public function has($key){
		if(!$this->schema->hasSetting($key)){
			$this->caseNotDefinedSetting($key);
			return false;
		}
		
		if(!isset($this->state[$key])){
			
			if(isset($this->backend)){
				$this->state[$key] = isset($this->backend[$key]);
			}else{
				$this->state[$key] = isset($this->settings[$key]);
			}
			
		}
		
		return boolval($this->state[$key]);
	}
	
	/**
	 * @param array|string $key
	 * @return $this
	 */
	public function delete($key){
		
		if($this->has($key)){
			unset($this->settings[$key]);
			unset($this->state[$key]);
			if(isset($this->backend)){
				unset($this->backend[$key]);
			}
		}
		
		return $this;
	}
	
	/**
	 * @param null $limit
	 * @param null $offset
	 * @param null|string $query
	 * @return array
	 */
	public function toArray($limit = null, $offset = null, $query = null){
		if($this->backend instanceof SettingsRegistry){
			$collection = $this->backend->toArray($limit, $offset, $query);
		}else{
			$collection = [];
			$array = $this->backend;$count = 0;$i = 0;
			foreach($array as $key => $value){
				if($offset != null && $i < $offset){
					$i++;
					continue;
				}
				
				if(!is_string($query) || mb_stripos($value, $query) || mb_stripos($key, $query)){
					$collection[$key] = $value;
					$count++;
					$i++;
				}
				
				if($limit!==null && $count >= $limit){
					break;
				}
			}
		}
		
		foreach($this->schema->settings as $key => $setting){
			$value = array_key_exists($key,$collection)? $collection[$key] : $setting->getDefaultSettingValue($key, $this) ;
			$this->settings[$key] = $this->_afterSettingFetch($key, $value);
			$this->state[$key]    = true;
		}
		
		return $collection;
	}
	
	protected function _afterSettingFetch($key, $value){
		return $this->schema->afterSettingFetch($key, $value);
	}
	
	protected function _beforeSettingSet($key, $value){
		return $this->schema->beforeSettingSet($key, $value);
	}
	
	protected function caseNotDefinedSetting($key){
		$this->schema->caseNotDefinedSetting($key, $this);
	}
}


