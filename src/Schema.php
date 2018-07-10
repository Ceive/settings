<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings;
use Ceive\settings\entity\Section;
use Ceive\settings\entity\Setting;
use Ceive\settings\entity\SettingTypes;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Schema
 * @package Ceive\Frontend
 */
class Schema implements \ArrayAccess{
	
	public $types = [];
	/** @var Setting[] */
	public $settings = [];
	
	protected $settingsPerSections = [];
	/** @var array[] */
	protected $sections = [];
	
	
	/**
	 * @param $title
	 * @param $name
	 * @param array $settings
	 * @param null $settingsPrefix
	 */
	public function section($title, $name, array $settings = [], $settingsPrefix = null){
		foreach($settings as $settingKey => $setting){
			if($settingsPrefix){
				$settingKey = ($settingsPrefix===true?"{$name}.":$settingsPrefix) . $settingKey;
			}
			
			if(is_array($setting)){
				$this->setting($settingKey, $setting);
			}
			
			if(isset($this->settings[$settingKey])){
				$this->settingsPerSections[$settingKey] = $name;
				$this->sections[$name] = [
					'title' => $title,
					'settingsPrefix' => $settingsPrefix
				];
			}
			
		}
	}
	
	
	/**
	 * @param $name
	 * @param array $settingOptions
	 * @return Setting
	 */
	public function setting($name, array $settingOptions = []){
		$this->setSetting($name, $setting = $this->makeSetting($settingOptions));
		return $setting;
	}
	
	/**
	 * @param array $sections
	 * @param Schema $applyTo
	 * @return Schema
	 */
	public function extendWithSections(array $sections, Schema $applyTo = null){
		
		if(!$applyTo){
			/** @var Schema $applyTo */
			$applyTo = get_class($this);
			$applyTo = new $applyTo();
		}
		
		
		foreach($sections as $sectionKey => $settingsKeys){
			
			if(is_int($sectionKey) && is_string($settingsKeys)){
				$sectionKey = $settingsKeys;
				foreach($this->getSectionSettings($sectionKey) as $key => $setting){
					$applyTo->setting($key, $setting);
				}
			}elseif($settingsKeys === true || $settingsKeys === '*'){
				foreach($this->getSectionSettings($sectionKey) as $key => $setting){
					$applyTo->setting($key, $this->settings[$key]);
				}
			}else if(is_array($settingsKeys)){
				foreach($settingsKeys as $key){
					$applyTo->setting($key, $this->settings[$key]);
				}
			}else{
				continue;
			}
			
			$applyTo->section($sectionKey, $settingsKeys);
		}
		return $applyTo;
	}
	
	public function extendSettings(array $settingKeys, Schema $applyTo = null){
		
		if(!$applyTo){
			/** @var Schema $applyTo */
			$applyTo = get_class($this);
			$applyTo = new $applyTo();
		}
		
		foreach($settingKeys as $settingsKey){
			$applyTo->setting($settingsKey, $this->settings[$settingsKey]);
		}
		
		return $applyTo;
	}
	
	public function getSectionSettings($sectionKey){
		$settings = [];
		foreach($this->settingsPerSections as $settingKey => $section){
			if($section === $sectionKey){
				$settings[$settingKey] = $this->settings[$settingKey];
			}
		}
		return $settings;
	}
	
	/**
	 * @param $sectionKey
	 * @return Section
	 */
	public function getSection($sectionKey){
		
		if(isset($this->sections[$sectionKey])){
			$sectionObject = new Section();
			$sectionObject->title = $this->sections[$sectionKey]['title'];
			$sectionObject->settings = $this->getSectionSettings($sectionKey);
			
			return $sectionObject;
		}
		return null;
	}
	
	public function hasSetting($key){
		return isset($this->settings[$key]);
	}
	
	public function getSetting($key){
		return isset($this->settings[$key])?$this->settings[$key]:null;
	}
	
	public function setSetting($key, Setting $setting){
		$this->settings[$key] = $setting;
		return $this;
	}
	
	public function deleteSetting($key){
		unset($this->settings[$key]);
	}
	
	
	public function offsetExists($key){
		return isset($this->settings[$key]);
	}
	
	public function offsetGet($key){
		return isset($this->settings[$key])?$this->settings[$key]:null;
	}
	
	public function offsetSet($key, $value){
		$this->setSetting($key, $value);
	}
	
	public function offsetUnset($key){
		$this->deleteSetting($key);
	}
	
	
	protected function makeSetting(array $settingOptions){
		$setting = new Setting($settingOptions);
		
		if(!SettingTypes::acceptVisitFromSetting($setting, $this->types)){
			SettingTypes::acceptVisitFromSetting($setting);
		}
		
		return $setting;
	}
	
	
	public function afterSettingFetch($key, $value){
		$setting = $this->getSetting($key);
		if($setting && isset($setting->afterFetch) && is_callable($setting->afterFetch)){
			return call_user_func($setting->afterFetch, $value);
		}
		return $value;
	}
	
	public function beforeSettingSet($key, $value){
		$setting = $this->getSetting($key);
		if($setting && isset($setting->beforeSave) && is_callable($setting->beforeSave)){
			return call_user_func($setting->beforeSave, $value);
		}
		return $value;
	}
	
	public function caseNotDefinedSetting($key, Frontend $settings){
		//case for log not declared settings;
		// and monitoring for settings usage which not defined
	}
	
	
	/**
	 * @param callable $onSection(key, section, schema)
	 * @param callable $onSetting(key, setting, sectionKey, section, schema)
	 * @param callable|null $filter(key, setting, sectionKey, section, schema)
	 */
	public function map($onSetting, callable $onSection = null, callable $filter = null){
		foreach($this->sections as $key => $meta){
			$section = $this->getSection($key);
			if($section){
				
				$doSettings = function() use($section, $onSetting, $key, $filter){
					foreach($section->settings as $settingKey => $setting){
						if(!$filter || call_user_func($filter, $settingKey, $setting, $key, $section, $this)!==false){
							call_user_func($onSetting, $settingKey, $setting, $key, $section, $this);
						}
					}
				};
				if(!$onSection){
					$doSettings();
				}else{
					call_user_func($onSection, $doSettings,$key, $section, $this);
				}
				
			}
		}
	}
	
	/**
	 * @param object $mapperObject
	 */
	public function mapper($mapperObject){
		
		return $this->map(
			method_exists($mapperObject, 'onSetting')? [$mapperObject, 'onSetting']: property_exists($mapperObject,'onSetting')?$mapperObject->onSetting:null,
			method_exists($mapperObject, 'onSection')? [$mapperObject, 'onSection']: property_exists($mapperObject,'onSection')?$mapperObject->onSection:null,
			method_exists($mapperObject, 'filter')? [$mapperObject, 'filter']: property_exists($mapperObject,'filter')?$mapperObject->filter:null
		);
	}
}


