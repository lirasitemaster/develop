<?php defined('isENGINE') or die;

// у магазина есть:
// - витрина 'showroom'
// - корзина покупок 'cart'
// - список желаний 'wishlist/wish'
// - сравнение 'compare'

//$s = $shop;
//$s = objectProcess('cart:shop');

//$s = $_COOKIE;
//$s = preg_replace(['/(\r\n|\r|\n){2}/ui', '/Array\s+\(/ui', '/    /ui', '/ \)/'], ['$1', 'Array(', ' ', ')'], print_r($s, 1));
//echo '<pre>' . $s . '</pre>';

class Shop {
	
	public $process = null;
	public $order = null;
	public $settings = null;
	public $cart = null;
	public $prices = null;
	
	function __construct($name = null, $refresh = null) {
		
		$this -> process = objectProcess('shop:cart');
		$this -> order = [];
		
		$this -> prices = [
			'total' => null
		];
		
		$this -> settings = dbUse(
			'shop' . (!empty($name) ? ':' . $name : null),
			'select',
			['return' => 'name:data']
		);
		
		if (objectIs($this -> settings)) {
			$this -> settings = array_shift($this -> settings);
		} else {
			$this -> settings = null;
		}
		
		$cookie = cookie('cart', true);
		$this -> cart = !empty($cookie) ? iniPrepareJson($cookie, true) : null;
		unset($cookie);
		
		// теперь при создании экземпляра объекта можно сразу же делать обновление корзины
		
		if (!empty($refresh)) {
			$this -> refresh();
		}
		
	}
	
	public function read ($content, $cart = null) {
		
		if (empty($cart)) {
			$cart = $this -> cart;
		}
		
		if (objectIs($cart)) {
			
			if (defined('CORE_CONTENT') && CORE_CONTENT && !class_exists('Content')) {
				init('class', 'content');
			}
			
			$catalog = new Content([null, $content, 'all', null]);
			$catalog -> settings();
			$catalog -> read();
			$catalog = $catalog -> data;
			
			if (objectIs($catalog)) {
				
				foreach ($catalog as $key => $item) {
					
					$sku = $item['data'][ $this -> settings['sku'] ];
					$price = $item['data'][ $this -> settings['price'] ];
					
					if (array_key_exists($sku, $cart)) {
						if ($cart[$sku] > 0) {
							$this -> order[$key] = $item;
							$this -> prices['total'] += $price * $cart[$sku];
						} else {
							unset(
								$this -> order[$key],
								$cart[$sku]
							);
						}
					}
					
				}
				unset($key, $item, $sku, $price);
				
			}
			
			unset($catalog);
			
		}
		
		unset($cart, $content);
		
	}
	
	public function refresh () {
		
		// иногда происходит сбой и количество заказа становится отрицательным
		// чтобы не путать людей, в таких случаях мы просто обновляем указанную позицию
		// однако не всегда обновление можно записать в куки, потому что страница уже загружена
		// тогда мы обновляем только массив полученных кук
		// а при выводе и подсчетах отрицательные заказы все равно считаться не будут
		// для этого в процессы и модули внесены соответствующие изменения
		
		if (objectIs($this -> cart)) {
			
			$change = null;
			
			foreach ($this -> cart as $key => $item) {
				if ($item <= 0) {
					unset($this -> cart[$key]);
					$change = true;
				}
			}
			unset($key, $item);
			
			if (!empty($change)) {
				$cart = objectIs($this -> cart) ? iniPrepareArray($this -> cart) : null;
				if (headers_sent()) {
					$_COOKIE['cart'] = $cart;
				} else {
					cookie('cart', $cart);
				}
				unset($cart);
			}
			
			unset($change);
			
		}
		
	}
	
	public function change ($sku, $num, $change = null) {
		
		//return $this -> process['action'] . $this -> process['string'] . '&source[change]=' . ($change ? 'true' : null) . '&data[' . $sku . ']=' . $num;
		
		return $this -> process['action'] . '?' . http_build_query(
			array_merge(
				$this -> process['array'],
				[
					'source' => [
						'change' => $change ? 'true' : null
					],
					'data' => [
						$sku => $num
					]
				]
			)
		);
		
	}
	
}

?>