<?php defined('isCMS') or die;

if (!empty($module -> settings['wrapper']['tag'])) {
	$wrapper = new htmlElement(
		$module -> settings['wrapper']['tag'],
		set($module -> settings['wrapper']['class'], true),
		set($module -> settings['wrapper']['id'], true),
		set($module -> settings['wrapper']['data'], true),
		set($module -> settings['wrapper']['area'], true)
	);
}

if (!empty($module -> this)) :
	echo $print;
else :

?>

<ul class="footer-social__block">
	
	<?php
		foreach ($data as $key => $item) :
		if (!empty($item)) :
	?>
	
	<li class="<?= $item['class']; ?>">
		<a href="/<?= $item['filter']; ?>" target="blank">
			<i class="<?= $item['icon']; ?>" aria-hidden="true"></i>
			<?= $item['title']; ?>
		</a>
	</li> 
	
	<?php
		endif;
		endforeach;
		unset($key, $item);
	?>
	
</ul>

<?php

endif;

if (isset($wrapper)) {
	$wrapper -> close();
	unset($wrapper);
}

?>