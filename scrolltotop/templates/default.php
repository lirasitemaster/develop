<?php defined('isENGINE') or die; ?>

<!-- Scroll to Top Button-->
<?= '<' . $module -> settings -> element . ' class="' . $module -> var['classes'] . '"' . ($module -> settings -> element === 'a' ? ' href="#"' : '') . (!empty($module -> settings -> id) ? ' id="' . $module -> settings -> id . '"' : '') . '>'; ?>
	<?php if (!empty($module -> var['icon'])) : ?>
	<i class="<?= $module -> var['icon']; ?>"></i>
	<?php endif; ?>
	<?php
		if (!empty($module -> settings -> text)) {
			echo '<span' . (!empty($module -> var['text']) ? ' class="' . $module -> var['text'] . '"' : '') . '>';
			if (!empty($module -> settings -> lang)) {
				dataprint('{lang:' . $module -> settings -> lang . '}');
			} else {
				echo $module -> settings -> text;
			}
			echo '</span>';
		}
	?>
<?= '</' . $module -> settings -> element . '>'; ?>

<script>
	
	$(document).on('scroll', function() {
	var scrollDistance = $(this).scrollTop();
	if (scrollDistance > <?= $module -> settings -> distance; ?>) {
		$('<?= $module -> settings -> element . '.' . $module -> var['base']; ?>').fadeIn();
	} else {
		$('<?= $module -> settings -> element . '.' . $module -> var['base']; ?>').fadeOut();
	}
	});
	
	$(document).on('click', '<?= $module -> settings -> element . '.' . $module -> var['base']; ?>', function(e) {
		$('html, body').animate({scrollTop: 0}, <?= $module -> settings -> speed; ?>);
		e.preventDefault();
	});
	
</script>