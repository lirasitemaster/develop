<?php defined('isCMS') or die;
if (in('libraries', 'jquery')) :
?>
<script>
jQuery(document).ready(function($){
	$('body script').each(function(){
		$(this).appendTo($('body'));
	});
});
</script>
<?php endif; ?>