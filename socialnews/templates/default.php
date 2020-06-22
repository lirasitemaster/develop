<?php defined('isCMS') or die; ?>

<div id="news">

	<?php foreach ($module -> data as $item) : ?>
		
		<div>
			<p>NEW!</p>
			<p><?= $item -> date; ?></p>
			<p><?= $item -> text; ?></p>
			
			<?php foreach ($item -> images as $image) : ?>
				<img src="<?= $image; ?>">
			<?php endforeach; ?>
			
			<?php if (isset($item -> video)) : ?>
				<a href="<?= $item -> video; ?>" target="_blank">смотреть видео</a>
			<?php endif; ?>
			
		</div>
		
	<?php endforeach; ?>

</div>