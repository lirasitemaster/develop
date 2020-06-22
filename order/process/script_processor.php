<?php defined('isENGINE') or die; ?>
<script>

/**

самое важное:

1.
формат времени - dd.mm.yyyy, нужно сделать преобразование в заданный формат при записи в таблицу и после чтения

2. !
даты со скобками из расписания видятся не (нормально), остальные закрываются
но нет выбора времени по этим датам!

3.
когда устанавливаются запрещающие даты, они должны сливаться с уже существующим массивом запрещенных дат из
datesDisabled: [</?php foreach ($module -> settings -> date -> holidays as $key => $item) /?>]

**/

/*
* Правила:
* 
* сначала объявляются переменные, затем функции, затем идет код с комментариями
* участки кода озаглавлены в виде комментариев с решеткой, пишутся большими буквами
* имена функций и переменных имеют названия вида именаЗаглавнымиБуквами, впереди идет префикс:
*   func - функция,
*   v - простая переменная,
*   gv - глобальная,
*   a - массив или, иногда, объект
*	obj - объект DOM-структуры (элемент html)
* 
* Обязательная структура:
* 
* окончательные значения присваиваются и берутся из объектов $('[name="*"]'),
* где вместо * идет название объекта: place, date, time
* 
* Дерево:
* 
* #date                    - общий контейнер
*   [name="date"]          - поле хранения итоговой информации по выбранной дате
*   .input-daterange       - общий контейнер для диапазона дат
*     [name="date-from"]   - поле ввода начальной даты
*     [name="date-to"]     - поле ввода конечной даты
*     .date-summary        - поле вывода числа выбранных дней
* 
* #date
*   .input-daterange
*     [name="date"]
*     [name="date-from"]
*     [name="date-to"]
*     .date-summary
* 
* #date.input-daterange
*   [name="date"]
*   [name="date-from"]
*   [name="date-to"]
*   .date-summary
* 
* #time                    - общий контейнер
*   [name="time"]          - поле хранения итоговой информации по выбранному времени
*   [name="timeselect"]    - поля значений для выбора времени
* 
* #place                   - общий контейнер
*   [name="place"]         - поле хранения итоговой информации по выбранному месту
*   .place-line            - контейнер для хранения диапазона мест
*     [name="placeselect"] - поля значений для выбора места
*     .place-count         - поле вывода числа свободных мест в одном диапазоне
* 
*/

var aModuleData = <?= json_encode($module -> data); ?>;
var vKey = '<?= (!empty($module -> settings -> date -> key)) ? $module -> settings -> date -> key : 'time'; ?>'; // проверка на всякий случай, т.к. подобная проверка идет при инициализации модуля

// блок функций

function funcDateInitial() {
	
	// проверка и обновление текущей даты
	
	if ( !$('[name="date"]').val() ) {
		$('[name="date"]').val('<?= date('d.m.Y'); ?>');
		
		<?php if (!empty($module -> settings -> date -> range)) : ?>
			$('.input-daterange input').each(function(){ $(this).datepicker('setDate', '<?= date('d.m.Y'); ?>') });
		<?php else : ?>
			$('#date').datepicker('setDate', '<?= date('d.m.Y'); ?>');
		<?php endif; ?>
		
	}
	
}

// конец блока функций

funcDateInitial();



// # НАСТРОЙКИ ДИНАМИЧЕСКОГО ОБНОВЛЕНИЯ МОДУЛЯ ПРИ ВЫБОРЕ ДИАПАЗОНА ДАТ

<?php if (!empty($module -> settings -> date -> range)) : ?>

// блок функций

function funcDateRangeInitial() {
	
	/*
	* Функция инициализации диапазона дат
	* 
	* переменные: нет
	* на входе: ничего
	* на выходе: ничего
	* 
	* результат:
	* обновление полей [name="date-from"], [name="date-to"], [name="date"], 
	* если есть поле .date-summary, то вывод в него числа выбранных дней (1)
	*/
	
	if ( !$('.input-daterange [name="date-from"]').val() ) {
		$('.input-daterange [name="date-from"]').datepicker('setDate', new Date());
	}	
	if ( !$('.input-daterange [name="date-to"]').val() ) {
		$('.input-daterange [name="date-to"]').datepicker('setDate', new Date());
	}
	
	$('[name="date"]').val( $('.input-daterange [name="date-from"]').datepicker('getDate') );
	
	if ( $('.input-daterange').find('.date-summary') ) {
		$('.input-daterange .date-summary').text(1);
	}
}

