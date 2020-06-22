<?php defined('isENGINE') or die;
$form = objectProcess('content:form', null, str_replace('.', ':', $content -> parent));
?>

<form class="articles_rating" method="post" action="<?= $form['action']; ?>">
	
	<?php
		foreach ($form['fields'] as $fi) {
			echo $fi;
		}
		unset($fi, $form);
		
		//print_r($content -> settings['create']);
	?>
	
	<?php
		$ff = null;
		foreach ($content -> settings['create'] as $fk => $fi) {
			
			$fi = dataParse($fi);
			
			$ff .= '<';
			
			if (!empty($fi[1]) && $fi[1] === 'textarea') {
				$ff .= 'textarea';
			} else {
				$ff .= 'input';
			}
			
			$ff .= ' name="data[' . $fk . ']"' . set($fi[1], ' type="' . $fi[1] . '"') . set($fi[2], 'norequired') . '>';
			
			if (!empty($fi[1]) && $fi[1] === 'textarea') {
				$ff .= '</textarea>';
			}
		}
		echo $ff;
		unset($fi, $fk, $ff);
	?>
	
	<?php if (!empty($filter -> options['reset'])) : ?>
		<button type="reset">
			<?= lang('action:cancel'); ?>
		</button>
	<?php endif; ?>
	
	<button type="submit">
		<?= lang('action:send'); ?>
	</button>
	
</form>