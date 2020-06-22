<?php defined('isCMS') or die;

global $uri;

global
	$calc_folder,
	$callback,
	$fileinfo,
	$hide_Cols,
	$lang,
	$path,
	$temp_file;

require $module -> process . 'functions.php';
require $module -> process . 'classes.php';
require $module -> process . 'templates.php';

require $module -> process . 'first.php';
require $module -> process . 'actions_first.php';
require $module -> process . 'second.php';

//require $module -> process . 'actions_second.php';
//'template:default.php';


/*

require $module -> process . 'functions.php';
require $module -> process . 'class.php';
require $module -> process . 'template.php';

require $module -> process . 'first.php';
require $module -> process . 'actions_fisrt.php';
require $module -> process . 'second.php';
require $module -> process . 'actions_second.php';

// в конце концов переименовать это в шаблон
require $module -> process . 'display.php';
*/

?>