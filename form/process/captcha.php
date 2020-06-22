<?php defined('isCMS') or die;

$module -> var['token'] = mathRandom(null, 32);

$module -> var['allow'] = ['font', 'symbols', 'length', 'width', 'height', 'amplitude', 'waves', 'rotate', 'blacknoise', 'whitenoise', 'linenoise', 'lines', 'no_spaces', 'color', 'bgcolor'];
$module -> var['parameters'] = '';

foreach ($module -> settings['captcha'] as $key => $item) {
	if (
		!empty($item) &&
		(!is_array($item) || !is_object($item)) &&
		in_array($key, $module -> var['allow'])
	) {
		if (is_string($item)) {
			$item = str_replace(['<','>','(',')','{','}','[',']','?','&','=',';','"','\''], '', $item);
			$item = htmlentities($item);
			$item = mb_strtolower($item);
		}
		$module -> var['parameters'] .= '&data[' . $key . ']=' . clear($item, 'entities urlencode');
	}
}

$f = objectProcess('system:captcha');
$module -> var['captcha'] = $f['action'] . $f['string'] . $module -> var['parameters'] . '&data[token]=' . $module -> var['token'];
unset($f);

?>