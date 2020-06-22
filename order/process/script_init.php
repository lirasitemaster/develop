<?php defined('isCMS') or die; ?>
<script>

/* настройки календаря для выбора даты */

<?php if (!empty($module -> settings -> date -> range)) : ?>
$('.input-daterange input').each(function(){ $(this).datepicker({
<?php else : ?>
$('#date').datepicker({
<?php endif; ?>

<?php if (!empty($template -> datepicker)) : ?>
	
	updateViewDate: false,
	language: '<?= $lang -> lang; ?>',
	todayHighlight: true,
	daysOfWeekHighlighted: '0,6',
	disableTouchKeyboard: true,
	<?php if (!empty($module -> settings -> date -> alwaysview)) : ?>
	container: '#datepicker',
	<?php endif; ?>
	
	// Эти данные нужно скопировать для расписания, чтобы оно дополнялось этими датами,
	// а не формировалось на основе только дат в расписании
	<?php if (!empty($module -> settings -> week)) : ?>
		daysOfWeekDisabled: [<?php foreach ($module -> settings -> week -> disabledays as $key => $item) echo ($key === 0) ? $item : ',' . $item; ?>],
	<?php endif; ?>
	
	<?php if (!empty($module -> settings -> date -> hoidays)) : ?>
		datesDisabled: [<?php foreach ($module -> settings -> date -> holidays as $key => $item) echo ($key === 0) ? '\'' . $item . '\'' : ',\'' . $item . '\''; ?>],
	<?php endif; ?>
	
	format: 'dd.mm.yyyy',
	
	<?php if (!empty($module -> settings -> date -> firstday)) : ?>
		weekStart: <?= $module -> settings -> date -> firstday; ?>,
	<?php elseif ($lang -> lang === 'ru') : ?>
		weekStart: 1,
	<?php else : ?>
		weekStart: 0,
	<?php endif; ?>
	
	<?php if (!empty($module -> settings -> date -> startdate)) : ?>
		startDate: '<?= $module -> settings -> date -> startdate; ?>',
	<?php elseif (!empty($module -> settings -> date -> mindays)) : ?>
		startDate: '-<?= $module -> settings -> date -> mindays; ?>d',
	<?php else : ?>
		startDate: '0d',
	<?php endif; ?>
	
	<?php if (!empty($module -> settings -> date -> stopdate)) : ?>
		endDate: '<?= $module -> settings -> date -> stopdate; ?>',
	<?php elseif (!empty($module -> settings -> date -> maxdays)) : ?>
		endDate: '+<?= $module -> settings -> date -> maxdays; ?>d',
	<?php endif; ?>

<?php endif; ?>	

});

<?php if (!empty($module -> settings -> date -> range)) : ?>
});
<?php endif; ?>

<?php if (!empty($module -> settings -> date -> alwaysview)) : ?>
$('#date').data('datepicker').hide = function(){};
$('#date').datepicker('show');
<?php endif; ?>

</script>