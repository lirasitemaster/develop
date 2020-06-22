<?php defined('isCMS') or die;

if (defined('isCOOKIE') && !isCOOKIE) {
	echo lang('errors:cookie');
}

?>
<noscript><?= lang('errors:script'); ?></noscript>