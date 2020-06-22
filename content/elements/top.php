<?php defined('isCMS') or die; ?>

<div class="<?= $module -> settings['top']['classes']['common']; ?>">
<?php foreach ($module -> data['top'] as $topkey => $topitem) : ?>
	<div class="<?= $module -> settings['top']['classes']['item']; ?>">
		<div class="<?= $module -> settings['top']['classes']['inner']; ?>">
			<?= $topitem; ?>
		</div>
		<a href="<?= $topkey; ?>" class="<?= $module -> settings['top']['classes']['link']; ?>">
			<?= $topkey; ?>
		</a>
	</div>
<?php
endforeach;
unset($topkey, $topitem);
?>
</div>