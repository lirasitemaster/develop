<?php defined('isCMS') or die;

/* ФУНКЦИЯ ПОЛУЧЕНИЯ РЕАЛЬНОГО IP-АДРЕСА ПОСЕТИТЕЛЯ */

function ipReal() {
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	} elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	}
	return $ip;
}

/* ФУНКЦИЯ ПРОВЕРКИ IP НА ПРИСУТСТВИЕ В ЗАДАННОМ ДИАПАЗОНЕ */

function ipRange($ip, $ip_base) {
	
	$ip = ipConvert($ip);
	if (empty($ip) || empty($ip_base) || !is_array($ip_base)) { return false; }
	
	foreach ($ip_base as $item) {
		
		if (strpos($item, '.') !== false && strpos($item, ':') === false) {
			$ip_type = '4';
		} elseif (strpos($item, '.') === false && strpos($item, ':') !== false) {
			$ip_type = '6';
		}
		
		if (strpos($item, '-') !== false) {
			$item = explode('-', $item);
			$ip_min = ipConvert($item[0], $ip_type);
			$ip_max = ipConvert($item[1], $ip_type);
		} elseif (strpos($item, '*') !== false) {
			$ip_min = ipConvert(str_replace('*', '0', $item), $ip_type);
			$ip_max = ipConvert(str_replace('*', '255', $item), $ip_type);
		} elseif (strpos($item, '/') !== false) {
			if ($ip_type == '4') {
				$item = ipConvertCIDR4($item);
				$ip_min = $item[0];
				$ip_max = $item[1];
			} elseif ($ip_type == '6') {
				$item = ipConvertCIDR6($item);
				$ip_min = $item[0];
				$ip_max = $item[1];				
			}
		} else {
			$ip_min = ipConvert($item, $ip_type);
			$ip_max = ipConvert($item, $ip_type);
		}
		
		if (
			$ip_min && $ip_max &&
			$ip_min <= $ip && $ip <= $ip_max
		) {
			return true;
		}
		
	}
	
	unset($item, $ip_base, $ip_type, $ip);
	return false;
	
}

/* СЛУЖЕБНАЯ ФУНКЦИЯ ПРЕОБРАЗОВАНИЯ IP АДРЕСА */

function ipConvert($ip, $ip_type = null) {
	
	$ip = trim($ip);
	
	if (preg_match('/00|\s/', $ip)) {
		$ip = preg_replace('/\s+/', '', $ip);
		$ip = preg_replace('/(\.|\:)(?=\.|\:)/', '$1_', $ip);
		$ip = preg_replace('/_/', '0', $ip);
		$ip = preg_replace('/(\.|\:|^)[0]{1,3}(\w)/', '$1$2', $ip);
	}
	
	if (!$ip_type) {
		if (strpos($ip, '.') !== false && strpos($ip, ':') === false) {
			$ip_type = 'A4';
		} elseif (strpos($ip, '.') === false && strpos($ip, ':') !== false) {
			$ip_type = 'A16';
		}
	} else {
		$ip_type = 'A' . ($ip_type == '6' ? '16' : '4');
	}
	
	if (!empty($ip_type)) {
		return current( unpack( $ip_type, inet_pton( $ip ) ) );
	} else {
		return false;
	}
	
}

/* СЛУЖЕБНАЯ ФУНКЦИЯ ПРЕОБРАЗОВАНИЯ IP АДРЕСА ИЗ ФОРМАТА CIDR ДЛЯ IPv4 */

function ipConvertCIDR4($ipv4) {
	
	if ($ip = strpos($ipv4,'/')) {
		$n_ip = (1<<(32-substr($ipv4,1+$ip)))-1;
		$ip_dec = ip2long(substr($ipv4,0,$ip));
	}
	
	$ip_min = $ip_dec &~ $n_ip;
	$ip_max = $ip_min + $n_ip;
	
	return [
		current( unpack( 'A4', inet_pton( long2ip($ip_min) ) ) ),
		current( unpack( 'A4', inet_pton( long2ip($ip_max) ) ) )
	];
	
}

/* СЛУЖЕБНАЯ ФУНКЦИЯ ПРЕОБРАЗОВАНИЯ IP АДРЕСА ИЗ ФОРМАТА CIDR ДЛЯ IPv6 */

function ipConvertCIDR6($ip) {
	
    if(!preg_match('~^([0-9a-f:]+)[[:punct:]]([0-9]+)$~i', trim($ip), $v_Slices)){
        return false;
    }
    if(!filter_var($v_FirstAddress = $v_Slices[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
        return false;
    }
	
    $v_PrefixLength = intval($v_Slices[2]);
    if($v_PrefixLength > 128){
        return false;
    }
	
    $v_SuffixLength = 128 - $v_PrefixLength;
	
    $v_FirstAddressBin = inet_pton($v_FirstAddress);
	
    $v_NetworkMaskHex = str_repeat('1', $v_PrefixLength) . str_repeat('0', $v_SuffixLength);
    $v_NetworkMaskHex_parts = str_split($v_NetworkMaskHex, 8);
    foreach($v_NetworkMaskHex_parts as &$v_NetworkMaskHex_part){
        $v_NetworkMaskHex_part = base_convert($v_NetworkMaskHex_part, 2, 16);
        $v_NetworkMaskHex_part = str_pad($v_NetworkMaskHex_part, 2, '0', STR_PAD_LEFT);
    }
    $v_NetworkMaskHex = implode(null, $v_NetworkMaskHex_parts);
    $v_NetworkMaskBin = inet_pton(implode(':', str_split($v_NetworkMaskHex, 4)));
	
    $v_LastAddressBin = $v_FirstAddressBin | ~$v_NetworkMaskBin;
	
    return [$v_FirstAddressBin, $v_LastAddressBin];
	
}

?>