function funcDateRangeSet() {
	
	/*
	* Функция определения диапазона дат
	* 
	* переменные: 
	* vDateFrom - начальная дата
	* vDateTo - конечная дата
	* vDateFromAbsolute - начальная дата в милисекундах, преобразуется в дни
	* vDateToAbsolute - конечная дата в милисекундах, преобразуется в дни
	* vDateSummary - разница между конечной и начальной датой
	* 
	* на входе: ничего
	* на выходе: ничего
	* 
	* результат:
	* обновление поля [name="date"],
	* вызов функции TimeSelect
	* если есть поле .date-summary, то вывод в него числа выбранных дней
	*/
	
	var vDateFrom, vDateTo, vDateFromAbsolute, vDateToAbsolute, vDateSummary;
	
	vDateFrom = $('.input-daterange [name="date-from"]').val();
	vDateTo = $('.input-daterange [name="date-to"]').val();
	
	if (vDateTo === vDateFrom || !vDateTo) {
		$('[name="date"]').val(vDateFrom);
	} else {
		$('[name="date"]').val(vDateFrom + ',' + vDateTo);
	}
	
	funcTimeSelect();
	
	if ( $('.input-daterange').find('.date-summary') ) {
		
		vDateFromAbsolute = $('.input-daterange input[name="date-from"]').datepicker('getDate');
		vDateFromAbsolute = Date.parse(vDateFromAbsolute) / (<?= TIME_DAY; ?> * 1000);
		vDateToAbsolute = $('.input-daterange input[name="date-to"]').datepicker('getDate');
		vDateToAbsolute = Date.parse(vDateToAbsolute) / (<?= TIME_DAY; ?> * 1000);
		vDateSummary = vDateToAbsolute - vDateFromAbsolute;
		
		if (vDateSummary >= 0 && vDateSummary <= 1) {
			vDateSummary = 1;
		} else if (vDateSummary < 0) {
			vDateSummary = 0;
		} else {
			vDateSummary = Math.ceil(vDateSummary);
		}
		
		$('.input-daterange .date-summary').text(vDateSummary);
	}
}

// конец блока функций

// при инициализации скрипта, вызываем функцию DateRangeInitial вместо DateRangeSet,
// чтобы не было лишних вычислений и лишнего вызова функции TimeSelect

funcDateRangeInitial();

// если изменилось поле [name="date-from"], то
// - получаем начальную и конечную даты
// - устанавливаем для поля [name="date-to"] начальную дату (чтобы в качестве конечной нельзя было выбрать дату раньше начальной)
// - сравниваем начальную и конечную даты, и если начальная дата больше конечной, задаем конечной ту же дату, что и начальная
// - обновляем данные через функцию DateRangeSet

$('.input-daterange [name="date-from"]').change(function(){
	var vDateFrom, vDateFromAbsolute, vDateTo, vDateToAbsolute;
	
	vDateFrom = $(this).val();
	vDateFromAbsolute = $(this).datepicker('getDate');
	vDateFromAbsolute = Date.parse(vDateFromAbsolute)/1000;
	vDateTo = $('.input-daterange [name="date-to"]').val();
	if (vDateTo && vDateTo !== 0) {
		vDateToAbsolute = $('.input-daterange [name="date-to"]').datepicker('getDate');
		vDateToAbsolute = Date.parse(vDateToAbsolute)/1000;
	} else {
		vDateToAbsolute = 0;
	}
	
	$('.input-daterange [name="date-to"]').datepicker('setStartDate', vDateFrom);
	
	if ( vDateFromAbsolute > vDateToAbsolute ) {
		$('.input-daterange [name="date-to"]').val(vDateFrom).datepicker('setDate', vDateFrom);
	}
	
	funcDateRangeSet();
});

// если изменилось поле [name="date-to"], то
// - обновляем данные через функцию DateRangeSet

$('.input-daterange [name="date-to"]').change(function(){
	funcDateRangeSet();
});

<?php endif; ?>



// # НАСТРОЙКИ ДИНАМИЧЕСКОГО ОБНОВЛЕНИЯ МОДУЛЯ ПРИ ВЫБОРЕ ВРЕМЕНИ ИЛИ МЕСТА

// блок функций

