<html>
<head>
<style>

</style>
</head>
<body>
<?php

$list = !empty($_POST['list']) ? $_POST['list'] : null;
$ext = !empty($_POST['ext']) ? $_POST['ext'] : null;
$tpl = !empty($_POST['tpl']) ? $_POST['tpl'] : null;
$id = !empty($_POST['id']) ? $_POST['id'] : null;
$fn = !empty($_POST['fn']) ? $_POST['fn'] : null;
$flrd = !empty($_POST['flrd']) ? $_POST['flrd'] : null;

if (!empty($_POST['reset'])) {
	unset($list);
	unset($ext);
	unset($tpl);
	unset($fn);
	unset($id);
	unset($flrd);
}

?>
<form method="post" action="">
	
	<div style="display: inline-block; margin: 0 50px;">
		
		<p>Список</p>
		<textarea name="list" cols="20" rows="50" style="resize: none;"><?= !empty($list) ? $list : null; ?></textarea>
		
	</div>
	
	<div style="display: inline-block; vertical-align: top;">
		
		<p>
			Расширение файлов<br>
			<input name="ext" type="text" value="<?= !empty($ext) ? $ext : null; ?>">
		</p>
		
		<p>
			Имя файла<br>
			<input name="fn" type="text" value="<?= !empty($fn) ? $fn : null; ?>">
			<br>* если указать, то список будет использован как название папок
			<br>** здесь тоже можно использовать текстовые переменные
		</p>
		
		<p>
			<input name="id" type="checkbox" <?= !empty($id) ? 'checked' : null; ?>>
			Включить идентификаторы в имена
		</p>
		
		<p>
			<input name="flrd" type="radio" value="and" <?= !empty($flrd) && $flrd === 'and' ? 'checked' : null; ?>>
			Создать пустые папки по названиям файлов
			<br>
			<input name="flrd" type="radio" value="only" <?= !empty($flrd) && $flrd === 'only' ? 'checked' : null; ?>>
			Создать только пустые папки
			<br>
			<input name="flrd" type="radio" value="not" <?= empty($flrd) || $flrd === 'not' ? 'checked' : null; ?>>
			Не создавать папок
		</p>
		
		<p>Шаблон внутренностей файла</p>
		<textarea name="tpl" cols="100" rows="20"><?= !empty($tpl) ? $tpl : null; ?></textarea>
		
		<p>
			Вы можете использовать текстовые переменные: {{var}}. Например, 'bla-bla-bla {{name}} bla-bla-bla'.
			<br>name - имя файла из списка без расширения
			<br>ext - расширение
			<br>id - порядковый номер
		</p>
		
		<input type="submit">
		
	</div>
	
</form>
<?php

if (!empty($list)) {
	$data = [];
	$filelist = preg_split('/[\,\;\s\t\r\n]/u', $list, null, PREG_SPLIT_NO_EMPTY);
	if (!empty($filelist) && is_array($filelist)) {
		$dir = __DIR__ . DIRECTORY_SEPARATOR . '__output' . DIRECTORY_SEPARATOR;
		if (!file_exists($dir)) {
			mkdir($dir, 0777);
		}
		foreach ($filelist as $key => $item) {
			
			$name = strpos($item, '.') !== false ? substr($item, 0, strpos($item, '.')) : $item;
			
			if (!empty($flrd) && $flrd !== 'not') {
				if (!file_exists($dir . $name)) {
					mkdir($dir . $name, 0777);
				}
				if ($flrd === 'only') {
					continue;
				}
			}
			
			$fnn = !empty($fn) ? str_replace(
				['{{name}}', '{{ext}}', '{{id}}'],
				[$name, $ext, $key],
				$fn
			) : null;
			
			$content = !empty($tpl) ? str_replace(
				['{{name}}', '{{ext}}', '{{id}}'],
				[$name, $ext, $key],
				$tpl
			) : null;
			
			$name .= !empty($id) ? $key . '.' : null;
			
			$file = $dir . $name . (!empty($fnn) ? DIRECTORY_SEPARATOR . $fnn : null) . (!empty($ext) ? '.' . $ext : null);
			
			if (!empty($fnn) && !file_exists($dir . $name)) {
				mkdir($dir . $name, 0777);
			}
			
			if (!empty($content)) {
				file_put_contents($file, $content);
			}
			
			echo $file . '<br>';
			
		}
		unset($item);
	}
}

?>
</body>
</html>