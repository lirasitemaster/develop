<?php defined('isCMS') or die; ?>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			
			<?php $form = objectProcess('system:write'); ?>
			<form method="post" action="<?= $form['action']; ?>">
			
			<div class="modal-header">
				<h5 class="modal-title" id="editModalLabel"><?= $labels['edit']; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="modal-form-hidden">
					
					<?php
						foreach ($form['fields'] as $fi) {
							echo $fi;
						}
						unset($fi, $form);
					?>
					
				</div>
				<div class="modal-form-container">
					<div id="<?= $js['editor']; ?>"></div>
				</div>
				<div class="modal-form-json-editor">
					
					<div id="jsonEditor"></div>
					<pre id="jsonValue"></pre>
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="submit" class="btn btn-primary" data-dismiss="modal">Save and close</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
			</form>
		</div>
	</div>
</div>
