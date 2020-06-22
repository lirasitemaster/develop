<?php defined('isENGINE') or die;

class htmlElement {

	/*
	*  $a = new htmlElement('div', ['style1', 's2', 's3'], 'new', $data);
	*  создаем экземпляр класса и сразу же при этом выводится объект (тег)
	*  аргументы по-порядку:
	*    элемент (обязательно, остальные аргуметы - нет)
	*    стили (строкой или массивом)
	*    id
	*    данные (массивом в стиле 'название' => 'значение', тогда формируется data-название="значение")
	*    area (по аналогии с данными)
	*
	*  $a -> close();
	*  закрывает объект (тег)
	*
	*  пример вызова:
	*  $data = ['id' => '1', 'name' => 'my', 'target' => '#mod'];
	*  $a = new htmlElement('div', ['style1', 's2', 's3'], 'new', $data);
	*    $b = new htmlElement('p', 'basta');
	*      echo '45635062450598yer!!!';
	*    $b -> close();
	*  $a -> close();
	*  unset($a, $b);
	*  вывод:
	*  <div class="style1 s2 s3" id="new" data-id="1" data-name="my" data-target="#mod"><p class="basta">45635062450598yer!!!</p></div>
	*
	*  Вот такой вот маленький и очень полезный класс!!!
	*  
	*  UPD: теперь можно создавать элементы img и a со ссылками, которые чистятся автоматом, даже если туда подставить гадость, и при этом ведут только внутрь этого сайта
	*/
	
	function add($type = false, $data = false) {
		
		if (empty($type) || empty($data)) {
			return false;
		}
		
		$this -> print .= ' ' . $type . '="';
		
		if (!is_array($data)) {
			$this -> print .= $data;
		} elseif (count($data) == 1) {
			$this -> print .= $data[0];
		} else {
			$this -> print .= array_shift($data);
			foreach ($data as $i) {
				$this -> print .= ' ' . $i;
			}
		}
		
		$this -> print .= '"';
		
		unset($type, $data);
	}
	
	function data($type = false, $data = false) {
		
		if (!$type || !objectIs($data)) {
			return false;
		}
		
		foreach ($data as $k => $i) {
			$this -> print .= ' ' . $type . '-' . $k . '="' . $i . '"';
		}
		
		unset($type, $data);
	}
	
	function link($type = false, $data = false) {
		
		if (!$type || !$data) {
			return false;
		}
		
		$data = trim($data);
		$data = preg_replace('/^https?:/', '', $data);
		$data = preg_replace('/^\/\//', '', $data);
		$data = preg_replace('/^[\w\.]+?\.[\w]+?\//', '/', $data);
		
		if ($type === 'img') {
			$this -> print .= ' src';
		} else {
			$this -> print .= ' href';
		}
		$this -> print .= '="' . clear($data, 'simpleurl') . '"';
		
		unset($data);
	}
	
	function styles($data = false) {
		
		if (!objectIs($data)) {
			return false;
		}
		
		$this -> print .= ' style="';
		
		foreach ($data as $k => $i) {
			$this -> print .= $k . ': ' . $i . '; ';
		}
		
		$this -> print .= '"';
		
		unset($data);
	}
	
	// это - конструктор класса, т.е. функция, которая запускается при создании экземпляра класса
	// как раз то, что нам нужно, чтобы создавать элементы
	function __construct($tag = false, $class = false, $id = false, $data = false, $area = false, $styles = false, $link = false) {
		
		$allowtags = ['section', 'div', 'p', 'span', 'nav', 'ul', 'li', 'i', 'a', 'img'];
		
		if (!$tag || !in_array($tag, $allowtags)) {
			return false;
		}
		
		$this -> tag = $tag;
		
		$this -> print = '<' . $tag; //echo '<' . $tag;
		
		self::link($tag, $link);
		self::add('class', $class);
		self::add('id', $id);
		self::data('data', $data);
		self::data('area', $area);
		self::styles($styles);
		
		$this -> print .= '>';
		
		echo $this -> print;
		
	}
	
	function close() {
		
		if (!$this -> tag) {
			return false;
		}
		
		if ($this -> tag !== 'img') {
			echo '</' . $this -> tag . '>';
		}
		
		$this -> tag = null;
	}
		
}

?>