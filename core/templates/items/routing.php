<?php defined('isCMS') or die;

global $lang;

//if (empty($lang) || !page('before' . '.' . $lang -> lang, 'wrapper')) {
//	page('before', 'wrapper');
//}
page((thispage('home') ? 'home_' : 'inner_') . 'before', 'wrapper');

if (
	objectGet('content', 'name') ||
	$template -> page['type'] === 'content'
) {
	
	module(['content', $template -> page['name']]);
	
//} elseif (!page(thispage('is'))) {
} elseif (
	$template -> page['type'] === 'db' &&
	objectGet('user', 'authorised')
) {
	
	module(['db', $template -> page['name']], $template -> page['name']);
	
} elseif (!page(true)) {
	
	// в этом условии как бы подразумевается вызов текущей страницы
	// а выполнение условия происходит только, если текущая страница не была найдена
	
	if (!page('nopage', 'html')) {
		page('nopage', 'item');
	}
	
}

//if (empty($lang) || !page('after' . '.' . $lang -> lang, 'wrapper')) {
//	page('after', 'wrapper');
//}
page((thispage('home') ? 'home_' : 'inner_') . 'after', 'wrapper');

?>