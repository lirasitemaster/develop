<?php defined('isENGINE') or die; ?>

<?php if ($module -> settings['logo'] || $module -> settings['title']) : ?>
	
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
					echo URL_LOCAL . 'logo.';
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
		
		<?php if ($module -> settings['title']) : ?>
				
				<span class="<?= $module -> param; ?>_title">
					<?= lang('title'); ?>
				</span>
				<?php if (lang('slogan', 'is')) : ?>
				<p class="<?= $module -> param; ?>_slogan">
					<?= lang('slogan'); ?>
				</p>
				<?php endif; ?>
				
		<?php endif; ?>
		
		<?php if (!$module -> settings['logo']) : ?>
			</div>
		<?php endif; ?>
		
	</a>
	
<?php endif; ?>

<?php if ($module -> settings['collapse']) : ?>
	<<?= ($module -> settings['elements']['collapselink']) ? 'a href="#"' : 'button'; ?>
		class="
			<?= $module -> param; ?>_button
			<?= ($module -> settings['bootstrap']) ? 'navbar-toggler' : ''; ?>
		"
		<?php if ($module -> settings['bootstrap']) : ?>
			type="button"
			data-toggle="collapse"
			data-target="#navbar_<?= $module -> param; ?>"
			aria-controls="navbar_<?= $module -> param; ?>"
			aria-expanded="false"
		<?php endif; ?>
	>
		<span class="<?= ($module -> settings['classes']['button']) ? $module -> settings['classes']['button'] : ''; ?>"></span>
	</<?= ($module -> settings['elements']['collapselink']) ? 'a' : 'button'; ?>>
<?php endif; ?>
