<html>
<head>
<style>
body {
	font-size: 30px;
	padding: 2em;
	margin: 0;
}
form {
	display: flex;
	flex-direction: column;
	width: 100%;
}
.group {
	display: flex;
	flex-direction: row;
	flex-wrap: nowrap;
	justify-content: space-between;
}
.wrap {
	flex-wrap: wrap;
}
.element {
	padding: 1em 0.5em;
	margin: 1em 0.5em;
	border: 2px solid #ccc;
	width: 35%;
	font-size: 1em;
}
.compare {
	width: 30%;
	padding: 1em 0.5em;
	margin: 1em 0.5em 0;
	border: 2px solid #ccc;
	font-size: 1.5em;
}
.label {
	padding: 1em 0em;
	margin: 1em 0em;
	width: auto;
	font-size: 1.25em;
	font-weight: 700;
}
.info {
	font-size: 1.2em;
	margin: -0.5em 0.5em 0.5em;
	text-align: right;
}
.button {
	padding: 1em 0.5em;
	margin: 1em 0.5em 0;
	border: 2px solid #ccc;
	width: 100%;
	font-size: 1.5em;
}
.w50 {
	width: 55%;
}
.result {
	border: 2px solid #ccc;
	min-height: 1em;
	padding: 0.5em 1em;
	font-size: 2em;
}
</style>
</head>
<body>
<?php

$price = $_POST['price'];
$compare = $_POST['compare'];
$result = null;
$res = [];

if (!empty($_POST['reset'])) {
	unset($price);
	unset($compare);
}

if (!empty($price)) {
	
	foreach ($price as $key => &$item) {
		
		if (
			!empty($item['p']) &&
			!empty($item['w'])
		) {
			
			$item['p'] = str_replace(',', '.', $item['p']);
			$item['w'] = str_replace(',', '.', $item['w']);
			$item['p'] = preg_replace('/[^\d\.]/', '', $item['p']);
			$item['w'] = preg_replace('/[^\d\.]/', '', $item['w']);
			
			$res[$key] = $item['p'] / $item['w'];
			
			if (
				!empty($compare) &&
				$compare != 1
			) {
				$price[$key]['c'] = round($res[$key] * $compare * 100) / 100;
				$dot = strpos($price[$key]['c'], '.');
				if ($dot === false) {
					$price[$key]['c'] .= '.00';
				} elseif (strlen(substr($price[$key]['c'], $dot)) <= 2) {
					$price[$key]['c'] .= '0';
				}
				unset($dot);
			}
			
		}
		
	}
	unset($item, $key);
	
}

if (!empty($res)) {
	
	asort($res);
	$good = key($res);
	foreach ($res as $key => $item) {
		if ($key == $good) {
			$good = $item;
			$result = $key;
		} elseif ($good === $item) {
			$result .= ', ' . $key;
		} else {
			break;
		}
	}
	unset($good);
	
	echo '<p class="result">';
	if (strlen($result) > 2) {
		echo 'Самые дешевые варианты';
	} else {
		echo 'Самый дешевый вариант';
	}
	echo ': <strong>№ ' . $result . '</strong></p>';
	
}

?>
<form method="post" action="">
	
	<?php
		$data = [1, 2, 3, 4];
		$type = ['p' => 'цена', 'w' => 'вес или объем'];
		foreach ($data as $item) {
			echo '<div class="group"><span class="label">№ ' . $item . '</span>';
			echo '<input class="element" type="number" lang="en" min="0" max="1000000" step="0.01" novalidate name="price[' . $item . '][p]" placeholder="цена" value="' . (!empty($price[$item]['p']) ? $price[$item]['p'] : false) . '">';
			echo '<span class="label">руб за</span>';
			echo '<input class="element" type="number" lang="en" min="0" max="1000000" step="0.01" novalidate name="price[' . $item . '][w]" placeholder="вес или объем" value="' . (!empty($price[$item]['w']) ? $price[$item]['w'] : false) . '">';
			echo '</div>';
			if (!empty($price[$item]['c'])) {
				echo '<p class="info">' . $price[$item]['c'] . ' руб за ' . $compare . '</p>';
			}
		}
		unset($item);
	?>
	
	<div class="group wrap">
		<button class="button w50" type="submit">сравнить</button>
		<select name="compare" class="compare">
			<?php
				$cdata = [1, 10, 100, 1000];
				foreach ($cdata as $item) {
					echo '<option value="' . $item . '"' . ($compare == $item ? ' selected' : '') . '>за ' . $item . '</option>';
				}
				unset($item);
			?>
		</select>
		<button class="button" name="reset" value="1" type="submit">очистить</button>
	</div>
	
</form>
</body>
</html>