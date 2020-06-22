<?php defined('isENGINE') or die;

/**
 * Show nav block
 * @param string $path
 */

$getTheme = ' navbar-light bg-white';

?>
<nav class="navbar navbar-expand-sm <?= $getTheme; ?> main-nav">
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">

		<ul class="navbar-nav w-100 justify-content-between flex-wrap-reverse">
			
			<?php if (!FM_READONLY): ?>
			<div class="d-flex">
				<li class="nav-item">
					<a title="<?= lng('NewItem'); ?>" class="nav-link" href="#createNewItem" data-toggle="modal" data-target="#createNewItem"><i class="<?= $classes['icons']['newitem']; ?>"></i> <?= lng('NewItem'); ?></a>
				</li>
				<li class="nav-item">
					<a title="<?= lng('Upload'); ?>" class="nav-link" href="<?= FM_SELF_URL . urlencode(FM_PATH); ?>&data[upload]=1"><i class="<?= $classes['icons']['upload']; ?>" aria-hidden="true"></i> <?= lng('Upload'); ?></a>
				</li>
			</div>
			<?php endif; ?>
			
			<div class="d-flex">
				<li class="nav-item">
					<a title="<?= lng('Help'); ?>" class="nav-link" href="<?= FM_SELF_URL . urlencode(FM_PATH); ?>&data[help]=2"><i class="<?= $classes['icons']['help']; ?>" aria-hidden="true"></i> <?= lng('Help'); ?></a>
				</li>
				<li class="nav-item mr-2">
					<div class="input-group input-group-sm mr-1" style="margin-top:4px;">
						<input type="text" class="form-control" placeholder="<?= lng('Search'); ?>" aria-label="<?= lng('Search'); ?>" aria-describedby="search-addon2" id="search-addon">
						<div class="input-group-append">
							<span class="input-group-text" id="search-addon2"><i class="<?= $classes['icons']['search']; ?>"></i></span>
						</div>
						<div class="input-group-append btn-group">
							<span class="input-group-text dropdown-toggle" id="search-addon2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
							  <div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="<?= $path2 = $nav_path ? $nav_path : '.'; ?>" id="js-search-modal" data-toggle="modal" data-target="#searchModal">Advanced Search</a>
							  </div>
						</div>
					</div>
				</li>
			</div>
			
		</ul>
		
	</div>
</nav>

<?php
$nav_path = fm_clean_path(FM_PATH);
$nav_root_url = '<a href="' . FM_SELF_URL . '"><i class="' . $classes['icons']['home'] . '" aria-hidden="true" title="' . FM_ROOT_PATH . '"></i></a>';
$sep = '<i class="bread-crumb"> / </i>';
if ($nav_path != '') {
	$exploded = explode('/', $nav_path);
	$count = count($exploded);
	$array = array();
	$nav_parent = '';
	for ($i = 0; $i < $count; $i++) {
		$nav_parent = trim($nav_parent . '/' . $exploded[$i], '/');
		$parent_enc = urlencode($nav_parent);
		$array[] = '<a href="' . FM_SELF_URL . $parent_enc . '">' . fm_enc(fm_convert_win($exploded[$i])) . '</a>';
	}
	$nav_root_url .= $sep . implode($sep, $array);
}
?>
<div class="col-12 py-3"><?= $nav_root_url; ?></div>
<?php
unset($nav_path, $nav_root_url, $nav_parent);
?>