<?php defined('isENGINE') or die; ?>

<?php if (!empty($item['row']) && $item['row'] !== 'close') : ?>
<div class="<?= $item['row'] === true || $item['row'] === 'open' ? 'row' : $item['row']; ?>">
<?php endif; ?>

<div class="
	<?= $classes['group'] . (!empty($item['required']) ? ' ' . $classes['group-required'] : null); ?>
	<?= !empty($item['wrapper']) ? $item['wrapper'] : null; ?>
	<?= !empty($classes['defaults']) ? 'form__group' . (!empty($item['required']) ? ' form__group--required' : null) : null; ?>
	<?= !empty($classes['bootstrap']) ? 'form-group' : null; ?>
">
	<?php if (!empty($item['label'])) : ?>
	<label
		for="<?= 'form-' . $module -> param . '__field_' . $item['name']; ?>"
		class="
			<?= $classes['label']; ?>
			<?= !empty($classes['prefix']) ? $js['label'] . '_' . $item['name'] : null; ?>
			<?= !empty($classes['defaults']) ? 'form__label form__label_' . $item['name'] : null; ?>
		"
	>
		<?php
			
			if (!empty($elements['required']) && $elements['required'] === 'label:before') {
				echo '* ';
			}
			
			if (!empty($item['label'])) {
				if ($item['label'] === true && !empty(datalang($item['name'], 'custom'))) {
					echo datalang($item['name'], 'custom');
				} elseif ($item['label'] === true && !empty(datalang($item['name'], 'form'))) {
					echo datalang($item['name'], 'form');
				} elseif (!empty(datalang($item['label'], 'custom'))) {
					echo datalang($item['label'], 'custom');
				} elseif (datalang($item['name'], 'form')) {
					echo datalang($item['name'], 'form');
				} else {
					echo $item['label'];
				}
			}
			
			if (!empty($elements['required']) && ($elements['required'] === 'label:after' || $elements['required'] === true)) {
				echo ' *';
			}
			
		?>
	</label>
	<?php endif; ?>
	
	<?php
		
		if ($item['type'] === 'submit') {
			echo '<button ';
		} elseif ($item['type'] === 'textarea') {
			echo '<textarea ';
		} elseif ($item['type'] === 'select') {
			echo '<select ';
		} elseif (
			strpos($item['type'], 'group') !== false ||
			$item['type'] === 'info'
		) {
			echo '<div ';
		} else {
			// all another - no submit and textarea
			echo '<input ';
		}
		
		if ($item['type'] !== 'info') {
			echo 'type="' . ($item['type'] === 'captcha' ? 'text' : str_replace(':', '-', $item['type'])) . '" ';
			echo 'name="data[' . $item['name'] . ']" ';
		}
		
		if (!empty($item['label'])) {
			echo 'id="form-' . $module -> param . '__field_' . $item['name'] . '" ';
		}
		
		echo 'class="' . $class['item'] .
			(strpos($item['type'], 'group') !== false ? $class['group'] : null) .
			(!empty($classes['prefix']) ? ' ' . $js['item'] . '_' . $item['name'] : null);
		
		if (!empty($classes['defaults'])) {
			echo ' form__field form__field_' . $item['name'];
			if (!empty($item['required'])) {
				echo ' form__field--required';
			}
		}
		
		if (!empty($classes['bootstrap'])) {
			echo ' form-control';
		}
		
		if (!empty($item['class'])) {
			echo ' ' . $item['class'];
		}
		
		echo '" ';
		
		if ($item['type'] === 'submit') {
			
			echo 'value=true' ,
				'>' ,
				(!empty($item['default'])) ? datalang($item['default'], 'action') : datalang($item['name'], 'form') ,
				'</button>';
			
		} elseif ($item['type'] === 'captcha') {
			
			echo 'value="" /><div class="' . $classes['captcha-container'] . '">';
			require $module -> elements . 'captcha.php';
			echo '</div>';
			
		} elseif ($item['type'] === 'textarea') {
			
			echo 'placeholder="' ,
				(!empty($elements['required']) && $elements['required'] === 'placeholder:before' ? '* ' : null) ,
				(!empty($item['default']) ? datalang($item['default'], 'form') : null) ,
				(!empty($elements['required']) && $elements['required'] === 'placeholder:after' ? ' *' : null) ,
				'"' ,
				'>' ,
				(!empty($module -> data[$item['name']]) ? htmlentities($module -> data[$item['name']]) : set($item['default'], htmlentities($item['default']))) ,
				'</textarea>';
			
		} elseif ($item['type'] === 'select') {
			
			echo (!empty($item['multiple']) ? 'multiple ' : null) ,
				(!empty($item['size']) ? 'size="' . $item['size'] . '" ' : null) ,
				(!empty($item['required']) ? ' required' : null) ,
				'>';
			
			if (objectIs($item['options'])) {
				foreach ($item['options'] as $k => $i) {
					echo '<option value="' . $k . '"' .
						($k == $module -> data[$item['name']] ? ' selected' : null) .
						'>' . $i . '</option>';
				}
			}
			
			echo '</select>';
			
		} elseif ($item['type'] === 'list') {
			
			echo 'value="' , (!empty($module -> data[$item['name']]) ? htmlentities($module -> data[$item['name']]) : set($item['default'], htmlentities($item['default']))) , '" ' ,
				'placeholder="' , (!empty($item['default']) ? datalang($item['default'], 'form') : null) , '"' ,
				(!empty($item['required']) ? ' required' : null) ,
				(!empty($item['size']) ? ' size="' . $item['size'] . '"' : null) ,
				' list="' , $item['name'] , '" /><datalist id="' , $item['name'] , '">';
			
			if (objectIs($item['options'])) {
				foreach ($item['options'] as $i) {
					echo '<option>' . $i . '</option>';
				}
			}
			
			echo '</datalist>';
			
		} elseif (strpos($item['type'], 'group') !== false) {
			
			echo '>' . (!empty($item['default']) ? $item['default'] : null);
			
			$item['type'] = dataParse($item['type'])[1];
			if (empty($item['type']) || !empty($item['multiple'])) {
				$item['type'] = 'checkbox';
			}
			
			if (objectIs($item['options'])) {
				foreach ($item['options'] as $k => $i) {
					echo '<label class="' . $class['group-label'] . ' ' . (!empty($classes['prefix']) ? ' ' . $class['group-label'] . '_' . $item['name'] : null) . '">' . (!empty($item['default']) && $item['default'] === 'before' ? $i : null);
					
					if (set(trim($k))) {
						echo '<input class="' . $class['group-item'] . ' ' . $class['group-' . $item['type']] . (!empty($classes['prefix']) ? ' ' . $class['group-item'] . '_' . $item['name'] : null) . '" type="' . $item['type'] . '" ' .
						'name="data[' . $item['name'] . ']' . ($item['type'] !== 'radio' ? '[' . $k . ']' : null) . '" ' .
						'value="' . $k . '"' .
						($item['type'] !== 'radio' && !empty($module -> data[$item['name']][$k]) || $item['type'] === 'radio' && $k == $module -> data[$item['name']] ? ' checked' : null) .
						' />';
					}
					
					echo (empty($item['default']) || $item['default'] !== 'before' ? $i : null) . '</label>';
				}
			}
			
			echo '</div>';
			
		} elseif ($item['type'] === 'info') {
			
			echo '>' . (!empty($item['default']) ? $item['default'] : null) . '</div>';
			
		} else {
			
			// all another - no submit and textarea
			
			echo 'value="' , (!empty($module -> data[$item['name']]) ? htmlentities($module -> data[$item['name']]) : null) , '" ' ,
				'placeholder="' ,
				(!empty($elements['required']) && $elements['required'] === 'placeholder:before' ? '* ' : null) ,
				(!empty($item['default']) ? datalang($item['default'], 'form') : null) ,
				(!empty($elements['required']) && $elements['required'] === 'placeholder:after' ? ' *' : null) ,
				'"' ,
				(!empty($item['required']) ? ' required' : null) ,
				'/>';
			
		}
		
	?>
	
	<?php if (!empty($item['description'])) : ?>
		<div class="
			<?= $classes['description']; ?>
			<?= !empty($classes['prefix']) ? $js['description'] . '_' . $item['name'] : null; ?>
			<?= !empty($classes['defaults']) ? 'form__description form__description_' . $item['name'] : null; ?>
			<?= (!empty($classes['bootstrap'])) ? 'form-text text-muted' : ''; ?>
		">
			<?= datalang($item['description'], 'custom'); ?>
		</div>
	<?php endif; ?>
	<?php if ($item['type'] !== 'info' && !empty($elements['errors']) && $elements['errors'] !== 'after' && $elements['errors'] !== 'before') : ?>
		<div class="
			<?= $classes['error']; ?>
			<?= !empty($classes['prefix']) ? $js['error'] . '_' . $item['name'] : null; ?>
			<?= !empty($classes['defaults']) ? 'form__error form__error_' . $item['name'] : null; ?>
		"><?php
			unset($module -> var['errors']['fail']);
			if (
				!empty($item['name']) &&
				objectIs($module -> var['errors']) &&
				in_array($item['name'], $module -> var['errors'])
			) {
				if (!empty($labels['errors'][$item['name']])) {
					echo $labels['errors'][$item['name']];
				} elseif (!empty(lang('errors:' . $item['name']))) {
					echo lang('errors:' . $item['name']);
				} elseif (
					$item['type'] === 'select' ||
					$item['type'] === 'list' ||
					strpos($item['type'], 'group') !== false
				) {
					echo $labels['error_select'];
				} else {
					echo $labels['error_field'];
				}
			}
		?></div>
	<?php endif; ?>
	
</div>

<?php if (!empty($item['row']) && $item['row'] !== 'open') : ?>
</div>
<?php endif; ?>