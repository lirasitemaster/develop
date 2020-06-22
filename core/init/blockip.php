<?php defined('isENGINE') or die;

if (!file_exists(PATH_SITE . SECURE_BLOCKIP . '.ini')) {
	error('blockip', false, true);
}

$blockip = json_decode(file_get_contents(PATH_SITE . SECURE_BLOCKIP . '.ini'), true);

init('functions', 'ip');

$ip = ipReal();
$in_range = ipRange($ip, $blockip);

if (!empty($blockip) && !$in_range && SECURE_BLOCKIP === 'developlist') {
	error('update', false, true);
} elseif (
	(!empty($blockip) && $in_range && SECURE_BLOCKIP === 'blacklist') ||
	(!empty($blockip) && !$in_range && SECURE_BLOCKIP === 'whitelist')
) {
	error('403', false, 'ip in blacklist or not in whitelist');
}

?>