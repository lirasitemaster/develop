<?php defined('isENGINE') or die; ?>

<thead>
	<tr>
		<?php if (!empty($buttons['editintable']) && $buttons['editintable'] !== 'after') : ?>
			<td class="<?= $class['nosort'] . ' ' . $class['noprint']; ?>">
			</td>
		<?php
			endif;
			foreach ($base as $item) :
		?>
		<th>
			<?= !empty($labels['base'][$item]) ? $labels['base'][$item] : $item; ?>
		</th>
		<?php
			endforeach;
			unset($item);
			if (!empty($module -> settings['data'])) :
				if (objectIs($map)) :
					foreach ($map as $i) :
		?>
						<th>
							<?= !empty($labels['base']['data']) ? $labels['base']['data'] : 'data'; ?>
							<br>
							<?= !empty($labels['data'][$i]) ? $labels['data'][$i] : $i; ?>
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
			<td class="<?= $class['nosort'] . ' ' . $class['noprint']; ?>">
			</td>
		<?php endif; ?>
	</tr>
</thead>
