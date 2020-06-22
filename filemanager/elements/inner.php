<?php defined('isCMS') or die;

foreach (['folders', 'files'] as $t) {
	$d = $t === 'folders' ? true : false;
	$ii = $d ? 3399 : 6070;
	
	foreach ($$t as $f) {
		$is_link = is_link($path . '/' . $f);
		$img = $is_link ? ($d ? 'icon-link_folder' : $classes['icons']['file-link']) : ($d ? $classes['icons']['folder'] : fm_get_file_icon_class($path . '/' . $f));
		$modif = date(FM_DATETIME_FORMAT, filemtime($path . '/' . $f));
		
		if (!$d) {
			$filesize_raw = fm_get_size($path . '/' . $f);
			$filesize = fm_get_filesize($filesize_raw);
			$filelink = urlencode(FM_PATH) . '&data[view]=' . urlencode($f);
			$all_files_size += $filesize_raw;
		}
		
		$perms = substr(decoct(fileperms($path . '/' . $f)), -4);
		if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
			$owner = posix_getpwuid(fileowner($path . '/' . $f));
			$group = posix_getgrgid(filegroup($path . '/' . $f));
		} else {
			$owner = array('name' => '?');
			$group = array('name' => '?');
		}
		?>
		<tr>
			<?php if (!FM_READONLY): ?>
				<td class="custom-checkbox-td">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="<?= $ii; ?>" name="data[file][]" value="<?= fm_enc($f); ?>">
					<label class="custom-control-label" for="<?= $ii; ?>"></label>
				</div>
				</td>
			<?php endif; ?>
			<td>
				<div class="filename">
				
				<?php if ($d) : ?>
				
					<a href="<?= FM_SELF_URL . urlencode(trim(FM_PATH . '/' . $f, '/')); ?>"><i class="<?= $img; ?>"></i> <?= fm_convert_win(fm_enc($f)); ?>
					</a><?= $is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : null; ?>
				
				<?php else : ?>
				
					<?php if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg'))): ?>
						<?php $imagePreview = fm_enc(FM_ROOT_URL . (FM_PATH != '' ? FM_PATH . '/' : '') . $f); ?>
						<a href="<?= FM_SELF_URL . $filelink; ?>" data-preview-image="<?= $imagePreview ?>" title="<?= $f; ?>">
					<?php else: ?>
						<a href="<?= FM_SELF_URL . $filelink; ?>" title="<?= $f; ?>">
					<?php endif; ?>
							<i class="<?= $img; ?>"></i> <?= fm_convert_win($f); ?>
						</a>
					<?php echo($is_link ? ' &rarr; <i>' . readlink($path . '/' . $f) . '</i>' : ''); ?>
				
				<?php endif; ?>
				
				</div>
			</td>
			<td>
				<?php if ($d) : ?>
					<?php if ($calc_folder) { echo fm_get_directorysize($path . '/' . $f); } else { echo lng('Folder'); } ?>
				<?php else : ?>
					<span title="<?php printf('%s bytes', $filesize_raw); ?>">
					<?= $filesize; //$filesize_raw; ?>
					</span>
				<?php endif; ?>
			</td>
			<td><?= $modif; ?></td>
			<?php if (!FM_IS_WIN && !$hide_Cols): ?>
				<td><?php if (!FM_READONLY): ?><a title="Change Permissions" href="<?= FM_SELF_URL . urlencode(FM_PATH); ?>&data[chmod]=<?= urlencode($f); ?>"><?= $perms; ?></a><?php else: ?><?= $perms; ?><?php endif; ?>
				</td>
				<td><?= fm_enc($owner['name'] . ':' . $group['name']); ?></td>
			<?php endif; ?>
			<td class="inline-actions">
				
				<a title="<?= lng('DirectLink'); ?>" href="<?= fm_enc(FM_ROOT_URL . (FM_PATH != '' ? FM_PATH . '/' : '') . $f . ($d ? '/' : null)); ?>" target="_blank"><i class="<?= $classes['icons']['link']; ?>" aria-hidden="true"></i></a>
				
				<?php if (!FM_READONLY): ?>
					
					<a title="<?= lng('CopyTo'); ?>..." href="<?= FM_SELF_URL . (!$d ? urlencode(FM_PATH) : null); ?>&data[copy]=<?= urlencode(trim(FM_PATH . '/' . $f, '/')); ?>"><i class="<?= $classes['icons']['copy']; ?>" aria-hidden="true"></i></a>
					
					<a title="<?= lng('Rename'); ?>" href="#" onclick="rename('<?= fm_enc(FM_PATH); ?>', '<?= fm_enc(addslashes($f)); ?>');return false;"><i class="<?= $classes['icons']['rename']; ?>" aria-hidden="true"></i></a>
					
					<a title="<?= lng('Delete'); ?>" href="<?= FM_SELF_URL . urlencode(FM_PATH); ?>&data[del]=<?= urlencode($f); ?>" onclick="return confirm('<?= lng('Delete').' '.lng($d ? 'Folder' : 'File').'?'; ?>\n \n ( <?= urlencode($f); ?> )');"> <i class="<?= $classes['icons']['trash']; ?>" aria-hidden="true"></i></a>
					
				<?php endif; ?>
				<?php if (!$d) : ?>
					<a title="<?= lng('Download'); ?>" href="<?= FM_ACTION_URL; ?>&data[dl]=<?= urlencode($f); ?>"><i class="<?= $classes['icons']['download']; ?>" aria-hidden="true"></i></a>
				<?php endif; ?>
			</td>
		</tr>
		<?php
		flush();
		$ii++;
	}
	
	unset($f, $d, $ii);
}
unset($t);

?>