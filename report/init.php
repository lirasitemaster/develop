<?php defined('isCMS') or die;

$name = $module -> param . 'DataTable';

$time = [
	'day' => !empty($_GET['day']) ? $_GET['day'] : null,
	'from' => !empty($_GET['from']) ? $_GET['from'] : null,
	'to' => !empty($_GET['to']) ? $_GET['to'] : null,
	'parent' => !empty($_GET['parent']) ? str_replace(':', DS, $_GET['parent']) . DS : null,
	'absfrom' => null,
	'absto' => null,
	'format' => '{yy}-{mm}-{dd}',
	'point' => null
];

if (
	!empty($time['from']) &&
	!empty($time['to']) &&
	$time['from'] > $time['to']
) {
	$n = $time['from'];
	$time['from'] = $time['to'];
	$time['to'] = $n;
	unset($n);
}

$path = PATH_LOG . 'send' . DS;
$folders = localList($path, ['subfolders' => true, 'return' => 'folders']);
unset($path);

?>

<form action="" method="get" class="report-form">
тема <select name="parent">
<option value=""<?= empty($_GET['parent']) ? ' selected' : null; ?>>-</option>
<?php
foreach ($folders as $item) :
$item = str_replace(DS, ':', $item);
if (mb_substr($item, -1) === ':') {
	$item = mb_substr($item, 0, -1);
}
?>
<option value="<?= $item; ?>"<?= $item === $_GET['parent'] ? ' selected' : null; ?>><?= $item; ?></option>
<?php
endforeach;
unset($item, $folders);
?>
</select>
<br>
за один день <input name="day" type="date" value="<?= $time['day']; ?>" />
<br>
за период <input name="from" type="date" value="<?= $time['from']; ?>" /> - <input name="to" type="date" value="<?= $time['to']; ?>" />
<br>
<input type="submit" value="Обновить" />
</form>

<?php

if (!empty($time['day'])) {
	$time['from'] = $time['day'];
	$time['to'] = $time['day'];
}

$time['absfrom'] = !empty($time['from']) ? datadatetime($time['from'], $time['format'], true) : null;
$time['absto'] = !empty($time['to']) ? datadatetime($time['to'], $time['format'], true) + TIME_DAY : null;

$path = PATH_LOG . 'send' . DS . (!empty($time['parent']) ? $time['parent'] : null);
$files = localList($path, ['subfolders' => true, 'return' => 'files']);
$table = [];
$count = null;

if (objectIs($files)) {
	
	$count = count($files);
	
	foreach ($files as $key => $item) {
		
		$target = iniPrepareJson(localFile($path . $item), true);
		
		$date = empty($time['point']) ? filemtime($path . $item) : $target['data'][$time['point']];
		
		if (
			!empty($time['absfrom']) && $date < $time['absfrom'] ||
			!empty($time['absto']) && $date > $time['absto']
		) {
			continue;
		}
		
		unset($date);
		
		if (objectIs($target)) {
			
			$table['date'][$key] = empty($time['point']) ? date('Y.m.d H:i:s', filemtime($path . $item)) : $target['data'][$time['point']];
			
			$table['status'][$key] = $target['status'];
			$table['type'][$key] = $target['sets']['type'];
			$table['subject'][$key] = $target['subject'];
			
			foreach ($target['data'] as $k => $i) {
				$table[$k][$key] = $i;
			}
			
			unset($key, $item);
			
		}
		
	}
	unset($key, $item);
	
}

unset($sets);
$sets = $module -> settings['options'];
$buttons = $module -> settings['buttons'];

$classes['display'] = dataParse($classes['display']);
$js['display'] = dataParse($js['display']);

/*
if (objectIs($table)) {
	
	$head = array_keys($table);
	
	if (objectIs($head)) {
		
		echo '<table class="report" id="' . $name . '"><thead><tr>';
		echo '<th>№</th>';
		
		foreach ($head as $key => $item) {
			echo '<th>' . clear($item) . '</th>';
		}
		unset($key, $item);
		
		echo '</tr></thead><tbody>';
		
		while ($count > 0) {
		//for (null; $count > 0; $count--) {
			
			$c = count($head);
			$i = null;
			
			foreach ($head as $item) {
				$i .= '<td>' . clear($table[$item][$count - 1]) . '</td>';
				if (!empty($table[$item][$count - 1])) {
					$c--;
				}
			}
			unset($item);
			
			if ($c < count($head)) {
				echo '<tr><td>' . $count . '</td>' . $i . '</tr>';
			}
			
			$count--;
			unset($i, $c);
		}
		
		echo '</tbody></table>';
		
	}
	
} elseif (objectIs($files)) {
	echo '<br>Нет записей, соответствующих заданному условию';
}
*/

?>