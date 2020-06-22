<?php defined('isENGINE') or die; ?>
<script>
$(document).ready(function(){
<?php

	$preprint = '
	var tbl = ' . iniPrepareArray($tbl) . ';
	';
	
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

// включаение редактора json
if (
	!empty($buttons['editintable']) ||
	!empty($buttons['edit'])
) {
	
	// https://github.com/json-editor/json-editor
	// https://json-editor.github.io/json-editor/
	// examples in /docs/ folder on repository and in site by link as
	// https://json-editor.github.io/json-editor/wysiwyg.html
	
	$form = objectProcess('system:write');
	
	$postprint .= '
	
	var jsoneditor;
	
	JSONEditor.defaults.options.schema = ' . iniPrepareArray($schema) . ';
	
    var jsonreload = function() {
		if (jsoneditor) jsoneditor.destroy();
		jsoneditor = new JSONEditor(document.getElementById("' . $js['editor'] . '"), {
			theme : "bootstrap4",
			iconlib : "fontawesome5",
			object_layout : "normal",
			show_errors : "interaction"
		});
		window.jsoneditor = jsoneditor;
    };
	
	jsonreload();
	
	$("#editModal form").on("submit", function(e){
		
		e.preventDefault();
		
		var data = ' . iniPrepareArray($form['array']) . ';
		
		data["data"] = {
			db : "' . ($module -> this ? $module -> this : $module -> param) . '",
			name : jsoneditor.options.schema.title,
			data : JSON.stringify(jsoneditor.getValue())
		};
		
		$.post(
			"' . $form['link'] . '",
			data,
			function(data) {
				
				var err = false;
				
				if (!data) {
					err = true;
				} else {
					
					var name = jsoneditor.options.schema.title;
					
					try {
						tbl[name]["data"] = JSON.parse(data);
					} catch (i) {
						err = true;
					}
					
					//console.log(data);
					
				}
				
				if (err) {
					console.log("AHTUNG!!!");
				} else {
					console.log("ай да сукин сын");
				}
				
				
				
			}
		);
		
	});
	';
	
	$jsoneditor = '
	
	JSONEditor.defaults.options.schema.title = name;
	jsonreload();
	jsoneditor.setValue(data);
	$("#editModal").modal();
	
	';
	
	unset($form);
	
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

// если выбрана дополнительная кнопка редактирования
if (!empty($buttons['editintable'])) {
	$print .= ',
		"columnDefs": [{
			"searchable": false,
			"orderable": false,
			"targets": "' . $js['nosort'] . '"
		}],
		"order": [[' . (!empty($buttons['editintable']) && $buttons['editintable'] !== 'after' ? '1' : '0') . ', "asc"]]';
	
	$postprint .= '
	$(".' . $js['editintable'] . '").click(function(){
		
		// вытаскиваем данные из текущей записи
		
		var name = $(this).parents("tr").first().data("name");
		var data = tbl[name]["data"];
		
		' . $jsoneditor . '
		
	});
	';
}

// это дает кнопку, добавляющую строку
if (!empty($sets['addrow'])) {
	$postprint .= '
	var counter = 1;
	$("#addRow").on("click", function(){
		table.row.add([
			counter+".1",
			counter+".2",
			counter+".3",
			counter+".4",
			counter+".5"
		]).draw(false);
		counter++;
	});';
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

// это создает форму внутри таблицы с редактированием ячеек inline
if (!empty($buttons['editinline'])) {
	$postprint .= '
	$(".' . $js['editinline'] . '").click(function(){
		
		$(this).toggleClass("' . $js['switch'] . '");
		var t = $(this).hasClass("' . $js['switch'] . '");
		
		$("#' . $name . ' tbody td").each(function(){
			if ( t && $(this).attr("data-protect") != 1 ) {
				var title = $(this).text().trim();
				$(this).html(\'<div><input type="text" value="\'+title+\'" /></div>\');
			} else if ( $(this).find("input") ) {
				var title = $(this).find("input").val();
				$(this).text(title);
			}
		});
		
		table.draw();
		return false;
	});
	';
}

// это выводит окно редактирования выбранной строки
if (!empty($buttons['edit'])) {
	$postprint .= '
	$(".' . $js['edit'] . '").click(function(){
		
		var data = [];
		
		// вытаскиваем данные из всех выделенных записей
		table.$("tbody tr.' . $js['select'] . '").each(function(){
			name = $(this).data("name");
			data = tbl[name]["data"];
			return true;
		});
		
		' . $jsoneditor . '
		
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
	
	/*
	var ttt = [];
	table.columns().every(function(i){
		if (!table.column(i).visible()) {
			ttt.push(i);
		}
	});
	table.buttons.exportData({
		"columns" : ttt
	});
	var data = table.buttons.exportData();
	console.log(data);
	*/
	
	$(".' . $js['display'][1] . '").on("click", function(e){
		
		e.preventDefault();
		$(this).toggleClass("' . $js['display'][2] . '");
		
		var index = $(this).attr("data-column");
		var column = table.column(index);
		
		/*
		$( table.table().header() ).find("th").eq(index' . (!empty($buttons['editintable']) && $buttons['editintable'] !== 'after' ? ' - 1' : null) . ').toggleClass("' . $js['noprint'] . '");
		*/
		
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
	
	if (!empty($buttons['editintable'])) {
		$print .= ', "selector" : "td:not(:' . ($buttons['editintable'] === 'before' ? 'first' : ($buttons['editintable'] === 'after' ? 'last' : 'first-child):not(:last')) . '-child)"';
	}
	
	$print .= '}';
	
}

// это для удаления выделенных строк
if (!empty($sets['delete'])) {
	$postprint .= '
	$(".' . $js['remove'] . '").click(function(){
		table.row(".' . $js['select'] . '").remove().draw(false);
	});
	';
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