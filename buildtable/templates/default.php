<?php defined('isCMS') or die; ?>

<table class="table_<?= $module -> settings -> name; ?>">

<?php foreach ($module -> data as $key => &$item) : ?>
	
	<tr<?= ($key <= $module -> settings -> header) ? ' class="table_' . $module -> settings -> name . '__title"' : ''; ?>>
		
		<?php for ($col = 0, $span = 1; $col <= $module -> settings -> row; $col++) : ?>
			
			<?php
				if (!$item[$col]) :
					
					$span++;
					
				elseif ($item[$col] !== 'xspan') :
					
					$v = $key + 1;
					$xspan = 1;
					while ($v <= $module -> settings -> cols && !$module -> data[$v][$col]) {
						$i = 0;
						foreach ($module -> data[$v] as $nextkey => $nextitem) {
							if ($nextkey !== $module -> settings -> titlerow && $nextitem) {
								$i++;
							}
						}
						if ($i) {
							$module -> data[$v][$col] = 'xspan';
							$xspan++;
						}
						$v++;
					}
					
					$h = $col + 1;
					while ($h <= $module -> settings -> row && !$item[$h]) {
						$span++;
						$h++;
					}
			?>
			<td<?=
				($span > 1 && $key !== $module -> settings -> header) ? ' colspan="' . $span . '"' : '';
			?><?=
				($xspan > 1 && $key !== $module -> settings -> header) ? ' rowspan="' . $xspan . '"' : '';
			?><?=
				($span === $module -> settings -> row && $key !== $module -> settings -> header) ? ' class="table_' . $module -> settings -> name . '__section"' : '';
			?>>
				<?= $item[$col]; ?>
			</td>
			<?php endif; ?>
			
		<?php endfor; ?>
		
	</tr>
	
<?php
	endforeach;
	unset($key, $item, $col, $span, $xspan, $v, $i, $h, $nextkey, $nextitem);
?>

</table>