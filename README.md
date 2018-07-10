Abstract Layer for settings on your project
===========================================

Пакет обеспечивает абстракцию доступа к настройкам, позволяет оперется на него для получения настроек в вашем приложении и задуман чтобы быть адаптированным под любой стек рабочего окружения на php

Фронтенд `Ceive\settings\Frontend` обеспечивает возможность кеширования настроек, и требует реализации доступа к настройкам через бакенд.
`$settings->state`Массив(\ArrayAccess): Обеспечивается ленивая загрузка настроек из бакенда (то есть то что было загружено больше не загружается)
`$settings->settings`Массив(\ArrayAccess): свойство в котором хранятся загруженные из бакенда настройки, можно использовать как кеш для настроек реализовав его путем реализации интерфейса \ArrayAccess
`$settings->backend`: Если есть фронтенд, значит есть бакенд. Бакенд обеспечивает слой релизации доступа к настройкам где бы они не были.


```
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
```


Smart defaults(Yii integration example):
```
$schema = new settings\Schema();
$schema->setting('mySettingKey', [
    'type' => 'string',
    'default' => function($key, settings\SettingsAbstract $settings){
        return Yii::$app->settings->has($key)
            ?Yii::$app->settings->get($key)
            :Yii::$app->params['config'][$key]
    }
]);
```



transport an options through some settings:
```
$schema->setting('mySettingKey', [
    //...

    'myImportantOption' => 'value'

    //...
]);


////////////////////////////////////////////////////////////////////////
///// OTHER CODE


$setting = $schema->getSetting('mySettingKey');
$importantValue = $setting->myImportantOption;

```


example for transport an one option:
```
$schema->setting('mySettingKey', [
    //...
    'type' => 'string'
    'autocomplete' => [
        'val1' => 'String1',
        'val1' => 'String2',
        'val1' => 'String3',
        'val1' => 'String4',
    ]

    //...
]);


////////////////////////////////////////////////////////////////////////
///// OTHER CODE


$setting = $schema->getSetting('mySettingKey');
if( $autocomplete = $setting->autocomplete ){
    ?> <select>
    <? foreach($autocomplete as $value=>$title){ ?><option value="<?= $value?>"><?= $title?></option><? }?>

    </select> <?
}

```


schema`s settings mapping
```
$schema->map(, function($key, Setting $setting, $sectionKey, Section $section, Schema $schema){
    // onSetting
},function($doSettings, $sectionKey, Section $section, $schema){
    // onSection (optionaly: default null)
    // call $doSettings() here
}, function($key, Setting $setting, $sectionKey, Section $section, Schema $schema){
    // filter (optionaly: default null)
});
```

example for schema`s settings mapping
```
$schema->map(function($key, Setting $setting, $sectionKey, Section $section, Schema $schema){
    // onSetting
}, function($doSettings, $sectionKey, Section $section, Schema $schema){
    // onSection (abort if returned false)
    ?>
    <section>
        <div>
            <h1><?= $section->title?></h1>
        </div>
        <div><? $doSettings() ?></div>
    </section>
    <?
}, function($key, Setting $setting, $sectionKey, Section $section, Schema $schema){
    // filter optionaly
});
```


example for schema`s settings mapping and try use options transport through setting
```
$schema->map(function($key, Setting $setting, $sectionKey, Section $section, Schema $schema) use($settings){
    // onSetting
    if($setting->autocomplete){
        ?> <select name="<?=$key?>">
            <? foreach($autocomplete as $value=>$title){ ?><option value="<?= $value?>"><?= $title?></option><? }?>
        </select> <?
    }else{
        ?> <input type="<?=$setting->type?>" name="<?=$key?>" value="<?= $settings[$key]?>"/> <?
    }
}, null, null);
```

Or use mapper object
```

$mapper = new \stdClass();

//$mapper->onSection = null;
$mapper->onSetting = function($key, Setting $setting, $sectionKey, Section $section, Schema $schema) use($settings){
    // onSetting
    if($setting->autocomplete){
        ?> <select name="<?=$key?>">
            <? foreach($autocomplete as $value=>$title){ ?><option value="<?= $value?>"><?= $title?></option><? }?>
        </select> <?
    }else{
        ?> <input type="<?=$setting->type?>" name="<?=$key?>" value="<?= $settings[$key]?>"/> <?
    }
};
$mapper->filter = function(){};

$schema->mapper($mapper);
```