<?php defined('isENGINE') or die;
if (!empty($sets['filtration'])) :
?>

<tfoot>
	<tr>
		<?php if (!empty($buttons['editintable']) && $buttons['editintable'] !== 'after') : ?>
			<td class="<?= $class['noprint']; ?>">
			</td>
		<?php
			endif;
			foreach ($base as $item) :
		?>
		<th>
			<?= $item; ?>
		</th>
		<?php
			endforeach;
			unset($item);
			if (!empty($module -> settings['data'])) :
				if (objectIs($map)) :
					foreach ($map as $i) :
		?>
						<th>
							<?= !empty($labels['data'][$i]) ? $labels['data'][$i] : 'data' . $i; ?>
						</th>
		<?php
					endforeach;
					unset($i);
				else :
		?>
					<th>
						<?= !empty($labels['base']['data']) ? $labels['base']['data'] : 'data'; ?>
					</th>
		<?php
				endif;
			endif;
			if (!empty($buttons['editintable']) && $buttons['editintable'] !== 'before') :
		?>
			<td class="<?= $class['noprint']; ?>">
			</td>
		<?php
			endif;
		?>
	</tr>
</tfoot>

<?php endif; ?>