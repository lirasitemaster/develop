<?php defined('isCMS') or die; ?>

<div class="main-side__block-item main-side__block-item-weather">
	
	<div class="main-side__block-item-weather-t">
		<?= $data['temperature']; ?><span>C</span>
	</div>
	
	<div class="main-side__block-item-weather-d">
		<span>
			<?= $data['description']; ?>
		</span>
		<span>
			влажность: <?= $data['humidity']; ?>%
		</span>
		<span>
			ветер: <?= $data['wind']; ?> м/с <?= $data['direction']; ?>
		</span>
		<span>
			по ощущениям <?= $data['feels']; ?> <i class="fa fa-circle" aria-hidden="true"></i> ночью <?= $data['night']['temperature']; ?>
		</span>
	</div>
	
</div>

<ul class="main-side__block-item-daily">
	<?php foreach ($data['daily'] as $item) : ?>
		<li class="main-side__block-item-daily-day">
			
			<div class="main-side__block-item-daily-t">
				<?= !empty($item['day']['temperature']) ? $item['day']['temperature'] : $item['morning']['temperature']; ?><span> C</span>
			</div>
			
			<div class="main-side__block-item-daily-w">
				<?= lang('datetime:W:' . $item['number']); ?>
			</div>
			
		</li>
	<?php
		endforeach;
		unset($item);
	?>
</ul>

<?php if (!empty($module -> settings['copyright'])) : ?>
<small class="main-side__block-item-weather-c">
	По данным <a href="http://openweathermap.com/" title="Open Weather Map" target="_blank">Open Weather Map</a>
</small>
<?php endif; ?>