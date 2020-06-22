<?php defined('isENGINE') or die;

global $uri;

/*
if (empty($_SESSION['fm_root_url']) || нет пользователя) {
	сброс
}
*/

$p = realpath(__DIR__ . DS . DP . DP) . DS;
$module = (object) [
	'process' => $p . 'process' . DS,
	'elements' => $p . 'elements' . DS
];
unset($p);

require $module -> process . 'functions.php';
require $module -> process . 'classes.php';
require $module -> process . 'templates.php';

require $module -> process . 'first.php';

require $module -> process . 'actions_first.php';
require $module -> process . 'actions_second.php';

//echo '<pre>' . print_r($_FILES, 1) . '</pre><br>';
//echo '<pre>' . print_r($process, 1) . '</pre><br>';

exit;

?>