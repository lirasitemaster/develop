<?php defined('isENGINE') or die; ?>

<div class="media-<?= $name . set($id, ' id-' . $id); ?>">
	<?php
		require $module -> elements . 'mainslider.php';
		require $module -> elements . 'controlblock.php';
		require $module -> elements . 'infoblock.php';
		require $module -> elements . 'slider.php';
		require $module -> elements . 'content.php';
	?>
</div>

<script type="text/javascript">
	$(function(){
		<?php
			$id = set($id, '.id-' . $id . ' ');
			require $module -> elements . 'script_slider.php';
			require $module -> elements . 'script_gallery.php';
		?>
	});
</script>

<?php require $module -> elements . 'styles.php'; ?>
