<?php defined('isENGINE') or die;

$jsonstr = [];
$url = null;
$temp = null;

if (!objectIs($module -> settings['types'])) {
	$module -> settings['types'] = [$module -> settings['types']];
}

foreach ($module -> settings['types'] as $item) {
	
	$url = 'https://api.openweathermap.org/data/2.5/' . $item . '?' . (is_numeric($module -> settings['weather']) ? 'id=' : 'q=') . $module -> settings['weather'] . '&units=metric&lang=ru&appid=' . $module -> settings['key'];
	
	$folder = PATH_ASSETS . 'modules' . DS . $module -> name . DS . 'temporary' . DS;
	$file = $folder . datadatetime('', $module -> settings['savestate']) . '.' . $item . '.ini';
	
	if (file_exists($file)) {
		$temp = localFile($file);
	} else {
		$temp = localRequestUrl($url, null, 'post');
		//$temp = localOpenUrl($url);
		if (!empty($temp)) {
			if (!file_exists(PATH_ASSETS . 'modules')) { mkdir(PATH_ASSETS . 'modules'); }
			if (!file_exists(PATH_ASSETS . 'modules' . DS . $module -> name)) { mkdir(PATH_ASSETS . 'modules' . DS . $module -> name); }
			if (!file_exists(PATH_ASSETS . 'modules' . DS . $module -> name . DS . 'temporary')) { mkdir(PATH_ASSETS . 'modules' . DS . $module -> name . DS . 'temporary'); }
			
			$list = localList($folder, ['return' => 'files', 'mask' => $item]);
			if (objectIs($list)) {
				foreach ($list as $i) {
					unlink($folder . $i);
				}
				unset($i);
			}
			unset($list);
			
			file_put_contents($file, $temp);
		}
	}
	unset($file, $folder);
	
	if (!empty($temp)) {
		$jsonstr[$item] = json_decode($temp, true);
	}
	
}

unset($item, $url, $temp);

