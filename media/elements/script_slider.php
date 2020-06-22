<?php defined('isCMS') or die;

$p = null;

if (
	!empty($sets['classes']['preload']) ||
	!empty($sets['classes']['loaded'])
) {
	$p .= '.on(\'init\', function(){ var s = $(this).parents(\'.media-' . $name . $id . '\'); s';
	if (!empty($sets['classes']['preload'])) {
		$p .= '.removeClass(\'' . $sets['classes']['preload'] . '\')';
	}
	if (!empty($sets['classes']['loaded'])) {
		$p .= '.addClass(\'' . $sets['classes']['loaded'] . '\')';
	}
	$p .= '.find(\'.slick-current\').each(function(i){
		t = $(this);
		t.removeClass(\'slick-current\');
		window.setTimeout(function(){
			t.addClass(\'slick-current\');
		}, 0);
	}); })';
}

if (
	!empty($sets['classes']['landscape']) ||
	!empty($sets['classes']['square']) ||
	!empty($sets['classes']['portrait'])
) {
	$p .= '.on(\'setPosition\', function(){
	var sl = $(this).parents(\'.media-' . $name . $id . '\');
	var w = $(window).width() / $(window).height();
	if ($(window).height() > 0 && $(window).width() > 0) {
		if (w >= 1.2) {
			sl' .
			set($sets['classes']['landscape'], '.addClass(\'' . $sets['classes']['landscape'] . '\')') .
			set($sets['classes']['square'], '.removeClass(\'' . $sets['classes']['square'] . '\')') .
			set($sets['classes']['portrait'], '.removeClass(\'' . $sets['classes']['portrait'] . '\')') .
			';
		} else if (w < 1.2 && w > 0.83) {
			sl' .
			set($sets['classes']['square'], '.addClass(\'' . $sets['classes']['square'] . '\')') .
			set($sets['classes']['landscape'], '.removeClass(\'' . $sets['classes']['landscape'] . '\')') .
			set($sets['classes']['portrait'], '.removeClass(\'' . $sets['classes']['portrait'] . '\')') .
			';
		} else {
			sl' .
			set($sets['classes']['portrait'], '.addClass(\'' . $sets['classes']['portrait'] . '\')') .
			set($sets['classes']['landscape'], '.removeClass(\'' . $sets['classes']['landscape'] . '\')') .
			set($sets['classes']['square'], '.removeClass(\'' . $sets['classes']['square'] . '\')') .
			';
		}
	}
})';
}

if (!empty($sets['special']) && $sets['special'] === 'lazy') {

$p .= '.on(\'lazyLoaded\', function(event, slick, image, imageSource){ var ll = $(this).parents(\'.media-' . $name . $id . '\'); ll.find(\'.slick-cloned [data-lazy=\"\'+imageSource+\'\"]\').each(function(i){
	$(this).removeAttr(\'data-lazy\').attr(\'src\', imageSource);
}); })';

} elseif (!empty($sets['special']) && $sets['special'] === 'lazybg') {

$p .= '.on(\'lazyLoaded\', function(event, slick, image, imageSource){ var ll = $(this).parents(\'.media-' . $name . $id . '\'); ll.find(\'.slick-cloned [data-lazy=\"\'+imageSource+\'\"]\').each(function(i){
	$(this).removeAttr(\'data-lazy\').css(\'backgroundImage\', \'url(\'+imageSource+\')\');
}); })';

}

?>

<?php if (!empty($sets['mainslider']['enable'])) : ?>
$('<?= $id; ?>.mainslider-<?= $name; ?>')<?= empty($sets['slider']['enable']) ? $p : null; ?>.slick({
	<?= !empty($sets['slider']['start']) ? '"initialSlide" : ' . (int)$sets['slider']['start'] . ',' : null ; ?>
	"slidesToShow" : 1,
	"slidesToScroll" : 1,
	"infinite" : <?= empty($sets['slider']['loop']) ? 'false' : 'true'; ?>,
	"arrows" : <?= empty($sets['mainslider']['arrows']) ? 'false' : 'true'; ?>,
	"prevArrow" : "<?= $id; ?>.<?= $sets['classes']['mainslider']['control']['previous']; ?>",
	"nextArrow" : "<?= $id; ?>.<?= $sets['classes']['mainslider']['control']['next']; ?>",
	"fade" : true,
	"asNavFor" : "<?= $id; ?>.<?= $sets['classes']['subcontrol']; ?>"
	<?= !empty($sets['mainslider']['options']) ? ',' . substr(json_encode($sets['mainslider']['options']), 1, -1) : null; ?>
});
<?php endif; ?>

