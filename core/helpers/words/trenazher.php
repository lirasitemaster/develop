<?php

$name = 'kozlyata';
$name = 'kolobok';
$name = 'teremok';
$name = 'masha';

$hard = 0;
$html = 1;
$type = 'peremeshka';

$type = 'propusk';
$insert = '..';

/* ====================================== */

$text = file_get_contents(__DIR__ . DS . $name . '.txt');

$oldtext = str_replace(
	['—'],
	['-'],
$text);

$abz = datasplit($text, "\r\n");

foreach ($abz as &$item) {
	
	$words = datasplit($item, ' ');
	
	foreach ($words as &$i) {
		
		if (mb_strpos($i, '-') !== false) {
			
			$doubleword = datasplit($i, '-');
			
			foreach ($doubleword as &$ii) {
				$ii = funcPropusk($ii, $type, $insert, $hard);
			}
			
			$i = objectToString($doubleword, '-');
			
		} else {
			
			$i = funcPropusk($i, $type, $insert, $hard);
			
		}
		
	}
	
	$item = objectToString($words, ' ');
	
}

if ($html) {
	$newtext = '<p>' . objectToString($abz, '</p><p>') . '</p>';
	echo '<hr>' . $newtext . '<hr>';
} else {
	$newtext = objectToString($abz, "\r\n");
	echo '<pre>' . $newtext . '</pre>';
}

exit;

/* ====================================== */

function funcPropusk($word, $type, $insert = '.', $hard = false) {
	
	$count = mb_strlen($word);
	$sym = 0;
	
	if (
		mb_strpos($word, '.') ||
		mb_strpos($word, ',') ||
		mb_strpos($word, '!') ||
		mb_strpos($word, '?') ||
		mb_strpos($word, ':') ||
		mb_strpos($word, ';') ||
		mb_strpos($word, '"')
	) {
		$count--;
		$sym = 1;
	}
	
	if ($count > 4) {
		
		if ($type === 'propusk') {
			
			$zamena = $count % 2 ? 1 : 2;
			
			if ($hard && $count > 8) {
				$zamena = $zamena + 2;
			}
			
			$middle = str_repeat($insert, $zamena);
			$letters = ($count - $zamena) / 2;
			$word = mb_substr($word, 0, $letters) . $middle . mb_substr($word, (0 - $letters - $sym));
			
		} elseif ($type === 'peremeshka') {
			
			$zamena = $count % 2 ? 1 : 2;
			
			if ($hard) {
				$zamena = $zamena + 2;
			}
			
			$letters = ($count - $zamena) / 2;
			$prev = mb_substr($word, 0, $letters);
			$next = mb_substr($word, (0 - $letters - $sym));
			
			$middle = mb_substr($word, $letters, $zamena);
			$middle = funcShuffle($middle, mb_substr($prev, -1), mb_substr($next, 0, 1));
			
			$letters = ($count - $zamena) / 2;
			$word = $prev . $middle . $next;
			
			/*
			$peremeshka = preg_split('//u', $middle, -1, PREG_SPLIT_NO_EMPTY);
			shuffle($peremeshka);
			$middle = objectToString($peremeshka, '');
			*/
			
		}
		
		
	}
	
	return $word;
	
}

function funcShuffle($middle, $prev = '', $next = '') {
	
	$peremeshka = preg_split('//u', $middle, -1, PREG_SPLIT_NO_EMPTY);
	shuffle($peremeshka);
	$middle = objectToString($peremeshka, '');
	
	if (preg_match('/(\w)\1+/u', $prev . $middle . $next)) {
		$middle = funcShuffle($middle, $prev, $next);
	}
	
	return $middle;
	
}


?>