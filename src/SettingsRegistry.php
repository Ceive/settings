<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */

namespace Ceive\settings;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class SettingsRegistry
 * @package Ceive\Frontend
 */
interface SettingsRegistry{
	
	
	/**
	 * @param array|string $key
	 */
	public function get($key);
	
	/**
	 * @param array|string $key
	 * @param $value
	 */
	public function set($key, $value = null);
	
	/**
	 * @param array|string $key
	 */
	public function has($key);
	
	/**
	 * @param array|string $key
	 */
	public function delete($key);
	
	/**
	 * @return array
	 */
	public function toArray();
	
}


