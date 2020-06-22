<?php defined('isENGINE') or die;

if (!empty($module -> data)) :
	foreach ($module -> data as $key => $item) :
?>

<div class="main-side__block-item main-side__block-item--currency">
	<i class="fa fa-<?= $key; ?>" aria-hidden="true"></i>
	<?= $item; ?>
</div>
<?php
	endforeach;
	unset($item, $key);
endif;
?>

<?php if (!empty($module -> settings['copyright'])) : ?>
<small>
	По данным сайта <a href="http://cbr.ru/" title="Центральный Банк России" target="_blank">Центробанка РФ</a>
</small>
<?php endif; ?>