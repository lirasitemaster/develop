<?php defined('isENGINE') or die;
$module -> table = dataloadcsv($module -> path . DS . 'data' . DS . $module -> param);
$module -> data = $module -> table -> data;
$module -> settings = $module -> table -> settings;
unset($module -> table);
?>