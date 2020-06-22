<?php defined('isCMS') or die;

/*
* ISCMS File Manager
* based on
* H3K | Tiny File Manager V2.4.3
* CCP Programmers | ccpprogrammers@gmail.com
* https://tinyfilemanager.github.io
*/

/* *** BASED SETTINGS *** */

//TFM version
define('VERSION', '2.4.3');
//Application Title
define('APP_TITLE', 'Tiny File Manager');
// max upload file size
define('MAX_UPLOAD_SIZE', 2048);
// private key and session name to store to the session
define('FM_SESSION_ID', 'filemanager');

// Show or hide files and folders that starts with a dot
define('FM_SHOW_HIDDEN', false);
// Enable or disable read only files and folders
define('FM_READONLY', false); // or true
// Allowed file extensions for create and rename files
// e.g. 'txt,html,css,js'
define('FM_FILE_EXTENSION', '');
// Allowed file extensions for upload files
// e.g. 'gif,png,jpg,html,txt'
define('FM_UPLOAD_EXTENSION', '');
// Files and folders to excluded from listing
// e.g. array('myfile.html', 'personal-folder', '*.php', ...)
define('FM_EXCLUDE_ITEMS', []);
// Online office Docs Viewer
// Availabe rules are 'google', 'microsoft' or false
// google => View documents using Google Docs Viewer
// microsoft => View documents using Microsoft Web Apps Viewer
// false => disable online doc viewer
define('FM_DOC_VIEWER', 'google');
// Enable ace.js (https://ace.c9.io/) on view's page
define('FM_EDIT_FILE', true);
// Enable highlight.js (https://highlightjs.org/) on view's page
define('FM_USE_HIGHLIGHTJS', true);
// highlight.js style
// for dark theme use 'ir-black'
define('FM_HIGHLIGHTJS_STYLE', 'vs');

// Hide Permissions and Owner cols in file-listing
$hide_Cols = true; //false
// Show directory size: true or speedup output: false
$calc_folder = true; //false
define('FM_LANG', $lang -> lang); // Default language
define('FM_ICONV_INPUT_ENC', 'CP1251'); //UTF-8
define('FM_DATETIME_FORMAT', 'Y.m.d H:i'); //d.m.y H:i

/* *** REQUEST SETTINGS *** */

// for ajax request - save
$input = file_get_contents('php://input');
$_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;
unset($input);

// get and post data in one request array
$request = ['get' => [], 'post' => []];
if (objectIs($_GET['data'])) { foreach ($_GET['data'] as $k => $i) { $request['get'][$k] = $i; } }
if (objectIs($_POST['data'])) { foreach ($_POST['data'] as $k => $i) { $request['post'][$k] = $i; } }

/* *** PATH SETTINGS *** */

// Root url for links in file manager. Relative to http_host. Variants: '', 'path/to/subfolder'
// Will not working if $root_path will be outside of server document root
// сюда еще нужно сделать так, чтобы он верно читал текстовые переменные, например {assets} укажет на PATH_ASSETS и URL_ASSETS
if (
	empty($_SESSION[FM_SESSION_ID]['fm_root_url']) ||
	!(defined('isPROCESS') && isPROCESS)
) {
	
	if (!empty($sets['path']) && mb_strpos($sets['path'], '{') !== false) {
		$sets['path'] = preg_replace_callback(
			'/\{(\w+)?\}/ui',
			function ($matches) {
				return constant('URL_' . strtoupper($matches[1]));
			},
			$sets['path']
		);
		$sets['path'] = rtrim($sets['path'], '\\/');
	}
	
	$root_url = !empty($sets['path']) ? fm_clean_path(str_replace(':', DS, $sets['path'])) : null;
	$_SESSION[FM_SESSION_ID]['fm_root_url'] = $root_url;
	
} else {
	$root_url = $_SESSION[FM_SESSION_ID]['fm_root_url'];
}

// Root path for file manager
// use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
$root_path = PATH_SITE . $root_url;

// clean and check $root_path
$root_path = rtrim($root_path, '\\/');
$root_path = str_replace('\\', '/', $root_path);
if (!@is_dir($root_path)) {
    echo "<h1>Root path \"{$root_path}\" not found!</h1>";
    exit;
}

// get and clean path
$p = !empty($request['get']['p']) ? $request['get']['p'] : (!empty($request['post']['p']) ? $request['post']['p'] : null);

// actions path
$action = objectProcess('filemanager:actions');

// instead globals vars

define('FM_IS_WIN', DIRECTORY_SEPARATOR == '\\');

define('FM_PATH', !empty($p) ? fm_clean_path($p) : null);
define('FM_ROOT_URL', $uri -> site . set($root_url, true) . '/');
define('FM_ROOT_PATH', $root_path);
define('FM_SELF_URL', $uri -> site . (defined('isPROCESS') && isPROCESS ? $uri -> previous : $uri -> path -> string) . '?data[p]=');
define('FM_SELF_PATH', FM_SELF_URL . urlencode(FM_PATH));
define('FM_ACTION_PATH', $action['action'] . $action['string'] . '&data[p]=');
define('FM_ACTION_URL', $action['link'] . $action['string'] . '&data[p]=' . urlencode(FM_PATH));

unset($p, $root_path, $root_url);

/*
echo 'ROOT : ' . $_SERVER['DOCUMENT_ROOT'] . '<br>';
echo 'SITE : ' . PATH_SITE . '<br>';
echo 'BASE : ' . PATH_BASE . '<br>';
echo 'FM_ROOT_PATH : ' . FM_ROOT_PATH . '<br>'; // = PATH_SITE
echo 'FM_ROOT_URL : ' . FM_ROOT_URL . '<br>';
echo 'FM_SELF_URL : ' . FM_SELF_URL . '<br>';
echo 'FM_PATH : ' . FM_PATH . '<br>';
echo 'FM_SELF_PATH : ' . FM_SELF_PATH . '<br>';
echo 'FM_ACTION_PATH : ' . FM_ACTION_PATH . '<br>';
echo 'FM_ACTION_URL : ' . FM_ACTION_URL . '<br>';
*/

?>