function funcTimeSelect() {
	
	/*
	* Функция определения времени
	* 
	* переменные: 
	* vDate - значение текущей даты
	* vDateFromAbsolute - начальная дата в милисекундах, преобразуется в дни, нужна для цикла
	* vDateToAbsolute - конечная дата в милисекундах, преобразуется в дни, нужна для цикла
	* vDateCurr - текущее значение даты в цикле, нужна для записи в массив aDate
	* aDate - массив выбранных дат, используется если включен диапазон
	* vTime - значение текущего времени
	* vPlace - значение текущего места
	* vCount - число свободных мест, используется если включен вывод одного места
	* objItem - текущий объект в цикле перебора мест и/или времени
	* 
	* на входе: ничего
	* на выходе: ничего
	* 
	* результат:
	* блокировка занятых дат, мест, времени (если был включен диапазон дат, то учитывается попадание в весь диапазон)
	* подсчет и вывод числа свободных мест для каждого диапазона мест (если был включен вывод одного места)
	*/
	
	// !!! нет проверки значений даты, времени и места и установки умолчаний, если они пусты
	
	var vDate, vDateFromAbsolute, vDateToAbsolute, vDateCurr, aDate, vTime, vPlace, vCount, objItem;
	
	vDate = $('[name="date"]').val();
	
	// разбор каждой даты из диапазона, заполнение массива aDate
	<?php if (!empty($module -> settings -> date -> range)) : ?>
	aDate = [];
	if (vDate.indexOf(',') > 0) {
		
		vDateFromAbsolute = $('.input-daterange input[name="date-from"]').datepicker('getDate');
		vDateFromAbsolute = Date.parse(vDateFromAbsolute) / (<?= TIME_DAY; ?> * 1000);
		vDateFromAbsolute = Math.ceil(vDateFromAbsolute);
		
		vDateToAbsolute = $('.input-daterange input[name="date-to"]').datepicker('getDate');
		vDateToAbsolute = Date.parse(vDateToAbsolute) / (<?= TIME_DAY; ?> * 1000);
		vDateToAbsolute = Math.ceil(vDateToAbsolute);
		
		while (vDateFromAbsolute < vDateToAbsolute) {
			vDateCurr = new Date(vDateFromAbsolute);
			aDate.push( ('0' + vDateCurr.getDate().toString()).slice(-2) + '.' + ('0' + (vDateCurr.getMonth() + 1).toString()).slice(-2) + '.' + vDateCurr.getFullYear().toString() );
			vDateFromAbsolute++;
		}
		
	}
	<?php endif; ?>
	
	<?php foreach ([['time', 'place'], ['place', 'time']] as $key) : ?>
	
	if (vKey === '<?= $key[0]; ?>') {
		
		v<?= ucfirst($key[1]); ?> = $('[name="<?= $key[1]; ?>"]').val();
		
		// автоматическая подсветка выбранных времени и места
		$('[name="<?= $key[1]; ?>select"]').each(function(){
			if ($(this).val() == v<?= ucfirst($key[1]); ?>) {
				$(this).addClass('selected');
			}
		});
		
		// разбор каждого объекта выбора времени или места
		// - очистка блокировок
		// - проверка наличия места, даты и времени в списке занятых (массив aModuleData)
		// - если был диапазон дат, то для каждой даты из диапазона (массив aDate)
		// - блокировка для выбора, если они присутствуют в списке занятых
		$('#<?= $key[0]; ?> [name="<?= $key[0]; ?>select"]').each(function(){
			
			objItem = $(this);
			$(this).removeAttr('disabled').removeClass('disabled');
			
			<?php if (!empty($module -> settings -> date -> range)) : ?>
			if (vDate.indexOf(',') > 0) {
				$.each(aDate, function(index,value){
					//console.log( aModuleData[v<?= ucfirst($key[1]); ?>][value.toString()] );
					if (
						aModuleData[v<?= ucfirst($key[1]); ?>] &&
						aModuleData[v<?= ucfirst($key[1]); ?>][value.toString()] &&
						$.inArray(objItem.val(), aModuleData[v<?= ucfirst($key[1]); ?>][value.toString()]) >= 0
					) {
						objItem.attr('disabled', true).addClass('disabled');
					}
				});
			} else {
			<?php endif; ?>
				if (
					aModuleData[v<?= ucfirst($key[1]); ?>] &&
					aModuleData[v<?= ucfirst($key[1]); ?>][vDate] &&
					$.inArray(objItem.val(), aModuleData[v<?= ucfirst($key[1]); ?>][vDate]) >= 0
				) {
					objItem.attr('disabled', true).addClass('disabled');
				}
			<?php if (!empty($module -> settings -> date -> range)) : ?>
			}
			<?php endif; ?>
			
			if ( $('[name="time"]').val() === objItem.val() && objItem.hasClass('disabled') ) {
				$('[name="time"]').val('');
				objItem.removeClass('selected');
			}
			
		});
		
		// если был включен вывод одного места, то идет подсчет и вывод числа свободных мест для каждого диапазона мест
		<?php if (!empty($module -> settings -> place -> aloneselect)) : ?>
		if ($('#place').find('.place-line') && $('#place').find('.place-count')) {
			$('#place .place-line').each(function(){
				vCount = $(this).find('[name="placeselect"]').not('.disabled').length;
				$(this).find('[name="placeselect"]').addClass('hidden');
				$(this).find('[name="placeselect"]').not('.disabled').first().removeClass('hidden');
				$(this).find('.place-count').text(vCount);
			});
		};
		<?php endif; ?>
		
	}
	<?php endforeach; ?>
	
}

