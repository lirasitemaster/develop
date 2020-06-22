<?php defined('isCMS') or die;

/**
 * Show Header after login
 */

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />
<?php if (FM_USE_HIGHLIGHTJS): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/styles/<?= FM_HIGHLIGHTJS_STYLE ?>.min.css">
<?php endif; ?>
<?php require $module -> elements . 'style.php'; ?>
<div id="wrapper" class="container-fluid">

    <!-- New Item creation -->
    <div class="modal fade" id="createNewItem" tabindex="-1" role="dialog" aria-label="newItemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newItemModalLabel"><i class="<?= $classes['icons']['newitem-title']; ?>"></i><?= lng('CreateNewItem'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><label for="newfile"><?= lng('ItemType'); ?> </label></p>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline1" name="data[newfile]" value="file" class="custom-control-input">
                        <label class="custom-control-label" for="customRadioInline1"><?= lng('File'); ?></label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline2" name="data[newfile]" value="folder" class="custom-control-input" checked="">
                        <label class="custom-control-label" for="customRadioInline2"><?= lng('Folder'); ?></label>
                    </div>

                    <p class="mt-3"><label for="newfilename"><?= lng('ItemName'); ?> </label></p>
                    <input type="text" name="data[newfilename]" id="newfilename" value="" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="<?= $classes['icons']['cancel']; ?>"></i> <?= lng('Cancel'); ?></button>
                    <button type="button" class="btn btn-success" onclick="newfolder('<?= fm_enc(FM_PATH); ?>');return false;"><i class="<?= $classes['icons']['ok']; ?>"></i> <?= lng('CreateNow'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title col-10" id="searchModalLabel">
                <div class="input-group input-group">
                    <input type="text" class="form-control" placeholder="<?= lng('Search'); ?> a files" aria-label="<?= lng('Search'); ?>" aria-describedby="search-addon3" id="advanced-search" autofocus required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="search-addon3"><i class="<?= $classes['icons']['search']; ?>"></i></span>
                    </div>
                </div>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
			<form action="<?= $action['action']; ?>" method="post">
				<?php foreach ($action['fields'] as $i) { echo $i; } unset ($i); ?>
                <div class="lds-facebook"><div></div><div></div><div></div></div>
                <ul id="search-wrapper">
                    <p class="m-2">Search file in folder and subfolders...</p>
                </ul>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script type="text/html" id="js-tpl-modal">
        <div class="modal fade" id="js-ModalCenter-<%this.id%>" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalCenterTitle"><%this.title%></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <%this.content%>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="<?= $classes['icons']['cancel']; ?>"></i> <?= lng('Cancel'); ?></button>
                        <%if(this.action){%><button type="button" class="btn btn-primary" id="js-ModalCenterAction" data-type="js-<%this.action%>"><%this.action%></button><%}%>
                    </div>
                </div>
            </div>
        </div>
    </script>
	
	<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<?php require $module -> elements . 'nav.php'; ?>
		</div>
		<div class="col-12">