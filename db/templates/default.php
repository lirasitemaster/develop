<?php defined('isCMS') or die;
if (objectIs($module -> data)) :
?>

<style>
table th {
	background: #eee;
}
table th,
table td {
	border: 1px solid #ccc;
	padding: 0.25em;
	vertical-align: top;
}
td {
	min-width: 0;
}
td[data-json="1"] {
	min-width: 50vw;
}
th {
	vertical-align: top!important;
}
th:before,
th:after {
	bottom: auto!important;
	top: 0.75em!important;
}
button.dt-button.buttons-enable {
	background: #ff0000!important;
}

.toggle-container {
	list-style: none;
	margin: 20px 0;
	padding: 0;
}
.toggle-vis {
	display: inline-block;
	padding: 5px 10px;
	border: 1px solid;
	cursor: pointer;
}
.toggle-inactive {
	color: #ccc;
	border-color: #ccc;
}
</style>

<?php

$t = !empty($module -> this) ? $module -> this : $module -> param;
$tl = lang('menu:' . $tl);
echo '<div class="' . $class['title'] . '">' . $labels['title'] . ' <span class="' . $class['name'] . '">' . (!empty($tl) ? $tl : $t) . '</span></div>';
unset($t, $tl);

require $module -> elements . 'buttons.php';
require $module -> elements . 'display.php';

?>

<table class="table table-bordered" id="<?= $name; ?>" width="100%" cellspacing="0">

	<?php require $module -> elements . 'thead.php'; ?>
	
	<tbody>
	<?php foreach ($module -> data as $item) : ?>
		<tr data-name="<?= $item['name']; ?>">
			<?php if (!empty($buttons['editintable']) && $buttons['editintable'] !== 'after') : ?>
				<td class="<?= $class['nosort'] . ' ' . $class['noprint']; ?>" data-protect="1">
					<button class="<?= $class['editintable']; ?>" type="button">
						<?= $labels['buttons']['editintable']; ?>
					</button>
				</td>
			<?php
				endif;
				foreach ($item as $k => $i) :
					if (objectIs($i) && $k === 'data') :
						if (!empty($module -> settings['data']) && objectIs($map)) :
							foreach ($map as $ki) :
								$ii = array_key_exists($ki, $i) ? (set($i[$ki]) ? $i[$ki] : null) : null;
			?>
								<td data-name="<?= $ki; ?>"<?= objectIs($ii) ? ' data-json="1"' : null; ?>>
									<?= objectIs($ii) ? iniPrepareArray($ii, true) : $ii; ?>
								</td>
			<?php
							endforeach;
							unset($ki, $ii);
						endif;
					elseif (in_array($k, $base)) :
						if ($k === 'ctime' || $k === 'mtime') :
			?>
							<td data-time="<?= $i; ?>"<?= objectIs($module -> settings['inline']) && !in_array($k, $module -> settings['inline']) ? ' data-protect="1"' : null; ?>>
								<?= datadatetime($i, !empty(lang('datetime:format')) ? lang('datetime:format') : '{yy}.{mm}.{dd} {hour}:{min}:{sec}'); ?>
							</td>
			<?php
						else :
			?>
							<td<?= objectIs($module -> settings['inline']) && !in_array($k, $module -> settings['inline']) ? ' data-protect="1"' : null; ?>>
								<?= objectIs($i) ? iniPrepareArray($i) : $i; ?>
							</td>
			<?php
						endif;
					endif;
				endforeach;
				unset($k, $i);
				if (!empty($buttons['editintable']) && $buttons['editintable'] !== 'before') :
			?>
				<td class="<?= $class['nosort'] . ' ' . $class['noprint']; ?>" data-protect="1">
					<button class="<?= $class['editintable']; ?>" type="button">
						<?= $labels['buttons']['editintable']; ?>
					</button>
				</td>
			<?php
				endif;
			?>
		</tr>
	<?php endforeach; ?>
	<?php unset($item); ?>
	</tbody>
	
	<?php require $module -> elements . 'tfoot.php'; ?>
	
</table>

<?php
	require $module -> elements . 'edit.php';
	require $module -> elements . 'script.php';
endif;
?>