<?php if (!empty($sets['content']) && empty($sets['contentdisable'])) : ?>
<?php /*if (!empty($sets['contentmove'])) : ?>
	$('<?= $sets['contentmove']; ?>').empty();
	$('.contentslider-<?= $name; ?>').appendTo('<?= $sets['contentmove']; ?>');
<?php endif;*/ ?>
$('<?= $id; ?>.contentslider-<?= $name; ?>').slick({
	<?= !empty($sets['slider']['start']) ? '"initialSlide" : ' . (int)$sets['slider']['start'] . ',' : null ; ?>
	<?= !empty($sets['slider']['speed']) ? '"autoplay" : true, "autoplaySpeed" : ' . $sets['slider']['speed'] . ',' : '"autoplay" : false,'; ?>
	"infinite" : <?= empty($sets['slider']['loop']) ? 'false' : 'true'; ?>,
	"accessibility" : false,
	"swipe" : false,
	"arrows" : false,
	"dots" : false,
	"fade" : true
})<?php if (!empty($sets['contentmove'])) : ?>.appendTo('<?= $sets['contentmove']; ?>')<?php endif; ?>;
<?php endif; ?>

<?php if (!empty($sets['slider']['enable'])) : ?>
$('<?= $id; ?>.slider-<?= $name; ?>')<?= $p; ?>.slick({
	<?= !empty($sets['content']) && empty($sets['contentdisable']) || !empty($sets['mainslider']['enable']) ? '"asNavFor" : "' . $id . '.' . $sets['classes']['subcontrol'] . '", "focusOnSelect" : true,' : null; ?>
	<?= !empty($sets['slider']['start']) ? '"initialSlide" : ' . (int)$sets['slider']['start'] . ',' : null ; ?>
	<?= !empty($sets['slider']['speed']) ? '"autoplay" : true, "autoplaySpeed" : ' . $sets['slider']['speed'] . ',' : '"autoplay" : false,'; ?>
	"infinite" : <?= empty($sets['slider']['loop']) ? 'false' : 'true'; ?>,
	"arrows" : <?= empty($sets['slider']['arrows']) ? 'false' : 'true'; ?>,
	"dots" : <?= empty($sets['slider']['dots']) ? 'false' : 'true'; ?>,
	<?= !empty($sets['classes']['dots'] && !is_array($sets['classes']['dots'])) ? '"dotsClass" : "' . $sets['classes']['dots'] . '",' : null; ?>
	<?php
		if (set($sets['labels']['dots'])) {
			$s = null;
			if (objectIs($sets['labels']['dots'])) {
				$s = '$(\'<span />\').text(' . json_encode($sets['labels']['dots']) . '[i])';
			} elseif (is_string($sets['labels']['dots'])) {
				$s = $sets['labels']['dots'];
			} else {
				$s = '$(\'<span />\').text(i + 1)';
			}
			if (!empty($sets['classes']['dots'])) {
				$s .= '.addClass(' . (objectIs($sets['labels']['dots']) ? json_encode($sets['classes']['dots']) . '[i]' : '\'' . $sets['classes']['dots'] . '\'') . ')';
			}
			if (set($sets['slider']['start']) && !empty($sets['classes']['slider']['control']['start'])) {
				$s .= '.addClass(function(n, c) {
					if (i == ' . $sets['slider']['start'] . ') {
						return \'' . $sets['classes']['slider']['control']['start'] . '\';
					}
				})';
			}
			echo '"customPaging" : function(slider, i) { return ' . $s . '; },';
			unset($s);
		}
	?>
	"prevArrow" : "<?= $id; ?>.<?= $sets['classes']['slider']['control']['previous']; ?>",
	"nextArrow" : "<?= $id; ?>.<?= $sets['classes']['slider']['control']['next']; ?>",
	"appendDots" : "<?= $id; ?>.<?= $sets['classes']['slider']['control']['dots']; ?>"
	<?= !empty($sets['slider']['options']) ? ',' . substr(json_encode($sets['slider']['options']), 1, -1) : null; ?>
});
// more on http://kenwheeler.github.io/slick/
<?php endif; ?>
<?php unset($p); ?>