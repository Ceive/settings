<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings\entity;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class SettingTypes
 * @package Ceive\Frontend
 */
class SettingTypes{
	
	public static $settingsTypes = [];
	
	/**
	 * @param Setting $setting
	 * @param array|null $settingTypes
	 * @return bool
	 */
	public static function acceptVisitFromSetting(Setting $setting, array $settingTypes = null){
		$type = $setting->type;
		if($settingTypes == null){
			$settingTypes = self::$settingsTypes;
		}
		if(isset($settingTypes[$type])){
			$setting->apply($settingTypes[$type], true);
			return true;
		}
		return false;
	}
}


