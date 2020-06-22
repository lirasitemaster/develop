<?php defined('isCMS') or die;

//print_r($classes);
//print_r($js);

if ($module -> status !== 'complete') : 
	
	$classes = $module -> settings['classes'];
	$js = $module -> settings['js'];
	foreach (['classes', 'js'] as $i) {
		foreach ($$i as &$ii) {
			$ii = preg_replace(['/\{param\}/ui', '/\{name\}/ui'], [$module -> param, $item['name']], $ii);
		}
		unset($ii);
	}
	unset($i);
	
?>

<form
	id="<?= $module -> param; ?>"
	class="
		<?= $classes['common']; ?>
		<?= !empty($classes['defaults']) ? ' form form_' . $module -> param : null; ?>
	"
	<?php require $module -> elements . 'attributes.php'; ?>
>

<?php
	
	require $module -> elements . 'hidden.php';
	
	if (!empty($elements['errors']) && $elements['errors'] === 'before') {
		require $module -> elements . 'errors.php';
	}
	
	if (!empty($module -> this)) {
		
		if (!empty($elements['this'])) {
			echo '<div class="' . $classes['this'] . '">';
		}
		
		if (objectIs($module -> this)) {
			echo '<input type="hidden" name="source[this]" value="' . base64_encode(iniPrepareArray($module -> this)) . '" />';
		} else {
			page($module -> this, 'html');
		}
		
		if (!empty($elements['this'])) {
			echo '</div>';
		}
		
	}
	
	foreach ($sets['form'] as $item) {
		
		$classes = $module -> settings['classes'];
		$js = $module -> settings['js'];
		foreach (['classes', 'js'] as $i) {
			foreach ($$i as &$ii) {
				$ii = preg_replace(['/\{param\}/ui', '/\{name\}/ui'], [$module -> param, $item['name']], $ii);
			}
			unset($ii);
		}
		unset($i);
		
		require $module -> elements . 'field.php';
		
	}
	
	if (!empty($elements['errors']) && $elements['errors'] === 'after') {
		require $module -> elements . 'errors.php';
	}
	
	require $module -> elements . 'submit.php';
	
?>

</form>

<?php

else :
	
	require $module -> elements . 'complete.php';
	
endif;

?>

<?php //print_r($module -> data); ?>