// primer
/*
$jsonstr = [
	'weather' => json_decode('{"coord":{"lon":43.06,"lat":44.05},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"base":"stations","main":{"temp":3.98,"feels_like":0.02,"temp_min":3.89,"temp_max":4,"pressure":1017,"humidity":80},"visibility":10000,"wind":{"speed":3,"deg":360},"clouds":{"all":90},"dt":1584619231,"sys":{"type":1,"id":8966,"country":"RU","sunrise":1584587548,"sunset":1584631107},"timezone":10800,"id":503550,"name":"Пятигорск","cod":200}', true),
	'forecast' => json_decode('{"cod":"200","message":0,"cnt":40,"list":[{"dt":1584630000,"main":{"temp":2.93,"feels_like":0.36,"temp_min":1.85,"temp_max":2.93,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":89,"temp_kf":1.08},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":100},"wind":{"speed":1.12,"deg":34},"sys":{"pod":"d"},"dt_txt":"2020-03-19 15:00:00"},{"dt":1584640800,"main":{"temp":2.29,"feels_like":0.24,"temp_min":1.48,"temp_max":2.29,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":92,"temp_kf":0.81},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":0.34,"deg":350},"sys":{"pod":"n"},"dt_txt":"2020-03-19 18:00:00"},{"dt":1584651600,"main":{"temp":1.3,"feels_like":-1.74,"temp_min":0.76,"temp_max":1.3,"pressure":1017,"sea_level":1017,"grnd_level":933,"humidity":90,"temp_kf":0.54},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04n"}],"clouds":{"all":81},"wind":{"speed":1.47,"deg":291},"sys":{"pod":"n"},"dt_txt":"2020-03-19 21:00:00"},{"dt":1584662400,"main":{"temp":0.69,"feels_like":-2.71,"temp_min":0.42,"temp_max":0.69,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":89,"temp_kf":0.27},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":86},"wind":{"speed":1.83,"deg":279},"sys":{"pod":"n"},"dt_txt":"2020-03-20 00:00:00"},{"dt":1584673200,"main":{"temp":-2.05,"feels_like":-3.82,"temp_min":-0.05,"temp_max":-0.05,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":89,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04n"}],"clouds":{"all":56},"wind":{"speed":2.23,"deg":258},"sys":{"pod":"n"},"dt_txt":"2020-03-20 03:00:00"},{"dt":1584684000,"main":{"temp":-0.05,"feels_like":0.23,"temp_min":3.85,"temp_max":3.85,"pressure":1018,"sea_level":1018,"grnd_level":935,"humidity":73,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03d"}],"clouds":{"all":29},"wind":{"speed":2.23,"deg":306},"sys":{"pod":"d"},"dt_txt":"2020-03-20 06:00:00"},{"dt":1584694800,"main":{"temp":6.5,"feels_like":2.89,"temp_min":6.5,"temp_max":6.5,"pressure":1017,"sea_level":1017,"grnd_level":934,"humidity":64,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03d"}],"clouds":{"all":39},"wind":{"speed":2.36,"deg":330},"sys":{"pod":"d"},"dt_txt":"2020-03-20 09:00:00"},{"dt":1584705600,"main":{"temp":6.65,"feels_like":2.77,"temp_min":6.65,"temp_max":6.65,"pressure":1016,"sea_level":1016,"grnd_level":933,"humidity":67,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04d"}],"clouds":{"all":68},"wind":{"speed":2.91,"deg":327},"sys":{"pod":"d"},"dt_txt":"2020-03-20 12:00:00"},{"dt":1584716400,"main":{"temp":5.55,"feels_like":2.02,"temp_min":5.55,"temp_max":5.55,"pressure":1017,"sea_level":1017,"grnd_level":934,"humidity":75,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":100},"wind":{"speed":2.53,"deg":313},"sys":{"pod":"d"},"dt_txt":"2020-03-20 15:00:00"},{"dt":1584727200,"main":{"temp":3.62,"feels_like":-0.16,"temp_min":3.62,"temp_max":3.62,"pressure":1019,"sea_level":1019,"grnd_level":936,"humidity":90,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":3.04,"deg":299},"sys":{"pod":"n"},"dt_txt":"2020-03-20 18:00:00"},{"dt":1584738000,"main":{"temp":3.95,"feels_like":0.39,"temp_min":3.95,"temp_max":3.95,"pressure":1019,"sea_level":1019,"grnd_level":935,"humidity":90,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":2.81,"deg":285},"sys":{"pod":"n"},"dt_txt":"2020-03-20 21:00:00"},{"dt":1584748800,"main":{"temp":3.4,"feels_like":0.67,"temp_min":3.4,"temp_max":3.4,"pressure":1019,"sea_level":1019,"grnd_level":935,"humidity":89,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":98},"wind":{"speed":1.45,"deg":276},"sys":{"pod":"n"},"dt_txt":"2020-03-21 00:00:00"},{"dt":1584759600,"main":{"temp":1.7,"feels_like":-1.14,"temp_min":1.7,"temp_max":1.7,"pressure":1020,"sea_level":1020,"grnd_level":936,"humidity":90,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03n"}],"clouds":{"all":38},"wind":{"speed":1.27,"deg":295},"sys":{"pod":"n"},"dt_txt":"2020-03-21 03:00:00"},{"dt":1584770400,"main":{"temp":5.05,"feels_like":2.52,"temp_min":5.05,"temp_max":5.05,"pressure":1019,"sea_level":1019,"grnd_level":936,"humidity":75,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03d"}],"clouds":{"all":41},"wind":{"speed":0.99,"deg":342},"sys":{"pod":"d"},"dt_txt":"2020-03-21 06:00:00"},{"dt":1584781200,"main":{"temp":7.46,"feels_like":4.16,"temp_min":7.46,"temp_max":7.46,"pressure":1019,"sea_level":1019,"grnd_level":936,"humidity":63,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04d"}],"clouds":{"all":72},"wind":{"speed":2.06,"deg":61},"sys":{"pod":"d"},"dt_txt":"2020-03-21 09:00:00"},{"dt":1584792000,"main":{"temp":6.58,"feels_like":3.75,"temp_min":6.58,"temp_max":6.58,"pressure":1017,"sea_level":1017,"grnd_level":935,"humidity":75,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":86},"wind":{"speed":1.76,"deg":83},"sys":{"pod":"d"},"dt_txt":"2020-03-21 12:00:00"},{"dt":1584802800,"main":{"temp":6.61,"feels_like":4.09,"temp_min":6.61,"temp_max":6.61,"pressure":1018,"sea_level":1018,"grnd_level":935,"humidity":76,"temp_kf":0},"weather":[{"id":500,"main":"Rain","description":"небольшой дождь","icon":"10d"}],"clouds":{"all":95},"wind":{"speed":1.38,"deg":108},"rain":{"3h":0.13},"sys":{"pod":"d"},"dt_txt":"2020-03-21 15:00:00"},{"dt":1584813600,"main":{"temp":5.25,"feels_like":2.19,"temp_min":5.25,"temp_max":5.25,"pressure":1019,"sea_level":1019,"grnd_level":935,"humidity":81,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":96},"wind":{"speed":2.05,"deg":156},"sys":{"pod":"n"},"dt_txt":"2020-03-21 18:00:00"},{"dt":1584824400,"main":{"temp":3.95,"feels_like":1.29,"temp_min":3.95,"temp_max":3.95,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":81,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03n"}],"clouds":{"all":30},"wind":{"speed":1.18,"deg":189},"sys":{"pod":"n"},"dt_txt":"2020-03-21 21:00:00"},{"dt":1584835200,"main":{"temp":3.32,"feels_like":0.51,"temp_min":3.32,"temp_max":3.32,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":77,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03n"}],"clouds":{"all":32},"wind":{"speed":1.11,"deg":236},"sys":{"pod":"n"},"dt_txt":"2020-03-22 00:00:00"},{"dt":1584846000,"main":{"temp":2.63,"feels_like":-0.15,"temp_min":2.63,"temp_max":2.63,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":76,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03n"}],"clouds":{"all":40},"wind":{"speed":0.9,"deg":228},"sys":{"pod":"n"},"dt_txt":"2020-03-22 03:00:00"},{"dt":1584856800,"main":{"temp":6.93,"feels_like":3.6,"temp_min":6.93,"temp_max":6.93,"pressure":1017,"sea_level":1017,"grnd_level":935,"humidity":63,"temp_kf":0},"weather":[{"id":801,"main":"Clouds","description":"небольшая облачность","icon":"02d"}],"clouds":{"all":21},"wind":{"speed":2,"deg":92},"sys":{"pod":"d"},"dt_txt":"2020-03-22 06:00:00"},{"dt":1584867600,"main":{"temp":9.73,"feels_like":4.91,"temp_min":9.73,"temp_max":9.73,"pressure":1016,"sea_level":1016,"grnd_level":934,"humidity":60,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03d"}],"clouds":{"all":40},"wind":{"speed":4.57,"deg":102},"sys":{"pod":"d"},"dt_txt":"2020-03-22 09:00:00"},{"dt":1584878400,"main":{"temp":9.56,"feels_like":5.2,"temp_min":9.56,"temp_max":9.56,"pressure":1015,"sea_level":1015,"grnd_level":933,"humidity":59,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04d"}],"clouds":{"all":70},"wind":{"speed":3.83,"deg":110},"sys":{"pod":"d"},"dt_txt":"2020-03-22 12:00:00"},{"dt":1584889200,"main":{"temp":8.44,"feels_like":4.44,"temp_min":8.44,"temp_max":8.44,"pressure":1016,"sea_level":1016,"grnd_level":933,"humidity":66,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":85},"wind":{"speed":3.44,"deg":104},"sys":{"pod":"d"},"dt_txt":"2020-03-22 15:00:00"},{"dt":1584900000,"main":{"temp":5.92,"feels_like":2.72,"temp_min":5.92,"temp_max":5.92,"pressure":1017,"sea_level":1017,"grnd_level":934,"humidity":80,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04n"}],"clouds":{"all":72},"wind":{"speed":2.36,"deg":127},"sys":{"pod":"n"},"dt_txt":"2020-03-22 18:00:00"},{"dt":1584910800,"main":{"temp":4.25,"feels_like":1.25,"temp_min":4.25,"temp_max":4.25,"pressure":1017,"sea_level":1017,"grnd_level":934,"humidity":91,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04n"}],"clouds":{"all":63},"wind":{"speed":2.12,"deg":135},"sys":{"pod":"n"},"dt_txt":"2020-03-22 21:00:00"},{"dt":1584921600,"main":{"temp":3.55,"feels_like":0.9,"temp_min":3.55,"temp_max":3.55,"pressure":1017,"sea_level":1017,"grnd_level":934,"humidity":89,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"переменная облачность","icon":"03n"}],"clouds":{"all":37},"wind":{"speed":1.37,"deg":133},"sys":{"pod":"n"},"dt_txt":"2020-03-23 00:00:00"},{"dt":1584932400,"main":{"temp":3,"feels_like":0.47,"temp_min":3,"temp_max":3,"pressure":1018,"sea_level":1018,"grnd_level":934,"humidity":87,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"ясно","icon":"01n"}],"clouds":{"all":8},"wind":{"speed":1,"deg":139},"sys":{"pod":"n"},"dt_txt":"2020-03-23 03:00:00"},{"dt":1584943200,"main":{"temp":7.5,"feels_like":4.37,"temp_min":7.5,"temp_max":7.5,"pressure":1016,"sea_level":1016,"grnd_level":934,"humidity":69,"temp_kf":0},"weather":[{"id":801,"main":"Clouds","description":"небольшая облачность","icon":"02d"}],"clouds":{"all":23},"wind":{"speed":2.13,"deg":87},"sys":{"pod":"d"},"dt_txt":"2020-03-23 06:00:00"},{"dt":1584954000,"main":{"temp":11.19,"feels_like":6.66,"temp_min":11.19,"temp_max":11.19,"pressure":1016,"sea_level":1016,"grnd_level":934,"humidity":59,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":88},"wind":{"speed":4.45,"deg":98},"sys":{"pod":"d"},"dt_txt":"2020-03-23 09:00:00"},{"dt":1584964800,"main":{"temp":11.94,"feels_like":6.46,"temp_min":11.94,"temp_max":11.94,"pressure":1014,"sea_level":1014,"grnd_level":933,"humidity":56,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":94},"wind":{"speed":5.8,"deg":108},"sys":{"pod":"d"},"dt_txt":"2020-03-23 12:00:00"},{"dt":1584975600,"main":{"temp":9.76,"feels_like":4.72,"temp_min":9.76,"temp_max":9.76,"pressure":1015,"sea_level":1015,"grnd_level":933,"humidity":67,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":100},"wind":{"speed":5.29,"deg":106},"sys":{"pod":"d"},"dt_txt":"2020-03-23 15:00:00"},{"dt":1584986400,"main":{"temp":7.77,"feels_like":3.76,"temp_min":7.77,"temp_max":7.77,"pressure":1016,"sea_level":1016,"grnd_level":934,"humidity":75,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":3.74,"deg":115},"sys":{"pod":"n"},"dt_txt":"2020-03-23 18:00:00"},{"dt":1584997200,"main":{"temp":5.06,"feels_like":1.33,"temp_min":5.06,"temp_max":5.06,"pressure":1016,"sea_level":1016,"grnd_level":933,"humidity":80,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":2.91,"deg":116},"sys":{"pod":"n"},"dt_txt":"2020-03-23 21:00:00"},{"dt":1585008000,"main":{"temp":3.47,"feels_like":0.1,"temp_min":3.47,"temp_max":3.47,"pressure":1016,"sea_level":1016,"grnd_level":933,"humidity":92,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":2.49,"deg":109},"sys":{"pod":"n"},"dt_txt":"2020-03-24 00:00:00"},{"dt":1585018800,"main":{"temp":2.96,"feels_like":-0.28,"temp_min":2.96,"temp_max":2.96,"pressure":1016,"sea_level":1016,"grnd_level":933,"humidity":94,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04n"}],"clouds":{"all":100},"wind":{"speed":2.26,"deg":109},"sys":{"pod":"n"},"dt_txt":"2020-03-24 03:00:00"},{"dt":1585029600,"main":{"temp":5.17,"feels_like":1.65,"temp_min":5.17,"temp_max":5.17,"pressure":1015,"sea_level":1015,"grnd_level":933,"humidity":81,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":100},"wind":{"speed":2.68,"deg":95},"sys":{"pod":"d"},"dt_txt":"2020-03-24 06:00:00"},{"dt":1585040400,"main":{"temp":9.31,"feels_like":4.86,"temp_min":9.31,"temp_max":9.31,"pressure":1014,"sea_level":1014,"grnd_level":933,"humidity":66,"temp_kf":0},"weather":[{"id":804,"main":"Clouds","description":"пасмурно","icon":"04d"}],"clouds":{"all":89},"wind":{"speed":4.29,"deg":94},"sys":{"pod":"d"},"dt_txt":"2020-03-24 09:00:00"},{"dt":1585051200,"main":{"temp":11.17,"feels_like":5.51,"temp_min":11.17,"temp_max":11.17,"pressure":1013,"sea_level":1013,"grnd_level":932,"humidity":61,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"облачно с прояснениями","icon":"04d"}],"clouds":{"all":79},"wind":{"speed":6.18,"deg":99},"sys":{"pod":"d"},"dt_txt":"2020-03-24 12:00:00"}],"city":{"id":503550,"name":"Пятигорск","coord":{"lat":44.0486,"lon":43.0594},"country":"RU","timezone":10800,"sunrise":1584587548,"sunset":1584631107}}', true)
];
*/
// end of primer

