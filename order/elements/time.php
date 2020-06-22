<?php defined('isCMS') or die; ?>

<?php if (!isset($module -> settings -> time -> type) || $module -> settings -> time -> type === 'auto') : ?>

<input type="hidden" name="time" value="<?= $module -> settings -> time -> param[0]; ?>">

<?php else : ?>

<?php if (isset($module -> settings -> time -> view) && $module -> settings -> time -> view === 'button') : ?>

<div id="time">

<input type="text" name="time" value="<?= $order -> time; ?>">

<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>

	<button type="button" name="timeselect" value="" class="hidden"></button>

<?php else : ?>

	<?php foreach ($module -> settings -> time -> param as $time) : ?>
		<button type="button" name="timeselect" value="<?= $time; ?>" <?php if ($time === $order -> time) echo 'class="selected"'; ?>><?= $time; ?></button>
	<?php endforeach; ?>
	<?php unset($time); ?>

<?php endif; ?>

</div>

<?php else : ?>

<select name="time" id="time">
	
	<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) : ?>
		
		<option value="" class="hidden"></option>
		
	<?php else : ?>
	
		<?php foreach ($module -> settings -> time -> param as $time) : ?>
			<option value="<?= $time; ?>" <?php if ($time === $order -> time) echo 'selected'; ?>><?= $time; ?></option>
		<?php endforeach; ?>
		<?php unset($time); ?>
	
	<?php endif; ?>
	
</select>

<?php endif; ?>

<?php endif; ?>
