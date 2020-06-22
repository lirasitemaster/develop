<?php defined('isENGINE') or die;

/*

Переменная 'name' уже предварительно задана в таком виде:
	$name = $item['name'];
	
Однако вы можете переназначить имя в этом файле.

Также содержимое 'content' и 'captions' кодируется и декодируется, т.к. оно передается между функциями системы
и обрабатывается непосредственно перед выводом. По-умолчанию используется пара функций 'htmlentities'/'html_entity_decode',
но вполне возможно, что в дальнейшем механизм будет изменен, например на 'base64' или системный 'crypting'.

Кроме того, переменная 'name' автоматически очищается:
	unset($name);

Для корректной работы вам остается только заполнить массив $media со следующими подмассивами:

$media['list'][] = $name;

$media['captions'][$name] = fieldname || 'caption';
or
$media['captions'][$name] = [
	'full' => value,
	'default' => value,
	'seo' => value
];

$media['content'][$name] = fieldname || array_merge(fieldname, fieldname) || 'content';

Обратите внимание, что подмассивы 'captions' и 'content' должны быть обязательно ассоциированными. Для 'list' это неважно.

Вы также можете задать специальные классы и подписи для элементов навигации следующим образом:

$media['options']['labels']['dots'][] = fieldname || 'content';
$media['options']['classes']['dots'][] = fieldname || 'content';

*/

?>