// конец блока функций

// при запуске
// если был включен вывод одного места,
// то идет подсчет и вывод числа свободных мест для каждого диапазона мест

<?php if (!empty($module -> settings -> place -> aloneselect)) : ?>
if ($('#place').find('.place-line') && $('#place').find('.place-count')) {
	$('#place .place-line').each(function(){
		vCount = $(this).find('[name="placeselect"]').not('.disabled').length;
		$(this).find('[name="placeselect"]').addClass('hidden');
		$(this).find('[name="placeselect"]').not('.disabled').first().removeClass('hidden');
		$(this).find('.place-count').text(vCount);
	});
};
<?php endif; ?>

// при запуске
// если у поля даты есть значение, то вызываем функции
// блокировки запрещенных дней и дат
// блокировки занятых дат, мест, времени

if ( $('[name="date"]').val() ) {
	<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>
		if ( $('[name="place"]').val() ) {
			funcTimeSelectRefresh( $('[name="place"]').val() );
		}
	<?php endif; ?>
	funcTimeSelect();
}

// при смене даты вызываем функции
// блокировки запрещенных дней и дат
// блокировки занятых дат, мест, времени

$('[name="date"]').change(function(){
	<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>
		if ( $('[name="place"]').val() ) {
			funcTimeSelectRefresh( $('[name="place"]').val() );
		}
	<?php endif; ?>
	funcTimeSelect();
});

// при смене месяца также вызываем функции
// блокировки запрещенных дней и дат
// блокировки занятых дат, мест, времени
// !!! работает только при выключенной опции updateViewDate

<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>
$('#date').datepicker().on('changeMonth', function(change){
	if ( $('[name="place"]').val() ) {
		funcMonthChange( $('[name="place"]').val(), change.date );
	}
	funcTimeSelect();
});
<?php endif; ?>

// обработка события, когда происходит выбор места или времени

<?php foreach ([['time', 'place'], ['place', 'time']] as $key) : ?>

<?php $k = $key[0]; ?>
$('body').on(
<?php if ( isset($module -> settings -> $k -> view) && $module -> settings -> $k -> view === 'button' ) : ?>
'click', '[name="<?= $key[0]; ?>select"]',
<?php else : ?>
'change', '[name="<?= $key[0]; ?>"]',
<?php endif; ?>

function(){
	
	// если включено расписание, то происходит сброс времени
	// если же расписания нет, то время для каждого места и даты общее и сбрасывать его не нужно
	<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>
		$('[name="time"]').val('');
	<?php endif; ?>
	
	// если включен мультивыбор на места
	<?php if ($key[0] === 'place' && !empty($module -> settings -> place -> multiselect)) : ?>
		
		var vPlaces;
		
		if ( $('[name="place"]').val() && $(this).hasClass('selected') ) {
			// убираем место
			vPlaces = $('[name="place"]').val().replace($(this).val() + ',', '').replace(',' + $(this).val(), '');
		} else if ( $('[name="place"]').val() ) {
			// добавляем место
			vPlaces = $('[name="place"]').val() + ',' + $(this).val();
		} else {
			// добавляем первое место
			vPlaces = $(this).val();
		}
		
		$('[name="place"]').val(vPlaces);
		$(this).toggleClass('selected');
		
	<?php else : ?>
		
		<?php if ( isset($module -> settings -> $k -> view) && $module -> settings -> $k -> view === 'button' ) : ?>
		
		$('[name="<?= $key[0]; ?>"]').val( $(this).val() );
		$(this).parents('#<?= $key[0]; ?>').find('[name="<?= $key[0]; ?>select"]').each(function(){
			$(this).removeClass('selected');
		});
		$(this).addClass('selected');
		
		<?php else : ?>
		
		$(this).find('[name="<?= $key[0]; ?>select"]').each(function(){
			$(this).removeClass('selected');
		});
		
		<?php endif; ?>
		
	<?php endif; ?>
	
	if (vKey == '<?= $key[1]; ?>') {
		funcTimeSelect();
	}
	
});

