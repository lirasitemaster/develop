<?php defined('isENGINE') or die;

global $uri;
$parent = $target[1];

?>

<section class="order-cart order__background">
	<div class="container">
		
		<div class="row">
			<div class="col">
				<div class="order__title">
					Корзина покупок
					<small>
						Уважаемые жители! В связи с тем, что ФАКТИЧЕСКИЙ вес обычно ОТЛИЧАЕТСЯ от заказанного, итоговая сумма пересчитывается и может меняться как в большую, так и в меньшую сторону.
					</small>
					<small>
						Стоимость доставки - 50 р. При заказе от 200 р. - доставка бесплатно.
					</small>
					<small>
						Все заказы, созданные после 15:00, будут доставлены на следующий день.
					</small>
				</div>
			</div>
		</div>
		
		<?php if (objectIs($module -> data)) : ?>
		<div class="row">
			<div class="col">

<table class="order-list">
	<tbody>
		<?php
			$i = 0;
			foreach ($module -> data as $item) :
				$data = $item['data'];
				$sku = $data[ $shop -> settings['sku'] ];
				$price = $data[ $shop -> settings['price'] ];
				$chnum = !empty($data[ $shop -> settings['change'] ]) ? $data[ $shop -> settings['change'] ] : 1;
				$i++;
		?>
		<tr class="order-list-row">
			<td class="order-list-item order-list-item__id d-none d-md-table-cell">
				<?= $i; ?>
			</td>
			<td class="order-list-item order-list-item__image">
				<img class="order-list-item__image--src" alt="<?= $data['title']; ?>" src="<?= URL_LOCAL . $parent . '/' . $item['name']; ?>.jpg" />
			</td>
			<td class="order-list-item order-list-item__info">
				<a href="/<?= $parent . '/' . $item['name']; ?>/">
					<?= $data['title']; ?>
				</a>
				<br>
				<span class="order-list-item__info--units">(1 <?= $data['units'] . (!empty($data['innum']) ? ', ' . $data['innum'] : null); ?>)</span>
				<br>
				<span class="order-list-item__info--articul">артикул <?= $data['articul']; ?></span>
			</td>
			<td class="order-list-item order-list-item__control">
				<?php if ($cart[$sku] > $chnum) : ?>
				<a href="<?= $shop -> change($sku, '-' . $chnum); ?>" class="order-list-item__control--button order-list-item__control--dec">&ndash;</a>
				<?php else : ?>
				<span class="order-list-item__control--button order-list-item__control--dec">&ndash;</span>
				<?php endif; ?>
				<span class="order-list-item__control--num d-none d-md-inline-block"><?= $cart[$sku]; ?></span>
				<a href="<?= $shop -> change($sku, $chnum); ?>" class="order-list-item__control--button order-list-item__control--inc">+</a>
			</td>
			<td class="order-list-item order-list-item__price">
				<span class="order-list-item__price--big"><?= $price * $cart[$sku]; ?></span> руб
				<span class="order-list-item__price--detail"><?= $cart[$sku]; ?> х <?= $price; ?> руб</span>
			</td>
			<td class="order-list-item order-list-item__delete">
				<a href="<?= $shop -> change($sku, '0', true); ?>">
					<i class="fa fa-times" aria-hidden="true"></i><span class="d-none d-md-inline"> удалить</span>
				</a>
			</td>
		</tr>
		<?php
			endforeach;
			unset($item, $data, $sku, $price, $i);
		?>
	</tbody>
</table>

			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<div class="order-total">
					Итого: <span class="order-total__price"><?= $prices['total']; ?></span> руб
				</div>
			</div>
			<div class="col order__button">
				<a href="<?= $uri -> url; ?>#order">Перейти к оформлению</a>
			</div>
		</div>
		
		<?php else : ?>
		
		<div class="row">
			<div class="col">
				<div class="order-total">
					Ваша корзина пуста
				</div>
			</div>
			<div class="col order__button">
				<a href="/">Перейти в каталог</a>
			</div>
		</div>
		
		<?php endif; ?>
		
	</div>
</section>

<section class="order order__background" id="order">
	<div class="container">
		
		<?php if (!$complete && objectIs($module -> data)) : ?>
		
		<div class="row">
			<div class="col">
				<div class="order__title">
					Оформление заказа
					<small>
						Внимание! Цены на товар могут меняться в пределах 10%
					</small>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				
				<?php
					module(['form', 'order'], $target);
					//module(['form', 'order'], $t);
				?>

			</div>
		</div>
		
		<?php elseif ($complete) : ?>
		
		<div class="row order-form">
			<div class="col order__button">
				<a href="/">Ваш заказ был оформлен успешно</a>
			</div>
		</div>
		
		<?php endif; ?>
		
	</div>
</section>

<?php
	
//print_r($catalog);
//print_r($cart);

?>