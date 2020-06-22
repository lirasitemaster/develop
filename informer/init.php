<?php defined('isENGINE') or die;

$data = [];

if (objectIs($module -> settings['data'])) {
	
	$time = time();
	
	foreach ($module -> settings['data'] as $item) {
		
		$item['date'] = dataParse($item['date']);
		if (objectIs($item['date'])) {
			
			$format = !empty($item['format']) ? $item['format'] : lang('datetime:format');
			$start = !empty($format) && !is_numeric($item['date'][0]) ? datadatetime($item['date'][0], $format, true) : (int) $item['date'][0];
			$final = !empty($format) && !is_numeric($item['date'][1]) ? datadatetime($item['date'][1], $format, true) : (int) $item['date'][1];
			
			if (empty($start) || !empty($start) && $start <= $time) {
				if (empty($final) || !empty($final) && $final >= $time) {
					$data[] = clear($item['content']);
				}
			}
			
			unset($format, $start, $final);
			
		} else {
			$data[] = clear($item['content']);
		}
		
		// функция преобразования даты в абсолютный формат из lang
		// или, если указан формат в параметре format, то из него
		
	}
	
	unset($item, $time);
	
}

if (
	empty($module -> settings['classes']) ||
	!empty($module -> settings['type']) && !in('libraries', 'jquery') && (
		$module -> settings['type'] === 'modal' && !in('libraries', 'fancybox') ||
		$module -> settings['type'] !== 'modal' && !in('libraries', $module -> settings['type'])
	)
) {
	unset($data);
}

// запись и проверка куки
// но только, если задан какой-нибудь тип, т.е. предполагается, что вывод будет через модальное окно
// иначе мы просто выводим инфоблок
// хотя это также может настраиваться по какой-нибудь опции, лучше оставить в автоматическом режиме

if (!empty($module -> settings['type']) && !empty($data)) {
	
	// parse time
	if (!empty($module -> settings['time'])) {
		$module -> settings['time'] = dataParseTime($module -> settings['time']);
	}
	
	// clear $data
	if (!empty(cookie('informer-' . $module -> param, true))) {
		unset($data);
	}
	
}

/*

Это простой модуль, который выводит одно сообщение или уведомление

Уведомление должно выводится либо текстом с оформлением,
либо модальным окном (всплывающим) через fancybox или средствами bootstrap/jqueryui

Также уведомление можно закрыть ДО следующего раза, записав флаг в куки,
чтобы оно не мешало при запуске каждой страницы

Уведомление требует список данных в базе данных
Сам текст уведомления

И еще пусть можно будет указывать даты запуска - так можно сделать несколько уведомлений на разный период времени

Еще пусть будет задержка старта, чтобы вылезало не сразу, а через несколько секунд после запуска страницы

*/

?>