<?php defined('isCMS') or die; ?>

<div class="card mb-2">
	<h6 class="card-header">
		<a href="<?= FM_SELF_URL . FM_PATH; ?>" class="float-right"><i class="fa fa-chevron-circle-left go-back"></i> <?= lng('Back'); ?></a>
		<?= $view_title ?> "<?= fm_enc(fm_convert_win($file)); ?>"
	</h6>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<p class="break-word">
					Full path: <?= fm_enc(fm_convert_win($file_url)); ?><br>
					File size: <?= fm_get_filesize($filesize); ?><?php if ($filesize >= 1000): ?> (<?= sprintf('%s bytes', $filesize); ?>)<?php endif; ?><br>
					MIME-type: <?= $mime_type; ?><br>
					<?php
					// ZIP info
					if (($is_zip || $is_gzip) && $filenames !== false) {
						$total_files = 0;
						$total_comp = 0;
						$total_uncomp = 0;
						foreach ($filenames as $fn) {
							if (!$fn['folder']) {
								$total_files++;
							}
							$total_comp += $fn['compressed_size'];
							$total_uncomp += $fn['filesize'];
						}
						?>
						Files in archive: <?= $total_files; ?><br>
						Total size: <?= fm_get_filesize($total_uncomp); ?><br>
						Size in archive: <?= fm_get_filesize($total_comp); ?><br>
						Compression: <?= round(($total_comp / $total_uncomp) * 100); ?>%<br>
						<?php
					}
					// Image info
					if ($is_image) {
						$image_size = getimagesize($file_path);
						echo 'Image sizes: ' . (isset($image_size[0]) ? $image_size[0] : '0') . ' x ' . (isset($image_size[1]) ? $image_size[1] : '0') . '<br>';
					}
					// Text info
					if ($is_text) {
						$is_utf8 = fm_is_utf8($content);
						if (function_exists('iconv')) {
							if (!$is_utf8) {
								$content = iconv(FM_ICONV_INPUT_ENC, 'UTF-8//IGNORE', $content);
							}
						}
						echo 'Charset: ' . ($is_utf8 ? 'utf-8' : '8 bit') . '<br>';
					}
					?>
				</p>
				<p>
					<b><a href="<?= FM_ACTION_URL; ?>&data[dl]=<?= urlencode($file); ?>" target="_blank"><i class="fa fas fa-download"></i> <?= lng('Download'); ?></a></b> &nbsp;
					<?php if ($ext !== 'php' && $ext !== 'ini') : ?>
					<b><a href="<?= fm_enc($file_url); ?>" target="_blank"><i class="fa fas fa-link"></i> <?= lng('Open'); ?></a></b> &nbsp;
					<?php endif; ?>
					<?php
					// ZIP actions
					if (!FM_READONLY && ($is_zip || $is_gzip) && $filenames !== false) {
						$zip_name = pathinfo($file_path, PATHINFO_FILENAME);
					?>
						<b><a href="<?= FM_SELF_URL . urlencode(FM_PATH); ?>&data[unzip]=<?= urlencode($file); ?>"><i class="fa fa-check-circle"></i> <?= lng('UnZip') ?></a></b> &nbsp;
						<b><a href="<?= FM_SELF_URL . urlencode(FM_PATH); ?>&data[unzip]=<?= urlencode($file); ?>&data[tofolder]=1" title="UnZip to <?= fm_enc($zip_name); ?>"><i class="fa fa-check-circle"></i> <?= lng('UnZipToFolder'); ?></a></b> &nbsp;
					<?php
					}
					if ($is_text && !FM_READONLY) {
					?>
						<b><a href="<?= FM_SELF_URL . urlencode(trim(FM_PATH)); ?>&data[edit]=<?= urlencode($file); ?>" class="edit-file"><i class="fa fas fa-pen"></i> <?= lng('Edit'); ?></a></b> &nbsp;
						<b><a href="<?= FM_SELF_URL . urlencode(trim(FM_PATH)); ?>&data[edit]=<?= urlencode($file); ?>&data[env]=ace" class="edit-file"><i class="fa fas fa-pen-square"></i> ACE</a></b> &nbsp;
						<b><a href="<?= FM_SELF_URL . urlencode(trim(FM_PATH)); ?>&data[edit]=<?= urlencode($file); ?>&data[env]=tiny" class="edit-file"><i class="fa fas fa-pen-square"></i> TinyMCE</a></b> &nbsp;
						<b><a href="<?= FM_SELF_URL . urlencode(trim(FM_PATH)); ?>&data[edit]=<?= urlencode($file); ?>&data[env]=ck" class="edit-file"><i class="fa fas fa-pen-square"></i> CK Editor</a></b> &nbsp;
					<?php } ?>
				</p>
				<?php
				if($is_onlineViewer) {
					if($online_viewer == 'google') {
						echo '<iframe src="https://docs.google.com/viewer?embedded=true&hl=en&url=' . fm_enc($file_url) . '" frameborder="no" style="width:100%;min-height:460px"></iframe>';
					} else if($online_viewer == 'microsoft') {
						echo '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' . fm_enc($file_url) . '" frameborder="no" style="width:100%;min-height:460px"></iframe>';
					}
				} elseif ($is_zip) {
					// ZIP content
					if ($filenames !== false) {
						echo '<code class="maxheight">';
						foreach ($filenames as $fn) {
							if ($fn['folder']) {
								echo '<b>' . fm_enc($fn['name']) . '</b><br>';
							} else {
								echo $fn['name'] . ' (' . fm_get_filesize($fn['filesize']) . ')<br>';
							}
						}
						echo '</code>';
					} else {
						echo '<p>Error while fetching archive info</p>';
					}
				} elseif ($is_image) {
					// Image content
					if (in_array($ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg'))) {
						echo '<p><img src="' . fm_enc($file_url) . '" alt="" class="preview-img"></p>';
					}
				} elseif ($is_audio) {
					// Audio content
					echo '<p><audio src="' . fm_enc($file_url) . '" controls preload="metadata"></audio></p>';
				} elseif ($is_video) {
					// Video content
					echo '<div class="preview-video"><video src="' . fm_enc($file_url) . '" width="640" height="360" controls preload="metadata"></video></div>';
				} elseif ($is_text) {
					if (FM_USE_HIGHLIGHTJS) {
						// highlight
						$hljs_classes = array(
							'shtml' => 'xml',
							'htaccess' => 'apache',
							'phtml' => 'php',
							'lock' => 'json',
							'svg' => 'xml',
						);
						$hljs_class = isset($hljs_classes[$ext]) ? 'lang-' . $hljs_classes[$ext] : 'lang-' . $ext;
						if (empty($ext) || in_array(strtolower($file), fm_get_text_names()) || preg_match('#\.min\.(css|js)$#i', $file)) {
							$hljs_class = 'nohighlight';
						}
						$content = '<pre class="with-hljs"><code class="' . $hljs_class . '">' . fm_enc($content) . '</code></pre>';
					} elseif (in_array($ext, array('php', 'php4', 'php5', 'phtml', 'phps'))) {
						// php highlight
						$content = highlight_string($content, true);
					} else {
						$content = '<pre>' . fm_enc($content) . '</pre>';
					}
					echo $content;
				}
				?>
			</div>
		</div>
	</div>
</div>