<?php defined('isENGINE') or die; ?>

<?php if (!empty($module -> var['errors'])) : ?>
	<div class="
		<?= $classes['error']; ?>
		<?= !empty($classes['defaults']) ? 'form__error' : null; ?>
	">
		
		<div class="
			<?= !empty($classes['prefix']) ? $js['error'] . '_fail' : null; ?>
			<?= !empty($classes['defaults']) ? 'form__error_fail' : null; ?>
		"><?php
				if (
					!empty($module -> var['errors']['fail']) &&
					!empty(lang('errors:fail'))
				) {
					echo lang('errors:fail');
				} elseif (!empty($module -> var['errors']['fail'])) {
					echo $module -> var['errors']['fail'];
				}
		?></div>
		
		<?php
			unset($module -> var['errors']['fail']);
			foreach ($module -> var['errors'] as $k => $i) :
		?>
			<div class="
				<?= !empty($classes['prefix']) ? $js['error'] . '_' . $i : null; ?>
				<?= !empty($classes['defaults']) ? 'form__error_' . $i : null; ?>
			"><?php
				if (!empty(lang('errors:' . $k))) {
					echo lang('errors:' . $k);
				} elseif (!empty(lang('errors:' . $i))) {
					echo lang('errors:' . $i);
				} else {
					echo $k . ':' . $i;
				}
			?>
			</div>
		<?php endforeach; ?>
		
	</div>
<?php endif; ?>