<?php defined('isCMS') or die; ?>
<script>
$(document).ready(function(){
<?php
	
	$preprint = '';
	
	// в input нужны разные типы, которые будут назначаться из настроек
	// а именно, дата/время, email, диапазон чисел от-до, область текста, редактор
	// но пока достаточно будет input/textarea/datepicker
	
	// если выделено несколько записей, нужно вычислять совпадающие и разнящиеся значения
	// и затем передавать только те значения, которые были или стали общими для всех выделенных записей
	// для этих целей нужно ввести специальный атрибут, который будет игнорировать запись при попытке её передачи (сериализации)
	
	// и еще, что делать в случае с языками? ведь там, да и во многих других моментах,
	// в одних записях могут появляться поля, которые не нужны в других записях
	// но сейчас записи мерджатся в таблицу
	// значит, мердж нужно убирать, когда данные не выводятся в таблице
	// а если в таблице выводятся только заданные данные, то мердж нужно оставить только для тех, которые выводятся
	
	// еще нет возможности вывода отдельного окна для какой-либо отдельной записи
	
	// ДО кастомизации редактора осталось сделать ЗАПИСЬ в базу данных по кнопке SAVE
	// а также системную библиотеку и ссылки для библиотек
	
	$postprint = '';
	
	$print = '
	var table = $("#' . $name . '").DataTable({
		"stateSave": true,
		"scrollX": true';

// сохраняет сортировку при обновлении страницы
// добавляет скролл по горизонтали

// это управляет шириной колонок
if (objectIs($sets['width'])) {
	$print .= ',
		"columnDefs": [';
	$c = 0;
	foreach ($sets['width'] as $key => $item) {
		if (!empty($item)) {
			$print .= (!empty($c) ? ',' : null) . '{"width": "' . $item . '", "targets": ' . $key . '}';
			$c++;
		}
	}
	unset($c, $key, $item);
	$print .= ']';
}

// это отменяет кнопки и дает прокрутку вниз
if (!empty($sets['scroll'])) {
	$print .= ',
		"scrollY": "100%",
		"scrollCollapse": true,
		"paging": false';
}

// это отменяет кнопки и дает прокрутку вниз
if (!empty($sets['length'])) {
	
	if (objectIs($sets['length'])) {
		$length = objectToString($sets['length'], ', ');
		$print .= ',
		"lengthMenu": [[' . $length . ', -1], [' . $length . ', "' . (!empty($labels['all']) ? $labels['all'] : 'All') . '"]]';
		unset($length);
	} elseif (is_string($sets['length']) || is_numeric($sets['length'])) {
		$print .= ',
		"pageLength": ' . $sets['length'] . ',
		"lengthChange": false';
	}
	
} else {
	$print .= ',
	"lengthChange": false';
}

// это дает еще кнопки - в начало, в конец
if (!empty($sets['extreme'])) {
	$print .= ',
		"pagingType": "full_numbers"';
}

// это дает строку фильтрации по каждой колонке
if (!empty($sets['filtration'])) {
	$preprint .= '
	$("#' . $name . ' tfoot th").each(function(){
		var title = $(this).text().trim();
		$(this).html(\'<input type="text" placeholder="' . (!empty($labels['filter']) ? $labels['filter'] : 'Search') . ' \'+title+\'" />\');
	});
	';
	$postprint .= '
	table.columns().every(function(){
		var that = this;
		$("input", this.footer()).on("keyup change clear", function(){
			if (that.search() !== this.value) {
				that
					.search(this.value)
					.draw();
			}
		});
	});
	';
}

// это выводит строку, которая позволяет отображать или скрывать колонки
if (!empty($sets['display'])) {
	$postprint .= '
	
	var thactive = [];
	
	$("#' . $name . ' thead th").each(function(){
		var title = $(this).text().trim();
		thactive.push(title);
	});
	
	$(".' . $js['display'][1] . '").each(function(){
		var title = $(this).text().trim();
		if (thactive.indexOf(title) < 0) {
			$(this).addClass("' . $js['display'][2] . '");
		}
	});
	
	$(".' . $js['display'][1] . '").on("click", function(e){
		
		e.preventDefault();
		$(this).toggleClass("' . $js['display'][2] . '");
		
		var index = $(this).attr("data-column");
		var column = table.column(index);
		
		column.visible(!column.visible());
		
	});
	
	';
}

// это для выделения одной строки
if (!empty($sets['select'])) {
	
	$print .= ',
		"select" : {"className" : "' . $js['select'] . '", ';
	
	if ($sets['select'] === true) {
		$print .= '"style" : "os"';
	} elseif ($sets['select'] === 'single') {
		$print .= '"style" : "single"';
	} else {
		$print .= '"items" : "' . $sets['select'] . '"';
	}
	
	$print .= '}';
	
}

// это для дополнительных кнопок сохранения таблицы
if (!empty($buttons['base'])) {
	
	if (objectIs($buttons['base']) && objectIs($labels['buttons'])) {
		$buttonsbase = null;
		foreach ($buttons['base'] as $k => $i) {
			$buttonsbase .= !empty($k) ? ', ' : null;
			if (array_key_exists($i, $labels['buttons'])) {
				$buttonsbase .= '{ "extend" : "' . $i . '", "text" : "' . $labels['buttons'][$i] . '", "exportOptions" : { "columns" : ":not(.' . $js['noprint'] . ')" } }';
			} else {
				$buttonsbase .= '"' . $i . '"';
			}
		}
		unset($k, $i);
		$buttons['base'] = null;
		$buttons['base'] = '[' . $buttonsbase . ']';
		unset($buttonsbase);
	}
	
	$print .= ',
		"buttons" : ' . (objectIs($buttons['base']) ? iniPrepareArray($buttons['base']) : (!empty($buttons['base']) ? $buttons['base'] : 'true'));
	
	$postprint .= '
		table.buttons().container().appendTo( $(".' . $js['buttons'] . '") );
	';
}

// это для перевода таблицы
if (!empty($labels['translate'])) {
	$print .= ',
		"language" : ' . (objectIs($labels['translate']) ? iniPrepareArray($labels['translate']) : '{ "url" : "' . $labels['translate'] . '" }');
}

$print .= '
	});';

echo $preprint . $print . $postprint;
unset($preprint, $print, $postprint);

// https://datatables.net/examples/index

?>

});
</script>