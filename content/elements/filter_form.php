<?php defined('isENGINE') or die;
$form = objectProcess('content:filter');
?>

<form class="<?= $filter -> class['common']; ?>" method="post" action="<?= $form['action']; ?>">
	
	<?php
		foreach ($form['fields'] as $fi) {
			echo $fi;
		}
		unset($fi, $form);
	?>
	
	<input type="hidden" name="status" value="<?= $module -> param; ?>">
	<input type="hidden" name="data[target]" value="<?= $uri -> path -> string; ?>" readonly>
	
	<?php if (!empty($form -> options['ajax'])) : ?>
		<input type="hidden" name="data[ajax]" value="<?= $filter -> options['ajax']; ?>">
	<?php endif; ?>
	
	<?php
		
		if (file_exists($module -> path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $module -> param . '_filter.php')) :
			require $module -> path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $module -> param . '_filter.php';
		else :
			
	?>
		
		<?php foreach ($filter -> data as $filter_name => $filter_current) : ?>
			
			<?php if (!empty($filter -> options['wrapper'])) : ?>
				<div class="<?= $filter -> class['wrapper']; ?>">
			<?php endif; ?>
			
			<?php if (!empty($filter -> options['labels'])) : ?>
				<label for="<?= $filter -> class['prefixid'] . $filter_name; ?>" class="<?= $filter -> class['label'] . ' ' . $filter -> class['prefixlabel'] . $filter_name; ?>">
					<?= lang('filter:' . $filter_name) ? lang('filter:' . $filter_name) : $filter_name; ?>
				</label>
			<?php endif; ?>
			
			<?php
				//$filter_filtration = $filter -> form[$filter_name][0];
				$filter_type = $filter -> form[$filter_name];
				if (empty($filter_type)) {
					$filter_type = 'select';
				}
				
				$filter_values = $filter -> array[$filter_name];
				//print_r($filter_values);
			?>
			
			<input
				name="data[types][<?= $filter_name; ?>]"
				value="<?= $filter_type; ?>"
				type="hidden"
				readonly
			>
			
			<?php
				// and/or/multi
				if ($filter_type === 'or' || $filter_type === 'and' || $filter_type === 'multi') :
			?>
				
				<?php
					if ($filter_type === 'or') :
						$input_type = 'radio';
					else :
						$input_type = 'checkbox';
					endif;
				?>
					
					<?php if ($filter_type === 'or') : ?>
					
					<div class="<?= $filter -> class['prefixname'] . $filter_name . ' ' . $filter -> class['prefixtype'] . $filter_type; ?>">
						<input
							id="<?= $filter -> class['prefixid'] . $filter_name; ?>"
							name="data[filter][<?= $filter_name; ?>]"
							value=""
							type="<?= $input_type; ?>"
							class="<?= $filter -> class['item']; ?>"
						>
						<?= lang('action:all'); ?>
					</div>
					
					<?php endif; ?>
					
				<?php foreach ($filter_current as $filter_key => $filter_item) : ?>
					
					<div class="<?= $filter -> class['prefixname'] . $filter_name . ' ' . $filter -> class['prefixtype'] . $filter_type; ?>">
						<input
							id="<?= $filter -> class['prefixid'] . $filter_name . '_' . $filter_key; ?>"
							name="data[filter][<?= $filter_name; ?>]<?= ($filter_type !== 'or') ? '[' . $filter_key . ']' : null; ?>"
							value="<?= $filter_key; ?>"
							type="<?= $input_type; ?>"
							class="<?= $filter -> class['item']; ?>"
							<?php
								if (
									objectIs($filter_values) &&
									in_array($filter_key, $filter_values)
								) {
									echo 'checked';
								}
							?>
						>
						<label for="<?= $filter -> class['prefixid'] . $filter_name . '_' . $filter_key; ?>">
						<?= $filter_item; ?>
						</label>
					</div>
					
				<?php endforeach; ?>
				
			<?php
				// search
				elseif ($filter_type === 'search') :
			?>
				
				<div class="<?= $filter -> class['prefixname'] . $filter_name . ' ' . $filter -> class['prefixtype'] . $filter_type; ?>">
					<input
						name="data[filter][<?= $filter_name; ?>]"
						list="<?= $filter -> class['prefixid'] . $filter_name; ?>"
						class="<?= $filter -> class['item']; ?>"
					>
					<datalist id="<?= $filter -> class['prefixid'] . $filter_name; ?>">
						
						<?php foreach ($filter_current as $filter_key => $filter_item) : ?>
							<option
							value="<?= $filter_key; ?>"
							<?php
								if (
									objectIs($filter_values) &&
									in_array($filter_key, $filter_values)
								) {
									echo 'selected';
								}
							?>
							>
								<?= $filter_item; ?>
							</option>
						<?php endforeach; ?>
						
					</datalist>
				</div>
				
			<?php
				// numeric
				elseif ($filter_type === 'numeric') :
			?>
				
				<div class="<?= $filter -> class['prefixname'] . $filter_name . ' ' . $filter -> class['prefixtype'] . $filter_type; ?>">
					<input
						name="data[filter][<?= $filter_name; ?>][0]"
						value="<?= (objectIs($filter_values)) ? $filter_values[0] : ''; ?>"
						class="<?= $filter -> class['item']; ?>"
					>
					-
					<input
						name="data[filter][<?= $filter_name; ?>][1]"
						value="<?= (objectIs($filter_values)) ? $filter_values[1] : ''; ?>"
						class="<?= $filter -> class['item']; ?>"
					>
				</div>
				
			<?php
				// range
				elseif (
					$filter_type === 'range' ||
					$filter_type === 'range_bootstrap' ||
					$filter_type === 'range_jqueryui'
				) :
					
					sort($filter -> data[$filter_name], SORT_NUMERIC);
					
					$filter_range_min = reset($filter -> data[$filter_name]);
					$filter_range_max = end($filter -> data[$filter_name]);
					
					if (!empty($filter_values)) {
						$filter_values = datasplit($filter_values[0], '_');
						if ($filter_values[0] < $filter_range_min) {
							$filter_values[0] = $filter_range_min;
						}
					}
					//print_r($filter_values);
					
					//print_r($template -> libraries);
			?>
				
				<div class="<?= $filter -> class['prefixname'] . $filter_name . ' ' . $filter -> class['prefixtype'] . $filter_type; ?>">
					
					<?php
						if (
							$filter_type === 'range_bootstrap' &&
							in('libraries', 'bootstrapslider')
						) :
					?>
						
						<script>
							$(function() {
								$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_amount")
									.bootstrapSlider({
										min: <?= $filter_range_min; ?>,
										max: <?= $filter_range_max; ?>,
										value: [<?= $filter_range_min . ',' . $filter_range_max; ?>]
									})
									.on("slide", function(slideEvt) {
										$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_min").text(slideEvt.value[0]);
										$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_max").text(slideEvt.value[1]);
									})
									.on("slideStop", function(slideEvt) {
										$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_amount").val(slideEvt.value[0] + "_" + slideEvt.value[1]);
									});
							});
						</script>
						
						<span id="<?= $filter -> class['prefixid'] . $filter_name; ?>_min"><?= $filter_range_min; ?></span>
						<input
							id="<?= $filter -> class['prefixid'] . $filter_name; ?>_amount"
							type="text"
							name="data[filter][<?= $filter_name; ?>]"
							value=""
							class="<?= $filter -> class['item']; ?>"
						>
						<span id="<?= $filter -> class['prefixid'] . $filter_name; ?>_max"><?= $filter_range_max; ?></span>
						
					<?php
						elseif (
							$filter_type === 'range_jqueryui' &&
							in('libraries', 'jqueryui')
						) :
					?>
						
						<script>
							$(function() {
								$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_range").slider({
									range: true,
									min: <?= $filter_range_min; ?>,
									max: <?= $filter_range_max; ?>,
									values: [<?= $filter_range_min . ', ' . $filter_range_max; ?>],
									slide: function(event, ui) {
										$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_amount").val(ui.values[0] + " _ " + ui.values[1]);
									}
								});
								$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_amount").val(
									$("#<?= $filter -> class['prefixid'] . $filter_name; ?>_range").slider("values", 0) + " _ " + $("#<?= $filter -> class['prefixid'] . $filter_name; ?>_range").slider("values", 1)
								);
							});
						</script>
						
						<label for="<?= $filter -> class['prefixid'] . $filter_name; ?>_amount"></label>
						<input
							type="text"
							id="<?= $filter -> class['prefixid'] . $filter_name; ?>_amount"
							name="data[filter][<?= $filter_name; ?>]"
							class="<?= $filter -> class['item']; ?>"
							readonly
						>
						<div id="<?= $filter -> class['prefixid'] . $filter_name; ?>_range"></div>
						
					<?php else : ?>
						
						<div id="<?= $filter -> class['prefixid'] . $filter_name; ?>_range">
							<span id="<?= $filter -> class['prefixid'] . $filter_name; ?>_min"><?= (objectIs($filter_values)) ? $filter_values[0] : $filter_range_min; ?></span>
							-
							<span id="<?= $filter -> class['prefixid'] . $filter_name; ?>_max"><?= (objectIs($filter_values)) ? $filter_values[1] : $filter_range_max; ?></span>
						</div>
						
						<div id="<?= $filter -> class['prefixid'] . $filter_name; ?>_amount">
							<input
								name="data[filter][<?= $filter_name; ?>][0]"
								value="<?= (objectIs($filter_values)) ? $filter_values[0] : $filter_range_min; ?>"
								type="range"
								min="<?= $filter_range_min; ?>"
								max="<?= $filter_range_max; ?>"
								class="<?= $filter -> class['item']; ?>"
								onchange="document.getElementById('<?= $filter -> class['prefixid'] . $filter_name; ?>_min').innerHTML = this.value;"
							>
							<input
								name="data[filter][<?= $filter_name; ?>][1]"
								value="<?= (objectIs($filter_values)) ? $filter_values[1] : $filter_range_max; ?>"
								type="range"
								min="<?= $filter_range_min; ?>"
								max="<?= $filter_range_max; ?>"
								class="<?= $filter -> class['item']; ?>"
								onchange="document.getElementById('<?= $filter -> class['prefixid'] . $filter_name; ?>_max').innerHTML = this.value;"
							>
						</div>
						
					<?php endif; ?>
					
				</div>
				
			<?php
				// another...
				else :
			?>
				
				<div class="<?= $filter -> class['prefixname'] . $filter_name . ' ' . $filter -> class['prefixtype'] . $filter_type; ?>">
					
					<select
						id="<?= $filter -> class['prefixid'] . $filter_name; ?>"
						name="data[filter][<?= $filter_name; ?>]"
						class="<?= $filter -> class['item']; ?>"
						<?php if ($filter_type === 'multiple') : ?>multiple<?php endif; ?>
					>
						
						<option value=""></option>
						
						<?php foreach ($filter_current as $filter_key => $filter_item) : ?>
							<option
								value="<?= $filter_key; ?>"
								<?php
									if (
										objectIs($filter_values) &&
										in_array($filter_key, $filter_values)
									) {
										echo 'selected';
									}
								?>
							>
								<?= $filter_item; ?>
							</option>
						<?php endforeach; ?>
						
					</select>
					
				</div>
				
			<?php endif; ?>
			
			
			<?php if (!empty($filter -> options['wrapper'])) : ?>
				</div>
			<?php endif; ?>
			
		<?php endforeach; ?>
		
		<?php
			
			if (!empty($filter -> options['items'])) :
				
				$items = dataParse($filter -> options['items']);
				
				if (!objectIs($items)) {
					$items = [
						'min' => $items,
						'current' => null
					];
				} else {
					$items = [
						'min' => $items[0],
						'max' => $items[1],
						'multiply' => $items[2],
						'all' => !empty($items[3]) ? true : null,
						'current' => null
					];
				}
				
				if (!empty($items['max'])) :
					
		?>
			<?php if (!empty($filter -> options['labels'])) : ?>
				<label for="<?= $filter -> class['prefixid'] . 'items'; ?>" class="<?= $filter -> class['label'] . ' items ' . $filter -> class['prefixlabel'] . 'items'; ?>">
					<?= lang('filter:items') ? lang('filter:items') : null; ?>
				</label>
			<?php endif; ?>
			<div class="<?= $filter -> class['prefixname'] . 'items ' . $filter -> class['prefixtype'] . 'items'; ?>">
				<select name="data[items]" class="<?= $filter -> class['item']; ?>">
					<option value=""></option>
					<?php
						for ($i = $items['min']; $i <= $items['max']; $i++) :
						if (
							isset($items['multiply']) &&
							is_numeric($items['multiply'])
						) {
							$items['current'] = $i * (int) $items['multiply'];
						} else {
							$items['current'] = $i;
						}
					?>
						<option
							value="<?= $items['current']; ?>"
							<?php
								if ($items['current'] == $filter -> items) {
									echo 'selected';
								}
							?>
						>
							<?= $items['current']; ?>
						</option>
					<?php
						endfor;
						unset($i);
					?>
					<?php if (!empty($items['all'])) : ?>
						<option value="all" <?php if ($filter -> items === 'all') { echo 'selected'; } ?>>
							<?= lang('action:all'); ?>
						</option>
					<?php endif; ?>
				</select>
			</div>
		<?php
				endif;
				unset($items);
			endif;
		?>
	
	<?php endif; ?>
	
	<div class="<?= $filter -> class['buttons']; ?>">
		<?php if (!empty($filter -> options['reset'])) : ?>
			<button type="submit" class="<?= $filter -> class['reset']; ?>" name="data[reset]" value="1">
				<?= lang('action:clear'); ?>
			</button>
		<?php endif; ?>
		
		<button type="submit" class="<?= $filter -> class['submit']; ?>">
			<?= lang('action:ok'); ?>
		</button>
	</div>
	
</form>