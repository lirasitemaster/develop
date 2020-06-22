<?php defined('isCMS') or die; ?>

<?php if (!empty($sets['submit'])) : ?>
	<button type="submit" class="
		<?= $classes['submit']; ?>
		<?= !empty($classes['defaults']) ? 'form__submit' : null; ?>
		<?= !empty($classes['bootstrap']) ? 'btn' : null; ?>
	"><?= datalang((is_string($module -> settings['submit']) ? $module -> settings['submit'] : 'submit'), 'action'); ?></button>
<?php endif; ?>