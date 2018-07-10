<?php
namespace Ceive\settings\tests;

use Ceive\settings;
use PHPUnit\Framework\TestCase;

/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.settings
 */
class SimpleTestCase extends TestCase{
	
	
	public function testA(){
		
		$schema = new settings\Schema();
		
		$schema->section('Общее', 'general', [
			'siteName' => [
				'type'      => 'string',
				'default'   => 'Example site',
			],
		], true);
		$schema->section('Настройки смс ведомлений', 'sms', [
			'notification.update' => [
				'type'      => 'string',
				'default'   => 'Example site',
			],
		]);
		
		$a = [
			'notification.update' => true
		];
		
		$settings = new settings\Frontend();
		$settings->schema = $schema;
		$settings->backend = new settings\backend\Customizable(
			function($key, $value) use(&$a){
				$a[$key] = $value;
			},//set
			function($key) use(&$a){
				return isset($a[$key])?$a[$key]:null;
			},//get
			function($key) use(&$a){
				return isset($a[$key]);
			},//has
			function($key) use(&$a){
				unset($a[$key]);
			},//delete
			function($limit=null, $offset=null, $query=null) use(&$a){
				return $a;
			}//list
		);
		
		$settings['notification.update'] = false; // set to settings
		
		$array = $settings->toArray();
		
		echo $settings['notification.update']; // use settings
	}
	
}


