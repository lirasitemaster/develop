<?php defined('isENGINE') or die; ?>

<div class="<?= $module -> settings['empty']['classes']['common']; ?>">
	<p class="<?= $module -> settings['empty']['classes']['label']; ?>">
		<?= $module -> settings['empty']['labels'][$module -> data['content']['type']]; ?>
	</p>
	<a href="<?= $path -> previous; ?>" class="<?= $module -> settings['empty']['classes']['link']; ?>">
		<?= $module -> settings['empty']['labels'][$module -> data['content']['type'] . '_link']; ?>
	</a>
</div>
