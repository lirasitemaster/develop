<?php

// вывод информации сервера

echo '<pre>';
print_r($_SERVER);
echo '</pre>';
exit;

// как мерить запросы

$n = [
	1000, // число итераций для вычисления среднего арифметического
	10000 // число повторений запроса, т.к. запрос обрабатывается быстро и на 1-100 прогонах сравнивать бесполезно
];
$a = []; for ($c = 0; $c < $n[0]; $c++){ $s = microtime(); for ($i = 0; $i < $n[1]; $i++){

	// сюда вставить код какого-либо действия

} $e = microtime(); $s = substr($s, 2, strpos($s, ' ') - 6); $e = substr($e, 2, strpos($e, ' ') - 6); $a[] = $e - $s; } foreach ($a as $key => $item) { if ($item <= 0) { unset($a[$key]); } } echo 'время выполнения запроса: ' . round(array_sum($a) / count($a)) . ' мсек (1/1000 сек) на ' . $n[1] . ' повторений<br>использовано памяти: ' . memory_get_usage()/1024 . ' Kb<br>максимальный расход памяти: ' . memory_get_peak_usage()/1024 . ' Kb';

// как посмотреть константы путей

$names = [
	'DATABASE',
	'CACHE',
	'CONTENT',
	'CORE',
	'LANGUAGES',
	'LIBRARIES',
	'LOCAL',
	'LOG',
	'MODULES',
	'TEMPLATES'
];

echo 'PATH_BASE ' . PATH_BASE . '<br>';
echo 'PATH_SITE ' . PATH_SITE . '<br>';

echo '<table>';
echo '<tr style="background: #eee;">
	<td>const' . $item . '</td>
	<td>NAME_*</td>
	<td>PATH_*</td>
	<td>URL_*</td>
</tr>';

foreach ($names as $item) {
	echo '<tr>
		<td>' . $item . '</td>
		<td>' . constant('NAME_' . $item) . '</td>
		<td>' . constant('PATH_' . $item) . '</td>
		<td>' . constant('URL_' . $item) . '</td>
	</tr>';
}
echo '</table>';

// проверка работы функции set и сравнения ее результатов с другими функциями сравнения

$testarr = [[
	// результаты на false
	null,
	false,
	'',
	[],
	['']
],[
	// результаты на true
	true,
	0,
	'0',
	1,
	'1',
	'string',
	['0'],
	['1'],
	['string']
],
[
	// результаты с выражениями
	is_int(123),
	is_int('123')
]];
$testarrnames = [[
	'false', 'true', 'expressions'
],[[],[],[
	'is_int(123)',
	'is_int(\'123\')'
]]];
echo '<table border=1><thead><tr>
	<th>$x</th>
	<th>!empty($x)</th>
	<th>isset($x)</th>
	<th>if($x)</th>
	<th>set($x)</th>
	<th>set($x, true)</th>
	<th>set($x, \'return\')</th>
</tr></thead><tbody>';
foreach ($testarr as $k => $i) {
	echo '<tr><td colspan="7" align="center">' . $testarrnames[0][$k] . '</td></tr>';
	foreach ($i as $key => $item) {
		if (empty($testarrnames[1][$k][$key])) { $testarrnames[1][$k][$key] = htmlentities(json_encode($item)); }
		echo '<tr>
			<td>' . $testarrnames[1][$k][$key] . '</td>
			<td>' . (!empty($item) ? 'true' : 'false') . '</td>
			<td>' . (isset($item) ? 'true' : 'false') . '</td>
			<td>' . ($item ? 'true' : 'false') . '</td>
			<td>' . (set($item) ? 'true' : 'false') . '</td>
			<td>' . htmlentities(json_encode(set($item, true))) . '</td>
			<td>' . htmlentities(set($item, 'return')) . '</td>
		</tr>';
	}
}
echo '</tbody></table>';

?>