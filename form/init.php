<?php defined('isCMS') or die;

// НУЖНО ОПРЕДЕЛИТЬ РАЗДЕЛ НАСТРОЕК С ПРЕДОПРЕДЕЛЕННЫМИ ПОЛЯМИ
// ИЛИ ДОБАВЛЯТЬ ЭТИ ПОЛЯ В ФОРМУ
// TYPE / VALUE / CLASS / USER NOT READ
// МЕСТА: BEFORE / AFTER
// если указана строка, будет подгружаться файл, если указан массив данных, он будет добавлен в форму
// список заказов отправлять в виде данных json и не в данных process -> data, а в process -> source
// а для корзины заказов сделать пометку внимание! не старайтесь подделать данные заказа - если вы поменяете значения пересылаемых данных, ваш заказ будет пересчитан заново

// здесь нужно определить назначение формы:
// $module -> settings['type'] . ':' . $module -> param
// или наоборот
// например, исходные:
// feedback:form
// reservation:form
// authorisation:authorisation
// content:form
// exit:authorisation
// registration:registration

// например, сейчас некоторые процессы выглядят так:
// user:authorise
// user:register
// content:filter
// content:form
// content:rating
// system:captcha
// content:global
// system:cookiesagree
// system:write

// еще нужно определить, как будет происходить авторизация, выход и другие элементы
// и как через форму можно задавать и управлять другими процессами
// например так:
// $module -> settings['type'] > $module -> settings['process']
// "type" : "name" > "process" : "one:two"

// в стандартном процессе должна отправляться форма с уведомлением
// администратора и отправителя о том, что форма принята
// и с данными, преобразованными через шаблон

global $uri;

$module -> status = set($uri -> query -> array['status'], true);
$module -> data = $uri -> query -> array['data'];
$module -> var['errors'] = objectIs($uri -> query -> array['errors']) ? array_keys($uri -> query -> array['errors']) : [];

if (
	!empty($module -> status) &&
	$module -> status !== 'complete'
) {
	$module -> var['errors']['fail'] = true;
}

//$module -> var['verification'] = '';

// new code -= vvv =-

$module -> var['base'] = objectProcess(
	!empty($module -> settings['process']) ? $module -> settings['process'] : 'form.iscms:submit',
	!empty($module -> settings['time']) && is_numeric($module -> settings['time']) ? $module -> settings['time'] : null
);

require $module -> process . 'functions.php';
foreach ($sets['form'] as &$item) {
	$item['options'] = moduleFormOptionsGenerator($item['options']);
	if (!empty($item['options_title'])) {
		$item['options'] = array_merge([' ' => $item['options_title']], $item['options']);
	}
	if (!empty($sets['required'])) {
		$item['required'] = $sets['required'];
	}
}
unset($item);

// new code -= ^^^ =-

if (!empty($module -> settings['captcha']['enable'])) {
	require $module -> process . 'captcha.php';
}
	
?>