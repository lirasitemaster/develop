<?php defined('isENGINE') or die; ?>

<div class="card mb-2">
	<h6 class="card-header">
		<i class="fa fa-exclamation-circle"></i> <?php echo lng('Help') ?>
		<a href="<?php echo FM_SELF_URL . FM_PATH ?>" class="float-right"><i class="fa fa-window-close"></i> <?php echo lng('Cancel')?></a>
	</h6>
	<div class="card-body">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<p><h3><a href="https://github.com/prasathmani/tinyfilemanager" target="_blank" class="app-v-title"> Tiny File Manager <?php echo VERSION; ?></a></h3></p>
				<p>Author: Prasath Mani</p>
				<p>Mail Us: <a href="mailto:ccpprogrammers@gmail.com">ccpprogrammers[at]gmail.com</a> </p>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="card">
					<ul class="list-group list-group-flush">
						<li class="list-group-item"><a href="https://github.com/prasathmani/tinyfilemanager/wiki" target="_blank"><i class="fa fa-question-circle"></i> <?php echo lng('Help Documents') ?> </a> </li>
						<li class="list-group-item"><a href="https://github.com/prasathmani/tinyfilemanager/issues" target="_blank"><i class="fa fa-bug"></i> <?php echo lng('Report Issue') ?></a></li>
						<li class="list-group-item"><a href="javascript:latest_release_info('<?php echo VERSION; ?>');"><i class="fa fa-link"> </i> <?php echo lng('Check Latest Version') ?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>