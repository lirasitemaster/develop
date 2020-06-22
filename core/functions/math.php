<?php defined('isENGINE') or die;

/* МАТЕМАТИЧЕСКИЕ ФУНКЦИИ */

function mathLastNumber($count, $max, $first = 0) {

	/*
	*  функция вычисления остатка и последних элементов, превышающих допустимое число столбцов
	*  задаем:
	*    число элементов
	*    макс число элементов в столбце
	*    необязательно: номер первого элемента (по-умолчанию, ноль)
	*/
	
	if ($count <= $max) {
		return false;
	}
	
	$math = [];
	
	$math['count'] = $count;
	$math['ostatok'] = $math['count'] % $max;
	$math['last'] = $math['count'] + $first - $math['ostatok'] - 1;
	
	return($math);
	
}

function mathFindDivider ($num, $max, $min = 1) {
	
	/*
	*  функция находит наибольший целый делитель числа
	*  задаем:
	*    число
	*    максимальный делитель для проверки
	*    минимальный делитель для проверки
	*  
	*  если наибольший целый делитель не найден,
	*  функция возвращает максимальное число
	*  
	*  функция нужна, например, чтобы определить оптимальное число колонок для элементов
	*  если колонок от 2 до 4, то для 9 лучше 3, для 8 - 4, для 6 - 3, для 4 - 4, для 3 - 3
	*/
	
	if ($num <= $min) {
		return $min;
	} elseif ($num <= $max) {
		return $num;
	}
	
	$c = $max;
	
	for ($n = $max; $num % $max !== 0 && $max > $min; $max--) {
		$n = $max - 1;
	}
	
	if ($n === $min && $num % $max !== 0) {
		$n = $c;
	}
	
	return $n;
	
}

function mathRandom ($str = false, $len = false) {
	
	/*
	*  генератор случайной строки
	*  задаем:
	*    набор символов (или массив с символами от - до)
	*    длину строки (или случайно с числами от - до)
	*/
	
	if (!$str || (!is_string($str) && !is_array($str))) {
		$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	} elseif (is_array($str)) {
		$str[2] = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str[3] = strpos($str[2], $str[0]);
		$str[4] = strpos($str[2], $str[1]);
		$str = substr($str[2], $str[3], $str[4]);
	}
	
	if (!$len || (!is_numeric($len) && !is_array($len))) {
		$len = 6;
	} elseif (is_array($len)) {
		$len = mt_rand($len[0], $len[1]);
	}
	
    $strlen = strlen($str);
    $return = '';
    for ($i = 0; $i < $len; $i++) {
        $return .= $str[rand(0, $strlen - 1)];
    }
    return $return;
	
}

?>