<?php defined('isCMS') or die;

if (empty($data)) {
	return;
}

$print = null;

foreach ($data as $key => $item) {
	$print .= '<div class="' . $module -> settings['classes']['main'] . '" style="display: none; max-width:600px;">';
	$print .= '<div class="' . $module -> settings['classes']['item'] . '">';
	$print .= $item . '</div>';
	$print .= '<button' . ($module -> settings['type'] === 'modal' ? ' data-fancybox-close' : null) . ' class="' . $module -> settings['classes']['button'] . '">' . $module -> settings['labels']['button'] . '</button>';
	$print .= '</div>';
}

unset($item, $key);

if (!empty($module -> settings['type'])) {
	
	$cookie = 'document.cookie = "informer-' . $module -> param . '=' . time() . '; path=/;' . set($module -> settings['time'], ' max-age=' . $module -> settings['time']) . '";';
	$print .= '<script type="text/javascript">';
	
	if (!empty($module -> settings['delay'])) { $print .= 'setTimeout(function(){'; }
	
	if ($module -> settings['type'] === 'modal') {
		$print .= '$.fancybox.open({
			src  : ".' . $module -> settings['classes']['main'] . '",
			type : "inline",
			opts : {
				modal: true,
				afterClose: function(instance, current) {
					' . $cookie . '
				}
			}
		})';
	}
	
	if (!empty($module -> settings['delay'])) { $print .= '}, ' . (int) $module -> settings['delay'] . '000)'; }
	
	$print .= ';</script>';
	
}

echo $print;

?>