<?php unset($k); ?>
<?php endforeach; ?>

// # НАСТРОЙКИ ДИНАМИЧЕСКОГО ОБНОВЛЕНИЯ МОДУЛЯ ПРИ НАЛИЧИИ РАСПИСАНИЯ

<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>

var objTimeSelect = $('#time <?= (isset($module -> settings -> time -> view) && $module -> settings -> time -> view === 'button') ? 'button' : 'option'; ?>.hidden');
var aSchedule = <?= json_encode($module -> settings -> schedule); ?>;

// блок функций

function funcTimeSelectRefresh(value) {
	
	/*
	* Функция переопределения времени
	* 
	* переменные: 
	* vDaySelect - выбранный день недели
	* aPlaceSelect - массив значений для конкретного места
	* aTimeSelect - массив времени для выбранного дня и места
	* aDisableDatas - массив запрещенных дат: base - заданные дни, allow - разрешенные даты, disable - запрещенные даты
	* 
	* на входе: значение выбранного места
	* на выходе: ничего
	* 
	* результат:
	* обновление времени
	* блокировка запрещенных дней и дат
	*/
	
	var vDaySelect, aPlaceSelect, aTimeSelect, aDisableDatas;
	
	// сбрасываем все время - просто удаляем html элементы
	$('#time <?= (isset($module -> settings -> time -> view) && $module -> settings -> time -> view === 'button') ? 'button' : 'option'; ?>').detach();
	
	// формируем массив значений для конкретного места
	aPlaceSelect = funcSearchInArray(aSchedule, 'place', value);
	
	if (!aPlaceSelect) {
		return;
	}
	
	// Блокируем дни недели, указанные в настройках
	$('#date').datepicker('setDaysOfWeekDisabled', aPlaceSelect.disabledays);
	
	// Формируем массив с датами - указанные в настройках, запрещенные и разрешенные и заполняем его
	aDisableDatas = {'base' : [], 'disable' : [], 'allow' : []};
	aDisableDatas.base = funcSearchInArray(aSchedule, 'place', value).dates;
	//aDisableDatas.base = [{'special' : ['6(1)', '6(2)']},{'special' : ['6(3)']}]; // тестовые значения
	
	// Первый этап разбора - значения из массива превращаем в даты и разбиваем на разрешенные и запрещенные
	$.each(aDisableDatas.base, function(index,val){
		var a = funcMonthDaysSelect(val.special, new Date( $('#date').datepicker('getUTCDate') ));
		if (a.disable && a.disable.length > 0) {
			$.each(a.disable, function(index,vl){
				if ( $.inArray(vl, aDisableDatas.disable) < 0 ) {
					aDisableDatas.disable.push(vl);
				}
			});
			$.each(a.dates, function(index,vl){
				if ( $.inArray(vl, aDisableDatas.allow) < 0 ) {
					aDisableDatas.allow.push(vl);
				}
			});
		}
	});
	
	// !!! второй этап разбора - перебираем массив запрещенных дат и удаляем те из них,
	// которые встречаются в массиве разрешенных дат
	// на самом деле - это следствие плохой реализации предыдущего этапа,
	// нужно переделывать условия, чтобы оба массива правильно формировались сразу же
	$.each(aDisableDatas.allow, function(index,vl){
		if ( $.inArray(vl, aDisableDatas.disable) >= 0 ) {
			aDisableDatas.disable.splice( $.inArray(vl, aDisableDatas.disable), 1 );
		}
	});
	
	// обновляем список запрещенных дат для данного месяца
	$('#date').datepicker('setDatesDisabled', aDisableDatas.disable);
	
	// если текущая дата оказывается заблокированной, то она просто сбрасывается
	if ( $('.datepicker .active').is('.disabled-date') ) {
		$('.datepicker .active').removeClass('active');
		$('[name="date"]').val(0);
	}
	
	// формируем массив времени для выбранной даты и места
	vDaySelect = new Date( $('#date').datepicker('getUTCDate') ).getDay();
	vDateSelect = new Date( $('#date').datepicker('getUTCDate') );
	
	vDateSelect = ('0' + vDateSelect.getDate()).slice(-2) + '.' + ('0' + (vDateSelect.getMonth() + 1).toString()).slice(-2) + '.' + vDateSelect.getFullYear().toString();
	
	$.each(aPlaceSelect.dates, function(index,val){
		if ($.inArray(vDaySelect, val.week) >= 0 || $.inArray(vDaySelect.toString(), val.week) >= 0) {
			aTimeSelect = val.param;
		} else if ($.inArray(vDateSelect, val.special) >= 0 || $.inArray(vDateSelect.toString(), val.special) >= 0) {
			aTimeSelect = val.param;
		}
	});
	// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	// сюда надо дописать условие, если дата находится в массиве разрешенных дат,
	// то выборку времени ведем по этой дате
	// проверка:
	// console.log( aPlaceSelect.dates );
	
	// сбрасываем время, если в новом списке нет выбранного нами времени
	// это позволяет сохранить выбранное время при переключении дат
	if ($.inArray($('[name="time"]').val(), aTimeSelect) < 0) {
		$('[name="time"]').val('');
	}
	
	// формируем список времени
	$.each(aTimeSelect, function(index,value){
		var objTimeNew = objTimeSelect.clone().val(value).html(value).removeClass('hidden');
		
		if ( $('[name="time"]').val() && $('[name="time"]').val() == value ) {
			objTimeNew.addClass('selected');
		}
		
		$('#time').append(objTimeNew);
	});
	
}

