<?php defined('isCMS') or die; ?>

<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) $i = 0; ?>

<?php if (isset($module -> settings -> place -> view) && $module -> settings -> place -> view === 'button') : ?>

<div id="place">
<input type="hidden" name="place" value="<?= $order -> place; ?>">
<?php foreach ($module -> settings -> place -> param as $key => $line) : ?>
	<div class="place-line line_<?= $key + 1; ?>">
	<?php foreach ($line as $place) : ?>
		<button type="button" name="placeselect" value="<?= $key + 1 . '-' . $place; ?>" <?php if ($place === $order -> place) echo 'class="selected"'; ?>>
			<?= (!empty($module -> settings -> schedule[$i] -> name)) ? $module -> settings -> schedule[$i] -> name : $place; ?>
		</button>
		<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) $i++; ?>
	<?php endforeach; ?>
	<?php if (!empty($module -> settings -> place -> aloneselect)) : ?>
		Свободных номеров: <span class="place-count"></span>
	<?php endif; ?>
	</div>
<?php endforeach; ?>
<?php unset($place); ?>
</div>

<?php else : ?>

<select name="place" id="place" <?php if ($module -> settings -> place -> multiselect) echo 'multiple="multiple" size="3"'; ?>>
<?php foreach ($module -> settings -> place -> param as $key => $line) : ?>
	<option value="" class="place-line line_<?= $key + 1; ?>" disabled>ряд: <?= $key + 1; ?></option>
	<?php foreach ($line as $place) : ?>
		<option name="placeselect" value="<?= $key + 1 . '-' . $place; ?>" <?php if ($order -> place === $key + 1 . '-' . $place) echo ' selected'; ?>>
			<?= (!empty($module -> settings -> schedule[$i] -> name)) ? $module -> settings -> schedule[$i] -> name : $place; ?>
		</option>
		<?php if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) $i++; ?>
	<?php endforeach; ?>
<?php endforeach; ?>
<?php unset($place); ?>
</select>

<?php endif; ?>
