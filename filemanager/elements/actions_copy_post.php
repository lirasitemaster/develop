<?php defined('isCMS') or die; ?>

<div class="path">
	<div class="card">
		<div class="card-header">
			<h6><?php echo lng('Copying') ?></h6>
		</div>
		<div class="card-body">
			<form action="<?= $action['action']; ?>" method="post">
				<?php foreach ($action['fields'] as $i) { echo $i; } unset ($i); ?>
				<input type="hidden" name="data[p]" value="<?php echo fm_enc(FM_PATH) ?>">
				<input type="hidden" name="data[finish]" value="1">
				<?php
				foreach ($copy_files as $cf) {
					echo '<input type="hidden" name="data[file][]" value="' . fm_enc($cf) . '">' . PHP_EOL;
				}
				?>
				<p class="break-word"><?php echo lng('Files') ?>: <b><?php echo implode('</b>, <b>', $copy_files) ?></b></p>
				<p class="break-word"><?php echo lng('SourceFolder') ?>: <?php echo fm_enc(fm_convert_win(FM_ROOT_PATH . '/' . FM_PATH)) ?><br>
					<label for="inp_copy_to"><?php echo lng('DestinationFolder') ?>:</label>
					<?php echo FM_ROOT_PATH ?>/<input type="text" name="data[copy_to]" id="inp_copy_to" value="<?php echo fm_enc(FM_PATH) ?>">
				</p>
				<p class="custom-checkbox custom-control"><input type="checkbox" name="data[move]" value="1" id="js-move-files" class="custom-control-input"><label for="js-move-files" class="custom-control-label" style="vertical-align: sub"> <?php echo lng('Move') ?></label></p>
				<p>
					<button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> <?php echo lng('Copy') ?></button> &nbsp;
					<b><a href="<?php echo FM_SELF_URL . urlencode(FM_PATH) ?>" class="btn btn-outline-primary"><i class="fa fa-times-circle"></i> <?php echo lng('Cancel') ?></a></b>
				</p>
			</form>
		</div>
	</div>
</div>