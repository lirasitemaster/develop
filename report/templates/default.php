<?php defined('isCMS') or die;

if (objectIs($table)) {
	
	if (objectIs($buttons)) {
		echo '<div class="' . $classes['buttons'] . '"></div>';
	}
	
	$head = array_keys($table);
	
	require $module -> elements . 'display.php';
	
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
		
		echo '</tbody>';
		
		if (!empty($sets['filtration'])) {
			echo '<tfoot><tr><td></td>';
			foreach ($head as $item) {
				echo '<th>' . $item . '</th>';
			}
			unset($item);
			echo '</tr></tfoot>';
		}
		
		echo '</table>';
		
	}
	
} elseif (objectIs($files)) {
	echo '<br>Нет записей, соответствующих заданному условию';
} else {
	echo '<br>Список пуст';
}

require $module -> elements . 'script.php';
require $module -> elements . 'style.php';

?>