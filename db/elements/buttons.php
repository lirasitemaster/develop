<?php defined('isCMS') or die;
if (objectIs($buttons)) :
?>

<div class="<?= $class['buttons']; ?>">
	<?php if (!empty($buttons['edit'])) : ?>
		<button class="<?= $class['edit']; ?>" type="button">
			<?= $labels['buttons']['edit']; ?>
		</button>
	<?php endif; ?>
	<?php if (!empty($buttons['editinline'])) : ?>
		<button class="<?= $class['editinline']; ?>" type="button">
			<?= $labels['buttons']['editinline']; ?>
		</button>
	<?php endif; ?>
	<button class="<?= $class['save']; ?>" type="button">
		<?= $labels['buttons']['save']; ?>
	</button>
</div>

<?php endif; ?>