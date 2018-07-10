<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings\entity;

use Ceive\settings\SettingsAbstract;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Setting
 * @package Ceive\Frontend
 */
class Setting{
	
	const TYPE_STRING   = 'string';//null
	const TYPE_INT      = 'integer';
	const TYPE_DOUBLE   = 'double';
	const TYPE_BOOL     = 'boolean';
	
	public $type;
	
	public $default;
	
	public function __construct(array $props = null){
		$this->apply((array)$props);
	}
	
	public function beforeSave($value){
		return (string) $value;
	}
	
	public function afterFetch($value){
		switch($this->type){
			case self::TYPE_INT:
				return (integer) $value;
				break;
			case self::TYPE_BOOL:
				return (boolean) $value;
				break;
			case self::TYPE_DOUBLE:
				return (double) $value;
				break;
			default:
				return (string) $value;
				break;
		}
	}
	
	
	public function apply(array $props, $ifNotExists = false){
		foreach((array)$props as $key=>$value){
			if(!$ifNotExists || !property_exists($this, $key)){
				$this->{$key} = $value;
			}
		}
	}
	
	public function getDefaultSettingValue($key, SettingsAbstract $settings){
		
		if(is_callable($this->default)){
			return call_user_func($this->default, $key, $settings);
		}
		
		return $this->default;
	}
}


