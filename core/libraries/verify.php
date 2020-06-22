<?php defined('isCMS') or die;

global $libraries;
//print_r($libraries);

if (!empty($libraries -> empty)) {
	
	if (
		DEFAULT_MODE === 'develop' &&
		SECURE_BLOCKIP === 'developlist' &&
		defined('CORE_COMPOSER') &&
		CORE_COMPOSER
	) {
		
		// здесь мы запускаем функцию по генерации данных
		// из файлов bower/composer/package.json
		
		init('libraries', 'functions');
		init('libraries', 'composer');
		
	} else {
		
		// а здесь мы сперва собираем данные, а затем выводим ошибку для всех сразу
		
		foreach ($libraries -> empty as &$item) {
			if (objectIs($item)) {
				$ii = null;
				foreach ($item as $i) {
					$ii = substr($i, 0, strpos($i, ':'));
				}
				$item = $ii;
				unset($i, $ii);
			} else {
				$item = substr($item, 0, strpos($item, ':'));
			}
		}
		error('403', false, 'libraries ' . objectToString($libraries -> empty, ', ') . ' are not found - enable composer');
		
	}
	
}

?>