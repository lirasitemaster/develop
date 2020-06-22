<?php defined('isCMS') or die;

//--- FILEMANAGER MAIN
require $module -> elements . 'header.php'; // HEADER
require $module -> process . 'actions_second.php';

if (empty($fm_not_display_form)) :

// messages
fm_show_message();

$num_files = count($files);
$num_folders = count($folders);
$all_files_size = 0;
?>
<form action="<?= $action['action']; ?>" method="post">
	<?php foreach ($action['fields'] as $i) { echo $i; } unset ($i); ?>
    <input type="hidden" name="data[p]" value="<?= fm_enc(FM_PATH); ?>">
    <input type="hidden" name="data[group]" value="1">
        <table class="table table-responsive table-bordered table-hover table-sm bg-white" id="main-table">
            <thead class="thead-white">
            <tr>
                <?php if (!FM_READONLY): ?>
                    <th style="width:3%" class="custom-checkbox-header">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="js-select-all-items" onclick="checkbox_toggle()">
                            <label class="custom-control-label" for="js-select-all-items"></label>
                        </div>
                    </th>
				<?php endif; ?>
                <th><?= lng('Name'); ?></th>
                <th><?= lng('Size'); ?></th>
                <th><?= lng('Modified'); ?></th>
                <?php if (!FM_IS_WIN && !$hide_Cols): ?>
                    <th><?= lng('Perms'); ?></th>
                    <th><?= lng('Owner'); ?></th>
				<?php endif; ?>
                <th><?= lng('Actions'); ?></th>
            </tr>
            </thead>
            <?php
            // link to parent folder
            if ($parent !== false) {
                ?>
                <tr>
					<?php if (!FM_READONLY): ?>
                    <td class="nosort"></td>
					<?php endif; ?>
                    <td class="border-0"><a href="<?= FM_SELF_URL . urlencode($parent); ?>"><i class="<?= $classes['icons']['back']; ?> go-back"></i> ..</a></td>
                    <td class="border-0"></td>
                    <td class="border-0"></td>
					<td class="border-0"></td>
                    <?php if (!FM_IS_WIN && !$hide_Cols) { ?>
                        <td class="border-0"></td>
                        <td class="border-0"></td>
                    <?php } ?>
                </tr>
                <?php
            }
			
			require $module -> elements . 'inner.php';
			
            if (empty($folders) && empty($files)) {
                ?>
                <tfoot>
                    <tr><?php if (!FM_READONLY): ?>
                            <td></td><?php endif; ?>
                        <td colspan="<?= (!FM_IS_WIN && !$hide_Cols) ? '6' : '4' ?>"><em><?= 'Folder is empty' ?></em></td>
                    </tr>
                </tfoot>
                <?php
            } else {
                ?>
                <tfoot>
                    <tr><?php if (!FM_READONLY): ?>
                            <td class="gray"></td><?php endif; ?>
                        <td class="gray" colspan="<?= (!FM_IS_WIN && !$hide_Cols) ? '6' : '4' ?>">
                            <?= lng('FullSize').': <span class="badge badge-light">'.fm_get_filesize($all_files_size).'</span>' ?>
                            <?= lng('File').': <span class="badge badge-light">'.$num_files.'</span>' ?>
                            <?= lng('Folder').': <span class="badge badge-light">'.$num_folders.'</span>' ?>
                            <?= lng('MemoryUsed').': <span class="badge badge-light">'.fm_get_filesize(@memory_get_usage(true)).'</span>' ?>
                            <?= lng('PartitionSize').': <span class="badge badge-light">'.fm_get_filesize(@disk_free_space($path)) .'</span> '.lng('FreeOf').' <span class="badge badge-light">'.fm_get_filesize(@disk_total_space($path)).'</span>'; ?>
                        </td>
                    </tr>
                </tfoot>
                <?php
            }
            ?>
        </table>

    <div class="row py-3">
        <?php if (!FM_READONLY): ?>
        <div class="col-12">
            <ul class="list-inline footer-action">
                <li class="list-inline-item"> <a href="#/select-all" class="btn btn-small btn-outline-primary btn-2" onclick="select_all();return false;"><i class="<?= $classes['icons']['select-all']; ?>"></i> <?= lng('SelectAll'); ?> </a></li>
                <li class="list-inline-item"><a href="#/unselect-all" class="btn btn-small btn-outline-primary btn-2" onclick="unselect_all();return false;"><i class="<?= $classes['icons']['unselect-all']; ?>"></i> <?= lng('UnSelectAll'); ?> </a></li>
                <li class="list-inline-item"><a href="#/invert-all" class="btn btn-small btn-outline-primary btn-2" onclick="invert_all();return false;"><i class="<?= $classes['icons']['invert-all']; ?>"></i> <?= lng('InvertSelection'); ?> </a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="data[delete]" id="a-delete" value="Delete" onclick="return confirm('Delete selected files and folders?')">
                    <a href="javascript:document.getElementById('a-delete').click();" class="btn btn-small btn-outline-primary btn-2"><i class="<?= $classes['icons']['trash']; ?>"></i> <?= lng('Delete'); ?> </a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="data[zip]" id="a-zip" value="zip" onclick="return confirm('Create archive?')">
                    <a href="javascript:document.getElementById('a-zip').click();" class="btn btn-small btn-outline-primary btn-2"><i class="<?= $classes['icons']['archive']; ?>"></i> <?= lng('Zip'); ?> </a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="data[tar]" id="a-tar" value="tar" onclick="return confirm('Create archive?')">
                    <a href="javascript:document.getElementById('a-tar').click();" class="btn btn-small btn-outline-primary btn-2"><i class="<?= $classes['icons']['archive']; ?>"></i> <?= lng('Tar'); ?> </a></li>
                <li class="list-inline-item"><input type="submit" class="hidden" name="data[copy]" id="a-copy" value="Copy">
                    <a href="javascript:document.getElementById('a-copy').click();" class="btn btn-small btn-outline-primary btn-2"><i class="<?= $classes['icons']['copy']; ?>"></i> <?= lng('Copy'); ?> </a></li>
            </ul>
        </div>
        <?php endif; ?>
    </div>

</form>
<?php
endif;
require $module -> elements . 'footer.php';

//--- END

?>