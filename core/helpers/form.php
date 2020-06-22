<!-- отправка этой формы дает HTTP_REFERER -->
<div<?= set(cookie('UID', true), ' style="opacity: 0.5;"'); ?>>
	<hr>POST
	<form method="post" action="<?= $url; ?>">
	<input type="text" name="name" value="admin">
	<input type="text" name="password" value="123456">
	<input type="submit">
	</form>
	<form method="post" action="<?= $url; ?>">
	<input type="submit" name="exit" value="exit">
	</form>
	<?php
		/*
		$p = '123456';
		$h = password_hash($p, PASSWORD_DEFAULT);
		$v = password_verify($p, $h);
		echo $p . ' :: ' . $h . ' [' . $v . ']';
		*/
	?>
</div>