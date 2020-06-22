<?php defined('isENGINE') or die;

/*
в вашем распоряжении есть:
$name, $item, $label, $class, $tpl

$name = dataParse('extreme:first');
$class = $module -> settings['navigation']['classes'];
$label = $module -> settings['navigation']['labels'];
$data = $module -> data['navigation'];
$tpl = file_exists($path -> current . 'navigation_template.php') ? $path -> current . 'navigation_template.php' : $module -> elements . 'navigation_template.php';

	if ($name[0] === 'pages') :
$key = !empty($name[2]) ? $name[2] : null;
$page = !empty($name[1]) ? $name[1] : null;
$name = $name[0];
	else :
$key = $name[1];
$page = $label[$key];
$name = $name[0];
$item = $item[$name][$key];

*/

if (objectIs($page)) {
	$page = $page['name'];
} elseif (empty($page)) {
	$page = $key;
}

$tpl = '
	<a href="' . (is_null($item) ? 'javascript:void(0);' : $item) . '" class="' . $class['item'] . '">
		<span class="' . $class['wrapper'] . '">' . $page . '</span>
	</a>
';

?>