<?php defined('isCMS') or die;

foreach ($module -> var['base']['fields'] as $fi) {
	echo $fi;
}
unset($fi);

?>
<input type="hidden" name="source[module]" value="<?= $module -> param; ?>" readonly>
<?php if (!empty($sets['captcha']['enable'])) : ?>
<input type="hidden" name="source[hash]" value="<?= $module -> var['token']; ?>" readonly>
<?php endif; ?>
