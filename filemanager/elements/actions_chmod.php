<?php defined('isCMS') or die; ?>

<div class="path">
	<div class="card mb-2">
		<h6 class="card-header">
			<?php echo lng('ChangePermissions') ?>
		</h6>
		<div class="card-body">
			<p class="card-text">
				Full path: <?php echo $file_path ?><br>
			</p>
			<form action="<?= $action['action']; ?>" method="post">
				<?php foreach ($action['fields'] as $i) { echo $i; } unset ($i); ?>
				<input type="hidden" name="data[p]" value="<?php echo fm_enc(FM_PATH) ?>">
				<input type="hidden" name="data[chmod]" value="<?php echo fm_enc($file) ?>">

				<table class="table compact-table">
					<tr>
						<td></td>
						<td><b><?php echo lng('Owner') ?></b></td>
						<td><b><?php echo lng('Group') ?></b></td>
						<td><b><?php echo lng('Other') ?></b></td>
					</tr>
					<tr>
						<td style="text-align: right"><b><?php echo lng('Read') ?></b></td>
						<td><label><input type="checkbox" name="data[ur]" value="1"<?php echo ($mode & 00400) ? ' checked' : '' ?>></label></td>
						<td><label><input type="checkbox" name="data[gr]" value="1"<?php echo ($mode & 00040) ? ' checked' : '' ?>></label></td>
						<td><label><input type="checkbox" name="data[or]" value="1"<?php echo ($mode & 00004) ? ' checked' : '' ?>></label></td>
					</tr>
					<tr>
						<td style="text-align: right"><b><?php echo lng('Write') ?></b></td>
						<td><label><input type="checkbox" name="data[uw]" value="1"<?php echo ($mode & 00200) ? ' checked' : '' ?>></label></td>
						<td><label><input type="checkbox" name="data[gw]" value="1"<?php echo ($mode & 00020) ? ' checked' : '' ?>></label></td>
						<td><label><input type="checkbox" name="data[ow]" value="1"<?php echo ($mode & 00002) ? ' checked' : '' ?>></label></td>
					</tr>
					<tr>
						<td style="text-align: right"><b><?php echo lng('Execute') ?></b></td>
						<td><label><input type="checkbox" name="data[ux]" value="1"<?php echo ($mode & 00100) ? ' checked' : '' ?>></label></td>
						<td><label><input type="checkbox" name="data[gx]" value="1"<?php echo ($mode & 00010) ? ' checked' : '' ?>></label></td>
						<td><label><input type="checkbox" name="data[ox]" value="1"<?php echo ($mode & 00001) ? ' checked' : '' ?>></label></td>
					</tr>
				</table>

				<p>
					<button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> <?php echo lng('Change') ?></button> &nbsp;
					<b><a href="<?php echo FM_SELF_URL . urlencode(FM_PATH) ?>" class="btn btn-outline-primary"><i class="fa fa-times-circle"></i> <?php echo lng('Cancel') ?></a></b>
				</p>
			</form>
		</div>
	</div>
</div>