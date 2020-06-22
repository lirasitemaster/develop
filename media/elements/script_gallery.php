<?php defined('isENGINE') or die; ?>

<?php if (!empty($sets['gallery']['enable'])) : ?>
	
	<?php
		$fancybox = 'fancybox-';
		$fancybox_button = $fancybox . 'button';
		$fancybox_lang = thislang('lang');
	?>
	
	$.fancybox.defaults.smallBtn = true;
	$.fancybox.defaults.toolbar = false;
	$.fancybox.defaults.protect = true;
	
	$.fancybox.defaults.baseTpl = '' +
		'<div class="<?= $fancybox; ?>container" role="dialog" tabindex="-1">' +
		'<div class="<?= $fancybox; ?>bg"></div>' +
		'<div class="<?= $fancybox; ?>inner">' +
		//'<div class="<?= $fancybox; ?>toolbar">{{buttons}}</div>' +
		'<div class="<?= $fancybox; ?>navigation">{{arrows}}</div>' +
		'<div class="<?= $fancybox; ?>stage">' +
		'<div class="<?= $fancybox; ?>caption">' +
		'<?php if (!empty($sets['gallery']['captions'])) : ?><div class="<?= $fancybox; ?>caption--body"></div><?php endif; ?>' +
		'<?php if (!empty($sets['gallery']['counter'])) : ?><div class="<?= $fancybox; ?>caption--counter">{{COUNTER}}<span data-fancybox-index></span>{{SEPARATE}}<span data-fancybox-count></span></div><?php endif; ?>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';
	
	$.fancybox.defaults.spinnerTpl = '<div class="<?= $fancybox; ?>loading"></div>';
	$.fancybox.defaults.errorTpl = '<div class="<?= $fancybox; ?>error"><p>{{ERROR}}</p></div>';
	
	$.fancybox.defaults.btnTpl = {
		download:
		'<a download data-fancybox-download class="<?= $fancybox_button . ' ' . $fancybox_button; ?>--download" title="{{DOWNLOAD}}" href="javascript:;"><span></span></a>',
		
		zoom:
		'<a data-fancybox-zoom class="<?= $fancybox_button . ' ' . $fancybox_button; ?>--zoom" title="{{ZOOM}}"><span></span></a>',
		
		close:
		'<a data-fancybox-close class="<?= $fancybox_button . ' ' . $fancybox_button; ?>--close" title="{{CLOSE}}"><span></span></a>',
		
		// Arrows
		arrowLeft:
		'<a data-fancybox-prev class="<?= $fancybox_button . ' ' . $fancybox_button; ?>--arrow_left" title="{{PREV}}"><span></span></a>',
		
		arrowRight:
		'<a data-fancybox-next class="<?= $fancybox_button . ' ' . $fancybox_button; ?>--arrow_right" title="{{NEXT}}"><span></span></a>',
		
		// This small close button will be appended to your html/inline/ajax content by default,
		// if "smallBtn" option is not set to false
		smallBtn:
		'<a type="button" data-fancybox-close class="<?= $fancybox_button . ' ' . $fancybox; ?>close-small" title="{{CLOSE}}"><span></span></a>'
	};
	
	<?php if (!empty($sets['gallery']['captions'])) : ?>
	$.fancybox.defaults.caption = function( instance, item ) {
		return $(this).next('.<?= $sets['classes']['gallery']['caption']; ?>').html();
	};
	<?php endif; ?>
	
	<?php if (!empty($sets['gallery']['thumbs'])) : ?>
	$.fancybox.defaults.thumbs = {
		autoStart : true,
		hideOnClose : true,
		parentEl : ".fancybox-container",
		axis : "x"
	};
	<?php endif; ?>
	
	<?php if (!empty($sets['gallery']['loop'])) : ?>
	$.fancybox.defaults.loop = true;
	<?php endif; ?>
	
	<?php if ($fancybox_lang) : ?>
	$.fancybox.defaults.lang = "<?= $fancybox_lang; ?>";
	$.fancybox.defaults.i18n.<?= $fancybox_lang; ?> = {
		CLOSE: "<?= $sets['labels']['lang']['close']; ?>",
		NEXT: "<?= $sets['labels']['lang']['next']; ?>",
		PREV: "<?= $sets['labels']['lang']['prev']; ?>",
		ERROR: "<?= $sets['labels']['lang']['error']; ?>",
		PLAY_START: "<?= $sets['labels']['lang']['start']; ?>",
		PLAY_STOP: "<?= $sets['labels']['lang']['stop']; ?>",
		FULL_SCREEN: "<?= $sets['labels']['lang']['fullscreen']; ?>n",
		THUMBS: "<?= $sets['labels']['lang']['thumbs']; ?>",
		DOWNLOAD: "<?= $sets['labels']['lang']['download']; ?>",
		SHARE: "<?= $sets['labels']['lang']['share']; ?>",
		ZOOM: "<?= $sets['labels']['lang']['zoom']; ?>",
		COUNTER: "<?= $sets['labels']['lang']['counter']; ?>",
		SEPARATE: "<?= $sets['labels']['lang']['separate']; ?>"
	};
	<?php endif; ?>
	
	<?php if (!empty($sets['gallery']['options'])) : ?>
	$('[data-fancybox="gallery-<?= $name; ?>"]').fancybox(<?= json_encode($sets['gallery']['options']); ?>)<?php if ($module -> param === 'pages' && !empty($sets['slider']['enable'])) : ?>.fancybox({
		"beforeClose" : function(instance, slide) {
			var idx = $('.slider-<?= $name; ?>').find('[data-fancybox="gallery-<?= $name; ?>"][href="' + slide.src + '"]').parent().not('.slick-cloned').data('slick-index');
			var idx = (idx % 2) > 0 ? idx - 1 : idx;
			$('.slider-<?= $name; ?>').slick('slickGoTo', idx);
		}
	})<?php endif; ?>;
	<?php endif; ?>
	
// more on http://fancyapps.com/fancybox/3/
<?php endif; ?>