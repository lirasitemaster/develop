<?php defined('isCMS') or die;

// СКОРЕЕ ВСЕГО НАДО БУДЕТ ЗАМЕНИТЬ array_merge В ДРАЙВЕРЕ В ЗАПИСИ НА array_replace

/*
пишем модуль, который для начала просто читает данные из базы данных

потом он будет их выводить в массив

потом он будет читать настройки, и в зависимости от настроек, сможет выводить:
- нужные строки (фильтр по имени)
- нужные поля
- определенные поля - только для чтения, другие - также для записи
- определение разрешений внутри массивов значений: для добавления, для удаления, неассоциированные массивы или ассоциированные массивы с указанием ключей
- определение разрешений для записей: добавление, удаление
- именование полей
- все это делать через настройки, соответственно, для них можно задавать мультиязычные значения
- вывод в виде массива (это самый первый этап)
- вывод в виде json (возможно, понадобится для преобразования)
- вывод в виде таблицы и в виде формы
- запись по аяксу (потом)

*/

//echo '<pre>' . print_r($module, 1) . '</pre>';

$db = $module -> this . set($module -> settings['select']['names'], ':' . $module -> settings['select']['names']);
$parameters = objectIs($module -> settings['select']) ? [] : null;

if (!empty($module -> settings['select']['allow'])) {
	$parameters['allow'] = $module -> settings['select']['allow'];
}
if (!empty($module -> settings['select']['deny'])) {
	$parameters['deny'] = $module -> settings['select']['deny'];
}
if (!empty($module -> settings['select']['filter'])) {
	$parameters['filter'] = $module -> settings['select']['filter'];
}

$module -> data = dbUse($db, 'select', $parameters);

unset($db, $parameters);

// таблица выводится не одноуровневая, к тому же с разным числом столбцов
// из-за этого скрипт ее не понимает и обваливается,
// поэтому надо массив, еще до вывода таблицы, подготовить

// все поля по-умолчанию будут текстовыми,
// все вложенные массивы будут преобразованы в json-формат

// кроме того, мы пробежимся по всему массиву и составим карту полей

// КСТАТИ, ДА... нам спешить некуда и экономить ресурсы тоже особо незачем
// МЫ ЖЕ В АДМИНКЕ !

// При построении таблицы, мы пляшем от base и map
// и только за значениями мы обращаемся к tbl

$base = objectIs($module -> settings['base']) ? $module -> settings['base'] : ['id', 'name', 'type', 'self', 'parent', 'ctime', 'mtime'];
$map = objectIs($module -> settings['data']) ? $module -> settings['data'] : [];
$tbl = [];

$schema = dbUse('schemas:' . ($module -> this ? $module -> this : $module -> param), 'select');
$schema = objectIs($schema) ? array_shift($schema) : null;
$schema = objectIs($schema) ? $schema['data'] : null;

$sctrigger = objectIs($schema) ? true : null;

if (empty($sctrigger)) {
	$schema = [
		'title' => $module -> this ? $module -> this : $module -> param,
		'type' => 'object',
		'properties' => []
	];
}

//print_r($module -> data);

if (objectIs($module -> data)) {
	
	$_SESSION['writedb'] = $module -> this ? $module -> this : $module -> param;
	
	foreach ($module -> data as $item) {
		
		// здесь мы записываем данные в дополнительную таблицу, которая нам будет нужна в скрипте
		if (
			empty($module -> settings['edit']) ||
			!objectIs($module -> settings['edit'])
		) {
			$tbl[$item['name']] = $item;
		} else {
			foreach ($module -> settings['edit'] as $i) {
				$tbl[$item['name']][$i] = $item[$i];
			}
			unset($i);
		}
		
		// здесь мы формируем массив map исходя из всех имеющихся полей data в разных записях
		if (
			objectIs($item['data']) &&
			(!empty($module -> settings['data']) || empty($sctrigger))
		) {
			foreach ($item['data'] as $k => $i) {
				if (
					(
						objectIs($module -> settings['data']) && in_array($k, $module -> settings['data']) ||
						$module -> settings['data'] === true
					) &&
					!in_array($k, $map)
				) {
					$map[] = $k;
				}
				if (
					empty($sctrigger) &&
					!array_key_exists($k, $schema['properties'])
				) {
					$schema['properties'][$k] = moduleDataTables_createSchema($k, $i);
				}
			}
			unset($k, $i);
		}
		
	}
	unset($item);
	
	// здесь мы повторно пробегаемся по таблице, вставляя отсутствующие поля data с пустыми значениями
	
	if (objectIs($map)) {
		foreach ($module -> data as &$item) {
			if (objectIs($item['data'])) {
				foreach ($map as $i) {
					if (!array_key_exists($i, $item['data'])) {
						
						// здесь мы записываем данные в дополнительную таблицу, которая нам будет нужна в скрипте
						$tbl[$item['name']]['data'][$i] = null;
						
						$item['data'][$i] = null;
						
					}
				}
				unset($i);
			} else {
				$item['data'] = array_fill_keys($map, null); 
			}
		}
		unset($item);
	}
	
}

//print_r($schema);
//print_r($tbl);
//print_r($map);



