<?php defined('isCMS') or die; ?>

<?php print_r($module -> settings -> row); ?>

<table class="table_<?= $module -> settings -> name; ?>">

<?php foreach ($module -> data as $key => &$item) : ?>
	
	<tr<?= ($key <= $module -> settings -> header) ? ' class="table_' . $module -> settings -> name . '__title"' : ''; ?>>
		
		<?php for ($col = 0, $span = 1; $col < $module -> settings -> row; $col++) : ?>
			
			<?php
				if ($item[$col]) :
					$span = 1;
					$row = $col + 1;
					while ($row < $module -> settings -> row && !$item[$row]) {
						$span++;
						$row++;
					}
			?>
			<td<?=
				' colspan="' . $span . '"';
			?><?=
				($span === $module -> settings -> row) ? ' class="table_' . $module -> settings -> name . '__section"' : '';
			?>>
				<?= $item[$col]; ?>
			</td>
			<?php elseif ($span === 1) : ?>
			<td></td>
			<?php endif; ?>
			
		<?php endfor; ?>
		
	</tr>
	
<?php
	endforeach;
	unset($key, $item, $col, $span, $row);
?>

</table>