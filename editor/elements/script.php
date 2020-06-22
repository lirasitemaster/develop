<?php defined('isENGINE') or die;

// https://github.com/json-editor/json-editor
// https://json-editor.github.io/json-editor/
// examples in /docs/ folder on repository and in site by link as
// https://json-editor.github.io/json-editor/wysiwyg.html

/*

#editModal form
$tbl
$js['editor']
$schema

*/

$form = objectProcess('system:write');

?>
<script>
$(document).ready(function(){

var tbl = <?= iniPrepareArray($tbl); ?>;

var jsoneditor;

JSONEditor.defaults.options.schema = <?= iniPrepareArray($schema); ?>;

var jsonreload = function() {
	if (jsoneditor) jsoneditor.destroy();
	jsoneditor = new JSONEditor(document.getElementById("<?= $id; ?>"), {
		theme : "bootstrap4",
		iconlib : "fontawesome5",
		object_layout : "normal",
		show_errors : "interaction"
		
		<?php if (!empty($sets['simple'])) : ?>,
		//collapsed : false,
		compact : false,
		no_additional_properties : true,
		disable_edit_json : true,
		disable_array_add : true,
		disable_array_delete : true,
		disable_array_delete_all_rows : true,
		disable_array_delete_last_row : true,
		disable_array_reorder : true,
		disable_collapse : false,
		disable_edit_json : true,
		disable_properties : true
		<?php endif; ?>
		
	});
	window.jsoneditor = jsoneditor;
	
	//console.log(jsoneditor);

};

jsonreload();

$("#editModal form").on("submit", function(e){
	
	e.preventDefault();
	
	var data = <?= iniPrepareArray($form['array']); ?>;
	
	data["data"] = {
		db : "<?= $db; ?>",
		name : jsoneditor.options.schema.title,
		type : "<?= $module -> data['type']; ?>",
		parent : JSON.stringify(<?= iniPrepareArray($module -> data['parent']); ?>),
		origin : JSON.stringify(<?= iniPrepareArray($module -> data['data']); ?>),
		data : JSON.stringify(jsoneditor.getValue())
	};
	
	$.post(
		"<?= $form['link']; ?>",
		data,
		function(data) {
			
			var err = false;
			
			if (!data) {
				err = true;
			} else {
				
				var name = jsoneditor.options.schema.title;
				
				try {
					tbl = JSON.parse(data);
				} catch (i) {
					err = true;
				}
				
				console.log(data);
				
			}
			
			if (err) {
				console.log("AHTUNG!!!");
			} else {
				console.log("ай да сукин сын");
			}
			
		}
	);
	
});

// вытаскиваем данные из текущей записи

var name = "<?= $module -> data['name']; ?>";
var data = tbl;

JSONEditor.defaults.options.schema.title = name;

jsonreload();
jsoneditor.setValue(data);

});
</script>
<?php unset($form); ?>