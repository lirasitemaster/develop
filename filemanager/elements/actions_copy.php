<?php defined('isCMS') or die; ?>

<div class="path">
	<p><b>Copying</b></p>
	<p class="break-word">
		Source path: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . $copy)) ?><br>
		Destination folder: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?>
	</p>
	<p>
		<b><a href="<?php echo FM_SELF_URL . urlencode(FM_PATH) ?>&data[copy]=<?php echo urlencode($copy) ?>&data[finish]=1"><i class="fa fa-check-circle"></i> Copy</a></b> &nbsp;
		<b><a href="<?php echo FM_SELF_URL . urlencode(FM_PATH) ?>&data[copy]=<?php echo urlencode($copy) ?>&data[finish]=1&data[move]=1"><i class="fa fa-check-circle"></i> Move</a></b> &nbsp;
		<b><a href="<?php echo FM_SELF_URL . urlencode(FM_PATH) ?>"><i class="fa fa-times-circle"></i> Cancel</a></b>
	</p>
	<p><i>Select folder</i></p>
	<ul class="folders break-word">
		<?php
		if ($parent !== false) {
			?>
			<li><a href="<?php echo FM_SELF_URL . urlencode($parent) ?>&data[copy]=<?php echo urlencode($copy) ?>"><i class="fa fa-chevron-circle-left"></i> ..</a></li>
			<?php
		}
		foreach ($folders as $f) {
			?>
			<li>
				<a href="<?php echo FM_SELF_URL . urlencode(trim(FM_PATH . '/' . $f, '/')) ?>&data[copy]=<?php echo urlencode($copy) ?>"><i class="fa fa-folder-o"></i> <?php echo fm_convert_win($f) ?></a></li>
			<?php
		}
		?>
	</ul>
</div>