<?php defined('isENGINE') or die; ?>
<div class="<?= $defaults['classwrapper'] . ' ' . $defaults['classwrapperbefore']; ?>">
<a
	href="<?= $url -> site; ?>"
	class="
		<?= $module -> param; ?>_brand
		<?= ($module -> settings['bootstrap']) ? 'navbar-brand' : ''; ?>
		<?= ($module -> settings['classes']['brand']) ? $module -> settings['classes']['brand'] : ''; ?>
	"
>
	<?php if (!$module -> settings['logo']) : ?>
		
		<div class="<?= $module -> param; ?>_logo">
			<?php if (lang('icon', 'is')) : ?>
				<i class="<?= lang('icon'); ?> <?= $module -> param; ?>_icon"></i>
			<?php endif; ?>
		
	<?php else : ?>
		
		<img
			src="<?php
				echo $url -> site . URL_LOCAL . 'logo.';
				if (file_exists(PATH_LOCAL . 'logo.svg')) { echo 'svg'; }
				elseif (file_exists(PATH_LOCAL . 'logo.png')) { echo 'png'; }
				else { echo 'jpg'; }
			?>"
			alt="<?= lang('title'); ?>"
			title="<?= lang('title'); ?>"
			class="
				<?= $module -> param; ?>_logo
				<?= ($module -> settings['classes']['logo']) ? $module -> settings['classes']['logo'] : ''; ?>
			"
		>
		
	<?php endif; ?>
	
	<span class="<?= $module -> param; ?>_title">
		<?= lang('title'); ?>
	</span>
	<?php if (lang('slogan', 'is')) : ?>
	<p class="<?= $module -> param; ?>_slogan">
		<?= lang('slogan'); ?>
	</p>
	<?php endif; ?>
	
	<?php if (!$module -> settings['logo']) : ?>
		</div>
	<?php endif; ?>
</a>
</div>