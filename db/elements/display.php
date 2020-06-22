<?php defined('isCMS') or die;
if (!empty($sets['display'])) :
?>

<ul class="<?= $class['display'][0]; ?>">
	<?php
		$i = !empty($buttons['editintable']) && $buttons['editintable'] !== 'after' ? 1 : 0;
		foreach ($base as $item) :
	?>
	<li class="<?= $class['display'][1]; ?>" data-column="<?= $i; ?>"><?= $item; ?></li>
	<?php
		$i++;
		endforeach;
		unset($item);
		if (!empty($module -> settings['data']) && objectIs($map)) :
			foreach ($map as $item) :
	?>
	<li class="<?= $class['display'][1]; ?>" data-column="<?= $i; ?>">data <?= $item; ?></li>
	<?php
			$i++;
			endforeach;
			unset($item);
		elseif (!empty($module -> settings['data'])) :
	?>
		<li class="<?= $class['display'][1]; ?>" data-column="<?= $i; ?>">data</li>
	<?php
		endif;
		unset($i);
	?>
</ul>

<?php endif; ?>