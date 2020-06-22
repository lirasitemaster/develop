<?php defined('isENGINE') or die;

$sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n\n";

foreach ($module -> data as $key => $item) {
		
	$clear = moduleMenuClear((strpos($key, ':') !== false) ? substr($key, strrpos($key, ':') + 1) : $key);
	$clear = $clear['clear'];
	if (
		(
			is_array($item) &&
			isset($module -> settings -> nosubmenu) &&
			$module -> settings -> nosubmenu === true
		) || (
			is_array($item) &&
			isset($module -> settings -> nosubmenu) &&
			is_array($module -> settings -> nosubmenu) &&
			in_array($clear, $module -> settings -> nosubmenu)
		)
	) {
		$key = (strpos($key, ':') !== false) ? substr($key, 0, strrpos($key, ':')) . ':00' . rand() : $key;
		$item = $clear;
	}
	
	if (is_array($item)) {
		
		// пункты раскрывающие подменю
		// НОВЫЙ РАЗБОР СТРОКИ ССЫЛКИ
		$path = moduleMenuPath($key, $item, (isset($module -> settings -> onepage)) ? $module -> settings -> onepage : '', false);
		$item = $path['item'];
		if (
			$path['type'] !== 'nolink' &&
			$path['type'] !== 'group' &&
			$path['type'] !== 'none' &&
			$path['type'] !== 'hash' &&
			$path['type'] !== 'action' &&
			$path['type'] !== 'url'
		) {
			$sitemap .= "  <url>\n    <loc>" . $path['link'] . "</loc>\n    <changefreq>daily</changefreq>\n    <priority>1.0</priority>\n  </url>\n\n";
		}
		unset($path);
		
		foreach ($item as $subkey => $subitem) {
			
			// вложенные пункты меню
			// НОВЫЙ РАЗБОР СТРОКИ ССЫЛКИ
			$path = moduleMenuPath($subkey, $subitem, (isset($module -> settings -> onepage)) ? $module -> settings -> onepage : '', false);
			$subitem = $path['item'];
			if (
				$path['type'] !== 'nolink' &&
				$path['type'] !== 'group' &&
				$path['type'] !== 'none' &&
				$path['type'] !== 'hash' &&
				$path['type'] !== 'action' &&
				$path['type'] !== 'url'
			) {
				$sitemap .= "  <url>\n    <loc>" . $path['link'] . "</loc>\n    <changefreq>monthly</changefreq>\n    <priority>0.5</priority>\n  </url>\n\n";
			}
			unset($path);
			
		}
		
	} else {
		
		// верхние пункты меню
		// НОВЫЙ РАЗБОР СТРОКИ ССЫЛКИ
		$path = moduleMenuPath($key, $item, (isset($module -> settings -> onepage)) ? $module -> settings -> onepage : '', false);
		$item = $path['item'];
		if (
			$path['type'] !== 'nolink' &&
			$path['type'] !== 'group' &&
			$path['type'] !== 'none' &&
			$path['type'] !== 'hash' &&
			$path['type'] !== 'action' &&
			$path['type'] !== 'url'
		) {
			$sitemap .= "  <url>\n    <loc>" . $path['link'] . "</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n\n";
		}
		unset($path);
		
	}
	
}

$sitemap .= "</urlset>";

file_put_contents(PATH_SITE . DS . 'sitemap.xml', $sitemap);

/*
echo '<pre>';
print_r(htmlentities($sitemap));
echo '</pre>';
*/

?>