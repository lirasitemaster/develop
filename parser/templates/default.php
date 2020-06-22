<?php defined('isCMS') or die;

echo '<section id="tripadvisor" style="margin-top: 300px; overflow: auto;"><pre>';
echo htmlentities(print_r($module -> data, true));
echo '</pre></section>';

/*
foreach ($module -> data as $key => $item) :

?>
	
	<div
		class="
			gallery__item
			gallery_<?= $module -> param; ?>__item
			<?php
				if (!empty($module -> settings -> classes) && !empty($module -> settings -> classes -> item) && is_string($module -> settings -> classes -> item)) {
					echo $module -> settings -> classes -> item;
				}
			?>
		"
		<?php if (isset($module -> settings -> random)) : ?>
		style="
				<?php if (isset($module -> settings -> random -> rotate)) : ?>
				transform: rotate(<?= rand($module -> settings -> random -> rotate[0], $module -> settings -> random -> rotate[1]); ?>deg);
				<?php endif; ?>
				
				<?php if (isset($module -> settings -> random -> color)) : ?>
				background-color: rgb(
					<?= rand($module -> settings -> random -> color[0], $module -> settings -> random -> color[1]) * 32; ?>,
					<?= rand($module -> settings -> random -> color[0], $module -> settings -> random -> color[1]) * 32; ?>,
					<?= rand($module -> settings -> random -> color[0], $module -> settings -> random -> color[1]) * 32; ?>
				);
				<?php endif; ?>
		"
		<?php endif; ?>
	>
		<?= print_r($item, true); ?>
	</div>
	
<?php endforeach; */?>