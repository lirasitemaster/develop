<?php defined('isENGINE') or die;

$url = 'http://www.cbr.ru/scripts/XML_daily.asp';
$module -> data = [];

$folder = PATH_CUSTOM . 'modules' . DS . $module -> name . DS . 'temporary' . DS;
$file = $folder . datadatetime('', $module -> settings['savestate']) . '.ini';

if (file_exists($file)) {
	$xmlstr = localFile($file);
} else {
	$xmlstr = localRequestUrl($url, null, 'post');
	//$xmlstr = localOpenUrl($url);
	if (!empty($xmlstr)) {
		if (!file_exists(PATH_CUSTOM . 'modules')) { mkdir(PATH_CUSTOM . 'modules'); }
		if (!file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name)) { mkdir(PATH_CUSTOM . 'modules' . DS . $module -> name); }
		if (!file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . 'temporary')) { mkdir(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . 'temporary'); }
		
		$list = localList($folder, ['return' => 'files']);
		if (objectIs($list)) {
			foreach ($list as $i) {
				unlink($folder . $i);
			}
			unset($i);
		}
		unset($list);
		
		file_put_contents($file, $xmlstr);
	}
}
unset($file, $url);

if (!empty($xmlstr)) {
	$valcurs = new SimpleXMLElement($xmlstr);
	foreach ($valcurs as $item) {
		$item = (array) $item;
		$val = strtolower($item['CharCode']);
		if (in_array($val, $module -> settings['currencies'])) {
			$module -> data[$val] = ceil(str_replace(',', '.', $item['Value']) * 100) / 100;
		}
		unset($val);
	}
	unset($valcurs, $item, $val);
}

unset($xmlstr);

?>