function funcMonthDaysSelect(array, date) {
	
	/*
	* Функция переопределения времени
	* 
	* переменные: 
	* aMonth - рабочий массив
	* vDay - номер дня, полученный из входящей строки
	* vWeek - номер недели, полученный из входящей строки
	* 
	* на входе: массив определенных дней
	* на выходе: массив разрешенных и запрещенных дат
	* 
	* результат:
	* перевод заданных дней в даты для текущего выбранного месяца
	*/
	
	var aMonth, vDay, vWeek;
	
	aMonth = {
		'currdate' : date,
		'newdate' : '',
		'truedate' : '',
		'month' : [],
		'dates' : [],
		'disable' : []
	};
	
	// создаем массив текущего месяца,
	// индексы - номера недель и номера дней в неделе,
	// значения - числа месяца
	
	for (
		i = 1, n = 0, l = parseInt( new Date( aMonth.currdate.getFullYear(), aMonth.currdate.getMonth() + 1, 0 ).getDate().toString() );
		i <= l;
		i++
	) {
		aMonth.newdate = parseInt( new Date( aMonth.currdate.getFullYear(), aMonth.currdate.getMonth(), i ).getDay().toString() );
		aMonth.month[i] = {
			'day' : aMonth.newdate,
			'week' : n
		};
		if (aMonth.newdate < 1) {
			n++;
		}
	}
	
	// проверка результата:
	// console.log( aMonth.month );
	
	// проверяем массив чисел на доп.параметры - в скобках указан порядковый номер дня в месяце (напр. первая суббота),
	// находим дату для каждого дня и недели
	// и формируем массив, в котором по порядку указаны полученные даты
	
	$.each(array, function(index, value) {
		
		if ( !$.isNumeric(value) && value.indexOf('(') + 1 && value.indexOf(')') + 1 ) {
			
			vDay = parseInt( value.replace(/(.)?\((\w)?\)/,'$1') );
			vWeek = parseInt( value.replace(/(.)?\((\w)?\)/,'$2') ) - 1;
			
			if (vDay === 7) {
				vDay = 0;
			}
			
			$.each(aMonth.month, function(date){
				aMonth.truedate = ('0' + date).slice(-2) + '.' + ('0' + (aMonth.currdate.getMonth() + 1).toString()).slice(-2) + '.' + aMonth.currdate.getFullYear().toString();
				
				if ( $(this)[0].day == vDay && $(this)[0].week == vWeek ) {
					aMonth.dates.push( aMonth.truedate );
				} else if ( $(this)[0].day == vDay && $.inArray(aMonth.truedate, aMonth.disable) < 0 ) {
					aMonth.disable.push( aMonth.truedate );
				}
			});
			
		}
		
	});
	
	// проверка результата:
	// console.log(aMonth.dates);
	
	// проверяем массив дат и удаляем из массива запрещенных дней исключения
	
	$.each(aMonth.dates, function(index, value) {
		aMonth.disable.splice( $.inArray(value, aMonth.disable), 1 );
		array.push( value ); // <<<<<<<<<< здесь идет задвоение дат, в общем-то ну и хрен с ними, но непорядок
	});
	
	// проверка результата:
	//console.log(aMonth.disable);
	
	return {"dates" : aMonth.dates, "disable" : aMonth.disable};
	
}