$name = $module -> param . 'DataTable';
$sets = &$module -> settings['options'];
$class = &$module -> settings['classes'];
$labels = &$module -> settings['labels'];
$js = &$module -> settings['js'];
$buttons = &$module -> settings['buttons'];

$js['display'] = !empty($js['display']) ? dataParse($js['display']) : [null, null];
$class['display'] = !empty($class['display']) ? dataParse($class['display']) : [null, null];

function moduleDataTables_mergeClasses($arrTarget, $arrMerged) {
	
	foreach ($arrMerged as $k => $i) {
		if (objectIs($i)) {
			$arrTarget[$k] = moduleDataTables_mergeClasses($arrTarget[$k], $i);
		} elseif (!empty($i)) {
			$arrTarget[$k] .= ' ' . $i;
		}
	}
	unset($k, $i);
	
	return $arrTarget;
	
}

function moduleDataTables_createSchema($k, $i) {
	
	$arrTarget = [];
	
	if (is_array($i) && (!set($i) || objectKeys($i))) {
		
		$arrTarget = [
			'type' => 'object',
			'title' => $k,
			'options' => [
				'collapsed' => true
			],
			'properties' => []
		];
		
		if (objectIs($i)) {
			foreach ($i as $ki => $ii) {
				$arrTarget['properties'][$ki] = moduleDataTables_createSchema($ki, $ii);
			}
			unset($ki, $ii);
		}
		
	}
	
	/**/
	if (objectIs($i)) {
		if (objectKeys($i)) {
			
		} else {
			$arrTarget = [
				'type' => 'array',
				'format' => 'table'
			];
		}
	} elseif ($i === true) {
		$arrTarget['type'] = 'boolean';
	} elseif (set($i)) {
		$arrTarget['type'] = 'string';
	}
	/**/
	
	return $arrTarget;
	
}

$class = moduleDataTables_mergeClasses($class, $js);


//echo '<pre>' . print_r($map, 1) . '</pre>';
//echo '<pre>' . print_r($tbl, 1) . '</pre>';

//unset($tbl);

/*

это дает еще кнопки - вперед, назад, в начало, в конец
"pagingType": "full_numbers"

это сохраняет сортировку при обновлении страницы
stateSave: true

это отменяет кнопки и дает прокрутку вниз
"scrollY":        "200px",
"scrollCollapse": true,
"paging":         false

это добавляет скролл по горизонтали
"scrollX": true

пример разделения заголовков на две строки
<table id="example" class="display" style="width:100%">
	<thead>
		<tr>
			<th rowspan="2">Name</th>
			<th colspan="2">HR Information</th>
			<th colspan="3">Contact</th>
		</tr>
		<tr>
			<th>Position</th>
			<th>Salary</th>
			<th>Office</th>
			<th>Extn.</th>
			<th>E-mail</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
		</tr>
	</tbody>
</table>

это дает поиск только в активной колонке, по которой идет сортировка
$(document).ready(function() {
    var t = $('#example').DataTable( {
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]]
    } );
 
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
} );

это дает кнопку, добавляющую строку
$(document).ready(function() {
    var t = $('#example').DataTable();
    var counter = 1;
 
    $('#addRow').on( 'click', function () {
        t.row.add( [
            counter +'.1',
            counter +'.2',
            counter +'.3',
            counter +'.4',
            counter +'.5'
        ] ).draw( false );
 
        counter++;
    } );
 
    // Automatically add a first row of data
    $('#addRow').click();
} );

это дает строку фильтрации по каждой колонке
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#example tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#example').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change clear', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
} );

это дает форму внутри таблицы
$(document).ready(function() {
    var table = $('#example').DataTable({
        columnDefs: [{
            orderable: false,
            targets: [1,2,3]
        }]
    });
 
    $('button').click( function() {
        var data = table.$('input, select').serialize();
        alert(
            "The following data would have been submitted to the server: \n\n"+
            data.substr( 0, 120 )+'...'
        );
        return false;
    } );
} );

это выводит строку, которая позволяет отображать или скрывать колонки
$(document).ready(function() {
    var table = $('#example').DataTable( {
        "scrollY": "200px",
        "paging": false
    } );
 
    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr('data-column') );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );
} );

это для выделения и удаления строки
$(document).ready(function() {
    var table = $('#example').DataTable();
 
    $('#example tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
 
    $('#button').click( function () {
        table.row('.selected').remove().draw( false );
    } );
} );

есть сортировка по нескольким колонкам сразу, удерживая shift

теперь упорядочить эти моменты и обернуть в настройки модуля
НО ДЛЯ ЭТОГО надо еще инициализировать скрипты вначале шаблона

например, разрешать редактирование, добавление и удаление по переключателю
https://mdbootstrap.com/plugins/jquery/table-editor/#content-editor
https://mdbootstrap.com/docs/jquery/tables/editable/
что хотелось бы исправить:
при выключении, фокус остается и удаляется всегда эта колонка

что хотелось бы добавить:
выделение нескольких колонок по шифту
одновременное изменение полей сразу у нескольких выделенных колонок
пусть будет копирование колонки после текущей выделенной со всем ее содержимым

*/?>