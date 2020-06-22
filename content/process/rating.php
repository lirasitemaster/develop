<?php defined('isCMS') or die;

$content -> ratingPrepare(array_keys($content -> data));

//global $user;
//$r = $user;
//$r = $content -> ratings;
//$r = array_keys($r);
//echo '<br>{{' . print_r($r, true) . '}}<br>';
//echo '<br><br>';

if ($content -> type === 'alone') {
	$t = ['views', 'display'];
} else {
	$t = 'display';
}

$content -> ratingAdd($t, 1, true);

//$r = $content -> ratings;
//$r = array_keys($r);
//echo '<br>{{' . print_r($r, true) . '}}<br>';
//echo '<br><br>';

unset($t);

// фильтрация рейтингов

$content -> ratingPrepare(array_keys($content -> data), true);

//echo '<br><br>';
//print_r($content -> ratings);

//ratingAdd($type);
//'views', 'display'

//echo '<pre>' . print_r($content -> ratings, true) . '</pre><hr>';

?>