function funcSearchInArray(source, key, value) {
	
	/*
	* Функция поиска в массиве
	* 
	* переменные: нет
	* 
	* на входе: массив для поиска, ключ поиска, значение ключа
	* на выходе: строка массива, в которой есть ключ с заданным значением
	* 
	* результат:
	* поиск в массиве строки с заданными ключом и значением
	* в случае успеха, возвращается строка, которая также может быть массивом
	* в случае отсутствия значений, возвращается значение false
	*/
	
	for (var i = 0, ln = source.length; i < ln; i++) {
		if (source[i][key] == value) {
			return source[i];
		}
	}
	
	return false;
	
}

function funcMonthChange(place, date) {
	
	/*
	* Функция переопределения времени при смене месяца
	* 
	* переменные: 
	* aDisableDatas - массив запрещенных дат: base - заданные дни, allow - разрешенные даты, disable - запрещенные даты
	* 
	* на входе: значение выбранного места, дата
	* на выходе: ничего
	* 
	* результат:
	* блокировка запрещенных дней и дат в отображаемом месяце
	*/
	
	var aDisableDatas;
	
	// ВЫДРАННЫЙ КУСОК КОДА
	
	// Формируем массив с датами - указанные в настройках, запрещенные и разрешенные и заполняем его
	aDisableDatas = {'base' : [], 'disable' : [], 'allow' : []};
	aDisableDatas.base = funcSearchInArray(aSchedule, 'place', place).dates;
	//aDisableDatas.base = [{'special' : ['6(1)', '6(2)']},{'special' : ['6(3)']}]; // тестовые значения
	
	// Первый этап разбора - значения из массива превращаем в даты и разбиваем на разрешенные и запрещенные
	$.each(aDisableDatas.base, function(index,val){
		var a = funcMonthDaysSelect(val.special, date);
		if (a.disable && a.disable.length > 0) {
			$.each(a.disable, function(index,vl){
				if ( $.inArray(vl, aDisableDatas.disable) < 0 ) {
					aDisableDatas.disable.push(vl);
				}
			});
			$.each(a.dates, function(index,vl){
				if ( $.inArray(vl, aDisableDatas.allow) < 0 ) {
					aDisableDatas.allow.push(vl);
				}
			});
		}
	});
	
	// !!! второй этап разбора - перебираем массив запрещенных дат и удаляем те из них,
	// которые встречаются в массиве разрешенных дат
	// на самом деле - это следствие плохой реализации предыдущего этапа,
	// нужно переделывать условия, чтобы оба массива правильно формировались сразу же
	$.each(aDisableDatas.allow, function(index,vl){
		if ( $.inArray(vl, aDisableDatas.disable) >= 0 ) {
			aDisableDatas.disable.splice( $.inArray(vl, aDisableDatas.disable), 1 );
		}
	});
	
	// обновляем список запрещенных дат для данного месяца
	$('#date').datepicker('setDatesDisabled', aDisableDatas.disable);
	
}

// конец блока функций

// при инициализации, если место уже задано,
// отмечаем это место как выбранное
// и вызываем функцию блокировки запрещенных дней и дат

if ( $('[name="place"]').val() ) {
	$('[name="placeselect"][value="' + $('[name="place"]').val() + '"]').attr('selected', true).addClass('selected');
	funcTimeSelectRefresh( $('[name="place"]').val() );
}

// при смене места вызываем функцию блокировки запрещенных дней и дат

$('[name="placeselect"]').click(function(){
	funcTimeSelectRefresh( $(this).val() );
});

<?php endif; ?>

</script>