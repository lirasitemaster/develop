<?php defined('isENGINE') or die; ?>

<div class="path">
	<div class="row">
		<div class="edit-file-actions col-12 py-2">
			<a title="Back" class="btn btn-sm btn-outline-primary" href="<?php echo FM_SELF_URL . urlencode(trim(FM_PATH)) ?>&data[view]=<?php echo urlencode($file) ?>"><i class="fa fa-reply-all"></i> <?php echo lng('Back') ?></a>
			<a title="Backup" class="btn btn-sm btn-outline-primary" href="javascript:void(0);" onclick="backup('<?php echo urlencode(trim(FM_PATH)) ?>','<?php echo urlencode($file) ?>')"><i class="fa fa-database"></i> <?php echo lng('BackUp') ?></a>
			<?php if ($is_text) { ?>
				<?php if (!$isNormalEditor) : ?>
					<a title="Plain Editor" class="btn btn-sm btn-outline-primary" href="<?php echo FM_SELF_URL . urlencode(trim(FM_PATH)) ?>&data[edit]=<?php echo urlencode($file) ?>"><i class="fa fa-text-height"></i> <?php echo lng('NormalEditor') ?></a>
				<?php endif; ?>
				<?php if ($isAdvancedEditor !== 'ace') : ?>
					<a title="Advanced" class="btn btn-sm btn-outline-primary" href="<?php echo FM_SELF_URL . urlencode(trim(FM_PATH)) ?>&data[edit]=<?php echo urlencode($file) ?>&data[env]=ace"><i class="fa fa-pen-square"></i> ACE</a>
				<?php endif; ?>
				<?php if ($isAdvancedEditor !== 'tiny') : ?>
					<a title="Advanced" class="btn btn-sm btn-outline-primary" href="<?php echo FM_SELF_URL . urlencode(trim(FM_PATH)) ?>&data[edit]=<?php echo urlencode($file) ?>&data[env]=tiny"><i class="fa fa-pen-square"></i> TinyMCE</a>
				<?php endif; ?>
				<?php if ($isAdvancedEditor !== 'ck') : ?>
					<a title="Advanced" class="btn btn-sm btn-outline-primary" href="<?php echo FM_SELF_URL . urlencode(trim(FM_PATH)) ?>&data[edit]=<?php echo urlencode($file) ?>&data[env]=ck"><i class="fa fa-pen-square"></i> CK Editor</a>
				<?php endif; ?>
				<button type="button" class="btn btn-sm btn-outline-primary" name="Save" data-url="<?php echo fm_enc($file_url) ?>" onclick="edit_save(this,'<?= $isAdvancedEditor && !$isNormalEditor ? $isAdvancedEditor : 'nrl'; ?>')"><i class="fa fa-save fa-floppy-o"></i> <?php echo lng('Save') ?></button>
			<?php } ?>
		</div>
	</div>
	<?php if (!$isNormalEditor && $isAdvancedEditor === 'ace') { ?>
	<div class="row">
		<div class="col-12 py-2">
			<div class="btn-toolbar" role="toolbar">
				<div class="btn-group js-ace-toolbar">
					<button data-cmd="none" data-option="fullscreen" class="btn btn-sm btn-outline-secondary" id="js-ace-fullscreen" title="Fullscreen"><i class="fa fa-expand" title="Fullscreen"></i></button>
					<button data-cmd="find" class="btn btn-sm btn-outline-secondary" id="js-ace-search" title="Search"><i class="fa fa-search" title="Search"></i></button>
					<button data-cmd="undo" class="btn btn-sm btn-outline-secondary" id="js-ace-undo" title="Undo"><i class="fa fa-undo" title="Undo"></i></button>
					<button data-cmd="redo" class="btn btn-sm btn-outline-secondary" id="js-ace-redo" title="Redo"><i class="fa fa-redo fa-repeat" title="Redo"></i></button>
					<button data-cmd="none" data-option="wrap" class="btn btn-sm btn-outline-secondary" id="js-ace-wordWrap" title="Word Wrap"><i class="fa fa-text-width" title="Word Wrap"></i></button>
					<button data-cmd="none" data-option="help" class="btn btn-sm btn-outline-secondary" id="js-ace-goLine" title="Help"><i class="fa fa-question" title="Help"></i></button>
					<select id="js-ace-mode" data-type="mode" title="Select Document Type" class="btn-outline-secondary border-left-0 d-none d-md-block"><option>-- Select Mode --</option></select>
					<select id="js-ace-theme" data-type="theme" title="Select Theme" class="btn-outline-secondary border-left-0 d-none d-lg-block"><option>-- Select Theme --</option></select>
					<select id="js-ace-fontSize" data-type="fontSize" title="Selct Font Size" class="btn-outline-secondary border-left-0 d-none d-lg-block"><option>-- Select Font Size --</option></select>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<div class="col-12 py-2">
			<?php
			if ($is_text && $isNormalEditor) {
				echo '<textarea class="mt-2" id="normal-editor" rows="33" cols="120" style="width: 99.5%;">' . htmlspecialchars($content) . '</textarea>';
			} elseif ($is_text) {
				if ($isAdvancedEditor === 'ace' || !in_array($ext, ['php', 'html', 'htm'])) { $content = htmlspecialchars($content); }
				echo '<div id="editor" contenteditable="true">' . $content . '</div>';
			} else {
				fm_set_msg('FILE EXTENSION HAS NOT SUPPORTED', 'error');
			}
			?>
		</div>
	</div>
</div>