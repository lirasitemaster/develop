<?php defined('isENGINE') or die;

global $user;
global $userstable;
global $verification;

$field = dbUse($userstable, 'filter', ['filter' => 'system:password']);

$arr = [];
$result = [];

foreach ($field as $item) {
	
	$arr = [
		'name' => $item['name'],
		'crypt' => $item['data']['crypt'],
		'value' => $user -> data[$item['name']],
		'verify' => $_POST[$item['name']],
	];
	
	if (!$arr['crypt']) {
		
		// если не закриптован
		if ($arr['verify'] === $arr['value']) {
			$result[] = true;
		} else {
			$result[] = false;
		}
		
	} elseif ($arr['crypt'] === 'password') {
		
		// если закриптован как пароль
		$result[] = password_verify($arr['verify'], $arr['value']);
		
	} else {
		
		// если закриптован через обычную функцию
		$arr['verify'] = crypting($arr['verify']);
		if ($arr['verify'] === $arr['value']) {
			$result[] = true;
		} else {
			$result[] = false;
		}
		
	}
	
	//print_r($arr);
	//echo '<br><br>';
	
}

unset($item, $arr, $field);

$verification = set($result);

?>