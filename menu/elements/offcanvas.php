<?php defined('isCMS') or die;

if (!in('libraries', 'jquery')) {
	logging('module \'menu\' was not opening - not find needed library \'jquery\' by \'system\'');
}
if (!in('libraries', 'mobilemenu:system')) {
	logging('module \'menu\' was not opening - not find needed library \'mobile-menu\' by \'system\'');
}

$defaults = [
	"classcommon"        => "mobile-menu",
	"classmenu"          => "mm_offcanvas",
	"classmenuactive"    => "mm--expand",
	"classmenuhidden"    => "mm--collapse",
	"classtoggle"        => "mm_toggle",
	"classtoggleicon"    => "mm_toggle--icon",
	"classclose"         => "mm_close",
	"classcloseicon"     => "mm_close--icon",
	"classoverlay"       => "mm_overlay",
	"classoverlayactive" => "mm_overlay--visible",
	"classbody"          => "mm_body--visible",
	"classbutton"        => "mm_button",
	"classbuttonhide"    => "mm_button__minus",
	"classbuttonshow"    => "mm_button__plus",
	"classbuttonopen"    => "mm_button--open",
	"classlinkactive"    => "active",
	"classwrapper"       => "mm_wrapper",
	"classwrapperbefore" => "mm_wrapper--before",
	"classwrapperafter"  => "mm_wrapper--after"
];

if (!empty($module -> settings['offcanvas']['classes'])) {
	foreach ($module -> settings['offcanvas']['classes'] as $k => $i) {
		$defaults['class' . $k] = $i;
	}
	unset($k, $i);
}

$addmodule = $module;

unset(
	$addmodule -> settings['separator'],
	$addmodule -> settings['elements'],
	$addmodule -> settings['classes']
);

$addmodule -> settings['classes'] = [
	'ul' => $defaults['classsubmenu'],
	'li' => $defaults['classitem'],
	'link' => $defaults['classlink']
];

?>

<div class="<?= $defaults['classtoggle']; ?>">
	<span class="<?= $defaults['classtoggleicon']; ?>"></span>
	<?= $module -> settings['offcanvas']['labels']['toggle']; ?>
</div>

<div class="<?= $defaults['classcommon']; ?>">
	<div class="<?= $defaults['classclose']; ?>">
		<span class="<?= $defaults['classcloseicon']; ?>"></span>
		<?= $module -> settings['offcanvas']['labels']['close']; ?>
	</div>
	<?php
		if (!empty($module -> settings['offcanvas']['wrappers'])) {
			if (file_exists(PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> param . '_wrapper_before.php')) {
				require PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> param . '_wrapper_before.php';
			} else {
				require $module -> elements . 'wrapper_before.php';
			}
		}
	?>
	<ul class="<?= $defaults['classmenu']; ?>">
		<?php
			funcModuleMenu_Create($data, $addmodule);
			unset($addmodule);
		?>
	</ul>
	<?php
		if (!empty($module -> settings['offcanvas']['wrappers'])) {
			if (file_exists(PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> param . '_wrapper_after.php')) {
				require PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> param . '_wrapper_after.php';
			} else {
				require $module -> elements . 'wrapper_after.php';
			}
		}
	?>
</div> 

<script>
jQuery(document).ready(function($){
	$('.<?= $defaults['classcommon']; ?>').mobileMenu(<?= json_encode(!empty($module -> settings['offcanvas']['options']) ? array_merge($module -> settings['offcanvas']['options'], $defaults) : $defaults); ?>);
});
</script>

<?php

unset($defaults);

/*
<style>
.mm_overlay--visible {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.5);
}
.mobile-menu {
	left: 0px;
    width: 250px;
    overflow: overlay;
    top: 0px;
    position: fixed;
    bottom: 0px;
    background: rgb(204, 204, 204);
}
.mm_offcanvas, .mm_offcanvas ul {
	list-style: none;
	margin-block-start: 0;
    margin-block-end: 0;
    margin-inline-start: 0;
    margin-inline-end: 0;
    padding-inline-start: 0;
}
.mm_offcanvas li {
	padding: 2px 0px 2px 20px;
	border-bottom: 1px dotted black;
}
.mm_offcanvas li:first-child {
	border-top: 1px dotted black;
}
.mm_offcanvas li .mm_button {
	width: 20px;
	height: 20px;
	float: right;
	margin-right: 20px;
}
.mm_offcanvas li .mm_button::before {
	width: 20px;
	height: 20px;
	display: block;
	background: black;
	color: white;
}
.mm_offcanvas li .mm_button__plus::before {
	content: '+';
}
.mm_offcanvas li .mm_button__minus::before {
	content: '-';
}
</style>
*/

?>