<?php defined('isENGINE') or die; ?>
<img
	class="<?= (!empty($sets['captcha']['class']) ? $sets['captcha']['class'] : null) . (!empty($classes['defaults']) ? ' form__captcha' : null); ?>"
	<?= (!empty($sets['captcha']['id'])) ? 'id="' . $sets['captcha']['id'] . '"' : ''; ?>
	src="<?= $module -> var['captcha']; ?>"
	alt="<?= set($sets['captcha']['alt'], true); ?>"
>