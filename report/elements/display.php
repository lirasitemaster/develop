<?php defined('isCMS') or die;
if (!empty($sets['display'])) :
?>

<ul class="<?= $classes['display'][0]; ?>">
	<?php
		$key = 1;
		foreach ($head as $item) :
	?>
	<li class="<?= $classes['display'][1]; ?>" data-column="<?= $key; ?>"><?= $item; ?></li>
	<?php
		$key++;
		endforeach;
		unset($key, $item);
	?>
</ul>

<?php endif; ?>