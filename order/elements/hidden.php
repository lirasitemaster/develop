<?php defined('isCMS') or die; ?>

<input type="hidden" name="query" value="order">
<input type="hidden" name="name" value="<?= $module -> param; ?>">
<input type="hidden" name="key" value="<?= $module -> settings -> date -> key; ?>">
<input type="text" name="check" value="" style="display:none!important;">

<?php if ($module -> settings -> date -> sort) : ?>
	<input type="hidden" name="sort" value="<?= '[' . $module -> settings -> date -> sort . ']'; ?>">
<?php endif; ?>
