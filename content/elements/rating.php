<?php defined('isENGINE') or die;

//$rating = $content -> ratings[$key]['data'];
//$rating = $content -> ratings[$key];

// вывод формы голосования

if (!empty($rating['sets']['counter'])) :

$form = objectProcess('content:rating');
?>

<form class="articles_rating" method="post" action="<?= $form['action']; ?>">
	
	<?php
		foreach ($form['fields'] as $fi) {
			echo $fi;
		}
		unset($fi, $form);
	?>
	
	<!--
	<input type="hidden" name="hash" value="<?= crypting(time()); ?>">
	<input type="hidden" name="csrf" value="<?= csrf(); ?>">
	-->
	
	<input type="hidden" name="status" value="<?= $module -> param; ?>">
	<input type="hidden" name="data[target]" value="<?= base64_encode('id:' . $item['id'] . ' name:' . $item['name'] . ' parent:' . objectToString($item['parent'], '.')); ?>" readonly>
	
	<input type="hidden" name="data[name]" value="counter" readonly>
	
	<div>
		<div>
			<div>
				Общий рейтинг: <?= $rating['this']['counter']['total']; ?>
			</div>
			<div>
				Средний рейтинг: <?= $rating['this']['counter']['average']; ?>
			</div>
			<div>
				Число голосов: <?= $rating['this']['counter']['count']; ?>
			</div>
		</div>
		<div>
			<?php
				for ($i = $rating['sets']['counter'][0]; $i <= $rating['sets']['counter'][1]; $i++) :
				if ($i !== 0) :
			?>
				<input type="submit" name="data[counter]" value="<?= $i; ?>">
			<?php
				endif;
				endfor;
			?>
		</div>
	</div>
	
</form>

<?php endif; ?>