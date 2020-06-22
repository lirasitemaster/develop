<?php defined('isENGINE') or die; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet">
<div class="path">

	<div class="card mb-2 fm-upload-wrapper">
		<div class="card-header">
			<ul class="nav nav-tabs card-header-tabs">
				<li class="nav-item">
					<a class="nav-link active" href="#fileUploader" data-target="#fileUploader"><i class="fa fa-arrow-circle-o-up"></i> <?php echo lng('UploadingFiles') ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#urlUploader" class="js-url-upload" data-target="#urlUploader"><i class="fa fa-link"></i> Upload from URL</a>
				</li>
			</ul>
		</div>
		<div class="card-body">
			<p class="card-text">
				<a href="<?php echo FM_SELF_URL . FM_PATH ?>" class="float-right"><i class="fa fa-chevron-circle-left go-back"></i> <?php echo lng('Back')?></a>
				<?php echo lng('DestinationFolder') ?>: <?php echo fm_enc(fm_convert_win('/' . FM_PATH)) ?>
			</p>

			<form action="<?= $action['action']; ?>" class="dropzone card-tabs-container" id="fileUploader" enctype="multipart/form-data">
				<?php foreach ($action['fields'] as $i) { echo $i; } unset ($i); ?>
				<input type="hidden" name="data[p]" value="<?php echo fm_enc(FM_PATH) ?>">
				<input type="hidden" name="data[fullpath]" id="fullpath" value="<?php echo fm_enc(FM_PATH) ?>">
				<div class="fallback">
					<input name="data[file]" type="file" multiple/>
				</div>
			</form>

			<div class="upload-url-wrapper card-tabs-container hidden" id="urlUploader">
				<form id="js-form-url-upload" class="form-inline" onsubmit="return upload_from_url(this);" method="POST" action="">
					<input type="hidden" name="data[type]" value="upload" aria-label="hidden" aria-hidden="true">
					<input type="url" placeholder="URL" name="data[uploadurl]" required class="form-control" style="width: 80%">
					<button type="submit" class="btn btn-primary ml-3"><?php echo lng('Upload') ?></button>
					<div class="lds-facebook"><div></div><div></div><div></div></div>
				</form>
				<div id="js-url-upload__list" class="col-9 mt-3"></div>
			</div>
		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script>
	Dropzone.options.fileUploader = {
		timeout: 120000,
		maxFilesize: <?php echo MAX_UPLOAD_SIZE; ?>,
		acceptedFiles : "<?php echo getUploadExt() ?>",
		init: function () {
			this.on("sending", function (file, xhr, formData) {
				let _path = (file.fullPath) ? file.fullPath : file.name;
				document.getElementById("fullpath").value = _path;
				xhr.ontimeout = (function() {
					toast('Error: Server Timeout');
				});
			}).on("success", function (res) {
				let _response = JSON.parse(res.xhr.response);
				if(_response.status == "error") {
					toast(_response.info);
				}
			}).on("error", function(file, response) {
				toast(response);
			});
		}
	}
</script>