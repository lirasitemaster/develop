<?php defined('isENGINE') or die;

$folder = PATH_CUSTOM . 'modules' . DS . $module -> name . DS . 'temporary' . DS;
$file = $folder . datadatetime('', $module -> settings['savestate']) . '.' . $module -> param . '.ini';

if (!empty($module -> settings['savestate']) && file_exists($file)) {
	$result = localFile($file);
} else {
	
	if ($module -> settings['api'] === 'vk') {
		
		// для api вконтакте
		
		$result = file_get_contents('https://api.vk.com/method/wall.get', false, stream_context_create(array(
			'http' => array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query(
					array(
						'owner_id' => $module -> settings['id'],
						'count' => !empty($module -> settings['limit']) ? (int) $module -> settings['limit'] : null,
						'access_token' => $module -> settings['key'],
						'v' => '5.85'
					)
				)
			)
		)));
		
	} elseif ($module -> settings['api'] === 'instagram') {
		
		// для api instagram
		
		if (empty($module -> settings['id'])) {
			$module -> settings['id'] = substr($module -> settings['key'], 0, strpos($module -> settings['key'], '.'));
		}
		
		$result = localRequestUrl("https://api.instagram.com/v1/users/" . $module -> settings['id'] . "/media/recent?access_token=" . $module -> settings['key'], null, 'curl');
		
	}
	
	if (!empty($result) && !empty($module -> settings['savestate'])) {
		if (!file_exists(PATH_CUSTOM . 'modules')) { mkdir(PATH_CUSTOM . 'modules'); }
		if (!file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name)) { mkdir(PATH_CUSTOM . 'modules' . DS . $module -> name); }
		if (!file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . 'temporary')) { mkdir(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . 'temporary'); }
		
		$list = localList($folder, ['return' => 'files', 'mask' => $module -> param]);
		if (objectIs($list)) {
			foreach ($list as $i) {
				unlink($folder . $i);
			}
			unset($i);
		}
		unset($list);
		
		file_put_contents($file, $result);
	}
	
}
unset($file, $folder);

$result = json_decode($result);

?>