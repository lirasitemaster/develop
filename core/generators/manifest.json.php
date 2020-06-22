<?php defined('isENGINE') or die;

// initial

init('init', 'fast');

// uri

$uri -> url = $uri -> scheme . '://' . $uri -> host . $uri -> path -> base;

// webapp

$template = dbUse('templates:' . (!empty($_GET['template']) ? $_GET['template'] : 'default'), 'select', true);
if (!empty($template)) {
	$template = array_shift($template);
	$webapp = $template['webapp'];
} else {
	$webapp = null;
}
unset($template);

// lang

global $lang;
$clang = !empty($_GET['language']) ? $_GET['language'] : $lang -> lang;
unset($lang);
$lang = dbUse(
	'languages:' . $clang,
	'select',
	['allow' => 'parent:' . $clang, 'deny' => !empty($_GET['template']) ? null : 'type']
);
unset($clang);
if (objectIs($lang)) {
	if (!objectKeys($lang)) {
		$nlang = [];
		foreach ($lang as $item) {
			$nlang = array_replace_recursive($nlang, $item);
		}
		$lang = $nlang;
		unset($nlang, $item);
	} else {
		$lang = array_shift($lang);
	}
	$lang = $lang['data'];
} else {
	$lang = null;
}

// icons

$icons = dbUse('icons', 'select', true);

//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($uri, 1) . '</pre><hr><br>';
//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($webapp, 1) . '</pre><hr><br>';
//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($lang, 1) . '</pre><hr><br>';
//echo '<pre style="font-size: 10px; line-height: 0.8em;">' . print_r($icons, 1) . '</pre><hr><br>';

$json = [
	'name' => html_entity_decode(!empty($webapp['name']) ? $webapp['name'] : $lang['title']),
	'short_name' => html_entity_decode(!empty($webapp['short_name']) ? $webapp['short_name'] : $lang['title']),
	'description' => html_entity_decode(!empty($webapp['description']) ? $webapp['description'] : $lang['slogan']),
	'theme_color' => $webapp['color'],
	'background_color' => $webapp['background'],
	'display' => !empty($webapp['display']) ? $webapp['display'] : 'standalone',
	'start_url' => !empty($webapp['start_url']) ? $webapp['start_url'] : '/'
	//'start_url' => $uri -> url
];

foreach (['splashscreen', 'webapp'] as $key) {
	if (!empty($icons[$key])) {
		foreach ($icons[$key]['sizes'] as $item) {
			$item = strpos($item, ':') !== false ? str_replace(':', 'x', $item) : $item . 'x' . $item;
			$json['icons'][] = [
				'src' => '/' . URL_LOCAL . $icons['settings']['path'] . '/' . $icons[$key]['name'] . '-' . $item . '.png',
				'type' => 'image/png',
				'sizes' => $item
			];
		}
		unset($item);
	}
}

header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
header('Content-type: application/json; charset=utf-8');
echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

unset(
	$json,
	$uri,
	$webapp,
	$lang,
	$icons
);

exit;

?>