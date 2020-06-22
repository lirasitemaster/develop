<?php defined('isCMS') or die; ?>

<?php if ($item['type'] === 'textarea') : ?>

	<label class="field-text__title field-text__title_notreq"><?= $item['default']; ?><?= (!empty($item['required'])) ? ' *' : ''; ?></label>
	<textarea
		id="reservation-<?= $item['name']; ?>"
		class="input_text <?= $item['class']; ?>"
		name="data[<?= $item['name']; ?>]"
	><?= (!empty($module -> data[$item['name']])) ? $module -> data[$item['name']] : ''; ?></textarea>
	
	<div class="help-block"><?php
		unset($module -> var['errors']['fail']);
		if (
			!empty($item['name']) &&
			!empty($module -> var['errors']) &&
			is_array($module -> var['errors']) &&
			in_array($item['name'], $module -> var['errors'])
		) {
			global $lang;
			if (objectObject($lang -> errors, $item['name'])) {
				echo objectObject($lang -> errors, $item['name']);
			} else {
				if ($item['type'] !== 'select') {
					echo 'Введенный текст слишком короткий или содержит недопустимые символы';
				} else {
					echo 'Не выбрано допустимое значение';
				}
			}
		}
	?></div>

<?php elseif ($item['type'] !== 'checkgroup') : ?>

<div class="form-group field-text">
	<label class="field-text__label">
		
		<?php if ($item['type'] !== 'select') : ?>
		<span class="field-text__title"><?= $item['default']; ?><?= (!empty($item['required'])) ? ' *' : ''; ?></span>
		<input
			type="<?= $item['type']; ?>"
			id="reservation-<?= $item['name']; ?>"
			class="input_text <?= $item['class']; ?>"
			name="data[<?= $item['name']; ?>]"
			placeholder="<?= $item['placeholder']; ?>"
			<?= (!empty($item['required'])) ? 'required aria-required="true"' : ''; ?>
			<?= (!empty($module -> data[$item['name']])) ? 'value="' . $module -> data[$item['name']] . '"' : ''; ?>
		>
		<?php else : ?>
		<select
			id="reservation-<?= $item['name']; ?>"
			class="input_text <?= $item['class']; ?>"
			name="data[<?= $item['name']; ?>]"
			<?= (!empty($item['multiple'])) ? 'multiple' : ''; ?>
			<?= (!empty($item['size'])) ? 'size="' . $item['size'] . '"' : ''; ?>
		>
			<?php foreach ($item['options'] as $k => $i) : ?>
			<option value="<?= $k; ?>"<?php if ($k == $module -> data[$item['name']]) : ?> selected<?php endif; ?>><?= $i; ?></option>
			<?php endforeach; ?>
		</select>
		<?php endif; ?>
		
		<div class="help-block"><?php
			unset($module -> var['errors']['fail']);
			if (
				!empty($item['name']) &&
				!empty($module -> var['errors']) &&
				is_array($module -> var['errors']) &&
				in_array($item['name'], $module -> var['errors'])
			) {
				global $lang;
				if (objectObject($lang -> errors, $item['name'])) {
					echo objectObject($lang -> errors, $item['name']);
				} else {
					if ($item['type'] !== 'select') {
						echo 'Введенный текст слишком короткий или содержит недопустимые символы';
					} else {
						echo 'Не выбрано допустимое значение';
					}
				}
			}
		?></div>
	</label>
</div>

<?php else : ?>

<div class="form-group">
	<div class="field-text__title field-text__title_notreq"><?= $item['default']; ?></div>
	<div class="checkbox-container">
		<?php foreach ($item['options'] as $k => $i) : ?>
		<label class="control control--checkbox">
			<?= $i; ?>
			<input
				type="checkbox"
				name="data[<?= $item['name']; ?>][<?= $k; ?>]"
				value="<?= $k; ?>"
				<?php if (!empty($module -> data[$item['name']][$k])) : ?>checked<?php endif; ?>
			>
			<div class="control__indicator"></div>
		</label>
		<?php endforeach; ?>
	</div>
</div>

<?php endif; ?>