$jsonstr['forecast'] = set($jsonstr['forecast']['list'], true);

//print_r($jsonstr);

$data = [
	'number' => datadatetime($jsonstr['weather']['dt'], '{w}'),
	'temperature' => round($jsonstr['weather']['main']['temp']), // оС
	'feels' => round($jsonstr['weather']['main']['feels_like']), // оС
	'description' => null,
	'code' => null,
	'humidity' => $jsonstr['weather']['main']['humidity'], // влажность, %
	'pressure' => $jsonstr['weather']['main']['pressure'], // давление
	'wind' => $jsonstr['weather']['wind']['speed'], // м/с
	'direction' => $jsonstr['weather']['wind']['deg'], // 0/360 - север, 90 - восток, 180 - юг, 270 - запад
	'morning' => [],
	'day' => [],
	'night' => [],
	'daily' => []
];

foreach ($jsonstr['weather']['weather'] as $item) {
	$data['description'][] = $item['description'];
	$data['code'][] = $item['icon'];
}
$data['description'] = objectToString($data['description'], ', ');
unset($item);
if (set($data['code'])) {
	$data['code'] = array_flip(array_count_values($data['code']));
	krsort($data['code']);
	$data['code'] = array_shift($data['code']);
}

foreach ($jsonstr['forecast'] as $item) {
	
	$date = datadatetime($item['dt'], '{yy}{mm}{dd}');
	$curr = datadatetime('', '{yy}{mm}{dd}');
	$number = datadatetime($item['dt'], '{w}');
	$time = (int) datadatetime($item['dt'], '{hour}');
	$temperature = $item['main']['temp'] < 0 && $item['main']['temp'] > -1 ? 0 : round($item['main']['temp']);
	$feels = $item['main']['feels_like'] < 0 && $item['main']['feels_like'] > -1 ? 0 : round($item['main']['feels_like']);
	
	if ($time < 6) {
		$type = 'night';
		$date = datadatetime($item['dt'] - TIME_DAY / 2, '{yy}{mm}{dd}');
	} elseif ($time < 12) {
		$type = 'morning';
	} elseif ($time > 18) {
		$type = 'night';
	} else {
		$type = 'day';
	}
	
	//$nd = datadatetime($date, '{yy}{mm}{dd}', true); // <--- здесь почему-то не работает, хотя должен...
	//$nd = strtotime(datadatetime($item['dt'], '{yy}') . ':' . datadatetime($item['dt'], '{mm}') . ':' . datadatetime($item['dt'], '{dd}') . ' 12:00:00');
	//echo $date . '(' . $item['dt'] . ') : ' . $nd . '(' . datadatetime($nd, '{yy}{mm}{dd}') . ')<br>';
	
	if ($date === $curr) {
		$target = &$data;
	} else {
		$target = &$data['daily'][$date];
		if (empty($target['number'])) {
			$target['number'] = $number;
		}
	}
	
	$target[$type]['temperature'][] = $temperature;
	$target[$type]['feels'][] = $feels;
	
	$target[$type]['humidity'][] = $item['main']['humidity'];
	$target[$type]['pressure'][] = $item['main']['pressure'];
	$target[$type]['wind'][] = $item['wind']['speed'];
	$target[$type]['direction'][] = $item['wind']['deg'];
	
	foreach ($item['weather'] as $i) {
		$target[$type]['description'][] = $i['description'];
		$target[$type]['code'][] = $i['icon'];
	}
	unset($i);
	
	unset($date, $curr, $number, $time, $temperature, $type, $target);
	
}
unset($item);
unset($jsonstr);

