<?php defined('isCMS') or die; ?>

<script>
$(function() {
	
	var vParent = $('.articles').parent();
	var vBefore = $('.articles').prev();
	var vAfter = $('.articles').next();
	
	$('form').submit(function(e) {
		var $form = $(this);
		var $data = $form.serializeArray();
		
		$.ajax({
			type: $form.attr('method'),
			url: $form.attr('action'),
			data: $data
		}).done(function(data) {
			console.log('success');
			//console.log(data);
			vParent.children('.articles').remove();
			if (vBefore.length) {
				vBefore.after(data);
				//vParent.html(data);
			} else if (vBefore.length) {
				vAfter.before(data);
			} else {
				vParent.append(data);
			}
		}).fail(function() {
			console.log('fail');
		});
		//отмена действия по умолчанию для кнопки submit
		e.preventDefault(); 
	});
	
});
</script>