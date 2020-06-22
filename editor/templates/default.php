<?php defined('isENGINE') or die; ?>

<div class="" id="editModal">
	
	<?php $form = objectProcess('system:write'); ?>
	<form method="post" action="<?= $form['action']; ?>">
		
		<div class="body">
			<div class="form-hidden">
				<?php
					foreach ($form['fields'] as $fi) {
						echo $fi;
					}
					unset($fi, $form);
				?>
			</div>
			<div class="form-container">
				<div id="<?= $id; ?>"></div>
			</div>
			<div class="form-json-editor">
				<div id="jsonEditor"></div>
				<pre id="jsonValue"></pre>
			</div>
		</div>
		
		<div class="footer">
			<button type="submit" class="btn btn-primary">Save</button>
		</div>
		
	</form>
	
</div>

<?php require $module -> elements . 'style.php'; ?>
<?php require $module -> elements . 'script.php'; ?>