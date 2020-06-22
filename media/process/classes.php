<?php defined('isENGINE') or die;

$defc = iniPrepareJson('{
	"slider" : {
		"common" : "' . $prefix . $class . '",
		"container" : "' . $prefix . $class . '__container",
		"item" : "' . $prefix . $class . '__item",
		"noitem" : "' . $prefix . $class . '__noitem",
		"image" : "' . $prefix . $class . '__image",
		"caption" : "' . $prefix . $class . '__caption",
		"control" : {
			"common" : "' . $prefix . 'slider__control",
			"previous" : "' . $prefix . 'slider__control--prev",
			"next" : "' . $prefix . 'slider__control--next",
			"dots" : "' . $prefix . 'slider__control--dots",
			"start" : "slick-start"
		}
	},
	"mainslider" : {
		"common" : "' . $prefix . 'mainslider",
		"container" : "' . $prefix . 'mainslider__container",
		"item" : "' . $prefix . 'mainslider__item",
		"image" : "' . $prefix . 'mainslider__image",
		"caption" : "' . $prefix . 'mainslider__caption",
		"control" : {
			"common" : "' . $prefix . 'mainslider__control",
			"previous" : "' . $prefix . 'mainslider__control--prev",
			"next" : "' . $prefix . 'mainslider__control--next",
			"dots" : "' . $prefix . 'mainslider__control--dots"
		}
	},
	"contentslider" : {
		"common" : "' . $prefix . 'contentslider",
		"container" : "' . $prefix . 'contentslider__container",
		"item" : "' . $prefix . 'contentslider__item",
		"noitem" : "' . $prefix . 'contentslider__noitem"
	},
	"mansory" : {
		"common" : "' . $prefix . 'mansory",
		"container" : "' . $prefix . 'mansory__container",
		"column" : "' . $prefix . 'mansory__column",
		"item" : "' . $prefix . 'mansory__item"
	},
	"gallery" : {
		"thumbs" : "' . $prefix . 'gallery__thumbs",
		"caption" : "' . $prefix . 'gallery__caption"
	},
	"tiles" : "' . $prefix . 'tiles",
	"subcontrol" : "' . $prefix . 'subcontrol-' . $name . '",
	"info" : "' . $prefix . 'slider__info"
}', true);

if (objectIs($sets['classes'])) {
	//$sets['classes'] = array_merge($defc, $sets['classes']);
	$sets['classes'] = objectMerge($defc, $sets['classes'], 'replace');
} else {
	$sets['classes'] = $defc;
}

unset($defc);

?>