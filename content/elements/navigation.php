<?php defined('isENGINE') or die;

// вы можете вызвать этот элемент в любом месте шаблона
// для этого достаточно переназначить настройки module -> settings['navigation']
// например, так:
// $module -> settings['navigation']['custom'] = ['navigation:previous', 'navigation:next'];					
// require $module -> elements . 'navigation.php';

// на самом деле, вся подготовка элементов происходит один раз при инициализации модуля,
// так что если вы хотите вызвать данный элемент много раз,
// вам не стоит беспокоиться за то, что он загрузит сервер и базу данных
// при его вызове будут лишь читаться настройки и выводиться ранее подготовленные данные

// - однако для его корректной работы необходимо, чтобы в настройках было прописано
// module -> settings['navigation']['enable'] = true
// - если вы не хотите выводить навигацию в шаблоне по-умолчанию, пропишите в настройках
// module -> settings['navigation']['position'] = null
// - используйте файл navigation_template.php для задания своего шаблона навигации
// - изменение настроек module -> settings['navigation']['labels'] ничего не даст,
// потому что они читаются один раз при инициализации навигации

// но вот если у вас не было инициализации, либо вы хотите задать ее повторно,
// вам нужно вызвать файл инициализации в шаблоне
// однако мы настоятельно не рекомендуем этого делать по двум причинам
// во-первых, это действительно загрузит сервер
// во-вторых, впоследствии с изменением модуля, может меняться и инициализация навигации
// и тогда, при обновлении, ваш шаблон выдаст ошибку

// задаем базовые параметры

$print = '';
$nav = $module -> settings['navigation'];
$data = $module -> data['navigation'];
$tpl = (file_exists($path -> current . 'navigation_template.php') ? $path -> current : $module -> elements) . 'navigation_template.php';

// создаем кнопки

if (empty($module -> settings['navigation']['custom'])) {
	$module -> settings['navigation']['custom'] = ['extreme:first', 'navigation:previous', 'pages', 'navigation:next', 'extreme:last', 'position:list', 'position:all'];
}

foreach ($module -> settings['navigation']['custom'] as $i) {
	if ($i === 'pages') {
		if (!empty($data['pages'])) {
			foreach ($data['pages'] as $nav_key => $nav_item) {
				$print .= funcModuleContent_navigation('pages:' . $nav_key, $nav_item, $nav, $tpl);
			}
			unset($nav_key, $nav_item);
		}
	} else {
		$print .= funcModuleContent_navigation($i, $data, $nav, $tpl);
	}
}
unset($i);

echo '<ul class="' . $module -> settings['navigation']['classes']['container'] . '">' . $print . '</ul>';
//echo '<style>.button { list-style: none; display: inline-block; margin: 0 10px; } .button_page--active a { color: red!important; } .button--disable a { color: grey!important; }</style>';

unset($label, $class, $data, $print);

//echo '<br><pre>' . print_r($module -> data['navigation'], true) . '</pre><br>';

?>