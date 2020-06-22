<?php defined('isCMS') or die;
global $seo;
?><!DOCTYPE html>
<html lang="<?= thislang('lang'); ?>"<?php if (objectIs($seo -> options) && in_array('opengraph', $seo -> options)) : ?> prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# <?= objectGet('content', 'name') ? 'article: http://ogp.me/ns/article# ' : ''; ?>ya: http://webmaster.yandex.ru/vocabularies/"<?php endif;?>>
<head>
	
	<?php
		
		if (in('options', 'inspect')) {
			page('inspectstart', 'item');
		}
		
		page('meta', 'item');
		page('webapp', 'item');
		page('icons', 'item');
		
		page('preload', 'item');
		
		page('place', 'item', false);
		page('fonts', 'item');
		
		if (in('libraries', 'scss')) {
			page('scss', 'item');
		}
		
		if (
			in('libraries', 'less') ||
			in('libraries', 'lessjs:system')
		) {
			page('less', 'item');
		}
		
		page('styles', 'item');
		
		page('variables', 'item');
		page('head', 'html');
		page(thispage('is'), 'head');
		
		page('place', 'item', false);
		
	?>
	
</head>

<?php
	
	if (
		!in('options', 'bodycustom') ||
		!page('body', 'html')
	) {
		
		if (!in('options', 'bodynoclasses')) {
			
			$class = array_diff(
				[
					thispage('is'),
					set(!objectGet('template', 'name', 'default'), objectGet('template', 'name')),
					set(thispage('special'), 'special'),
					thispage('inspecial'),
					set(objectGet('template', 'device'), 'is_' . objectGet('template', 'device')),
					set(objectGet('template', 'os'), 'is_' . objectGet('template', 'os'))
				],
				[null]
			);
			
		}
		
		echo '<body' . set($class, ' class="' . objectToString($class) . '"') . '>';
		
		page('place', 'item', false);
		
		page('header', 'html');
		
		if (in('options', 'bodyh1') && !empty($seo -> h1)) {
			echo '<h1 class="autoseo">' . $seo -> h1 . '</h1>';
		}
		
		if (in('options', 'notice')) {
			page('notice', 'item');
		}
		
	}
	
?>