foreach (['morning', 'day', 'night'] as $key) {
	foreach ($data['daily'] as &$item) {
		$item[$key]['temperature'] = set($item[$key]['temperature']) ? round(array_sum($item[$key]['temperature']) / count($item[$key]['temperature'])) : null;
		$item[$key]['feels'] = set($item[$key]['feels']) ? round(array_sum($item[$key]['feels']) / count($item[$key]['feels'])) : null;
		$item[$key]['description'] = objectToString(set($item[$key]['description']) ? objectClear($item[$key]['description'], null, true) : $item[$key]['description'], ', ');
		if (set($item[$key]['code'])) {
			$item[$key]['code'] = array_flip(array_count_values($item[$key]['code']));
			krsort($item[$key]['code']);
			$item[$key]['code'] = array_shift($item[$key]['code']);
		}
		
		$item[$key]['humidity'] = set($item[$key]['humidity']) ? round(array_sum($item[$key]['humidity']) / count($item[$key]['humidity'])) : null;
		$item[$key]['pressure'] = set($item[$key]['pressure']) ? round(array_sum($item[$key]['pressure']) / count($item[$key]['pressure'])) : null;
		$item[$key]['wind'] = set($item[$key]['wind']) ? round(array_sum($item[$key]['wind']) / count($item[$key]['wind'])) : null;
		$item[$key]['direction'] = set($item[$key]['direction']) ? round(array_sum($item[$key]['direction']) / count($item[$key]['direction'])) : null;
		
	}
	unset($item);
	
	$item = &$data;
	$item[$key]['temperature'] = set($item[$key]['temperature']) ? round(array_sum($item[$key]['temperature']) / count($item[$key]['temperature'])) : null;
	$item[$key]['feels'] = set($item[$key]['feels']) ? round(array_sum($item[$key]['feels']) / count($item[$key]['feels'])) : null;
	
	$item[$key]['description'] = objectToString(set($item[$key]['description']) ? objectClear($item[$key]['description'], null, true) : $item[$key]['description'], ', ');
	if (set($item[$key]['code'])) {
		$item[$key]['code'] = array_flip(array_count_values($item[$key]['code']));
		krsort($item[$key]['code']);
		$item[$key]['code'] = array_shift($item[$key]['code']);
	}
	
	$item[$key]['humidity'] = set($item[$key]['humidity']) ? round(array_sum($item[$key]['humidity']) / count($item[$key]['humidity'])) : null;
	$item[$key]['pressure'] = set($item[$key]['pressure']) ? round(array_sum($item[$key]['pressure']) / count($item[$key]['pressure'])) : null;
	$item[$key]['wind'] = set($item[$key]['wind']) ? round(array_sum($item[$key]['wind']) / count($item[$key]['wind'])) : null;
	$item[$key]['direction'] = set($item[$key]['direction']) ? round(array_sum($item[$key]['direction']) / count($item[$key]['direction'])) : null;
	
	unset($item);
}
unset($key);

//echo '<hr><pre>' . print_r($module -> data, 1) . '</pre>';
//echo '<hr><pre>' . print_r($data, 1) . '</pre>';

?>