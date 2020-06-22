<?php defined('isENGINE') or die;

/*

ТЕПЕРЬ модуль поддерживает full + seo + default в caption
А ТАКЖЕ : контент (статьи, например) + его можно вызывать через в модуль вывода контента
И ЕЩЕ : поддержка lazyload
ВАЖНОЕ ИЗМЕНЕНИЕ : теперь для 'captions' и 'content' нельзя указывать расширение - только имя файла

(для lazy есть одно но - нельзя грузить картинки в бэкграунде) решение на замену:
	.media_mainslider__image,
	.media_slider__image,
	.media_gallery__image {
		height: 100%;
		margin-left: 50%;
		transform: translateX(-50%);
		width: auto;
	}

Еще модуль вывода рекламных предложений и сообщений:
	- чтобы отображал на главной
	- чтобы отображал на страницах: указать страницы
	- записывать в куки
	- сбрасывать куки через: задать промежуток времени


Модуль слайдера и галереи - совмещенный

Что он должен делать:

1. слайдер

Функции слайдера, согласно 'http://kenwheeler.github.io/slick/':

	- центральный слайд
	- точки
	- пиктограммы
	- автопрокрутка
	- одновременный вывод нескольких картинок
	- подписи на центральном слайде
	- подписи на пиктограммах

запуск:

<html>
  <head>
  <title>My Now Amazing Webpage</title>
  <link rel="stylesheet" type="text/css" href="slick/slick.css"/>
  <link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
  </head>
  <body>

  <div class="your-class">
    <div>your content</div>
    <div>your content</div>
    <div>your content</div>
  </div>

  <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <script type="text/javascript" src="slick/slick.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      $('.your-class').slick({
        setting-name: setting-value
      });
    });
  </script>

  </body>
</html>

2. галерея

Галерея, совмещенная со слайдером, чтобы по клику на слайд, открывалось окно

Функции галереи, согласно 'http://fancyapps.com/fancybox/3/':

	- галерея
	- стиль
	- элементы:
		запустить слайдшоу
		развернуть на полный экран
		увеличить
		показать значки
		поделиться
		скачать
	- элементы окна
		навигация
		счетчик
		значки вывести
	- эффекты переходов при смене кадра
		фейд
		слайд
		поворот
		циркуляция
		слайд с уменьшением/увеличением
		зум ин/аут
		нет
	- те же эффекты, но при запуске

	- видео
	- модальные окна + эффекты
	
запуск:

<!-- 1. Add latest jQuery and fancybox files -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<!-- 2. Create links -->
<a data-fancybox="gallery" href="big_1.jpg"><img src="small_1.jpg"></a>
<a data-fancybox="gallery" href="big_2.jpg"><img src="small_2.jpg"></a>

ИЛИ

Initialize with JavaScript
Select your elements with a jQuery selector (you can use any valid selector) and call the fancybox method:

$('[data-fancybox="gallery"]').fancybox({
	// Options will go here
});

Info Sometimes you might need to bind fancybox to dynamically added elements. Use selector option to attach click event listener for elements that exist now or in the future. All selected items will be automatically grouped in the gallery. Example:

$().fancybox({
    selector : '.imglist a:visible'
});

Video

<a data-fancybox href="https://www.youtube.com/watch?v=_sI_Ps7JSEk">
    YouTube video
</a>

<a data-fancybox href="https://vimeo.com/191947042">
    Vimeo video
</a>

<a data-fancybox data-width="640" data-height="360" href="video.mp4">
    Direct link to MP4 video
</a>

<a data-fancybox href="#myVideo">
    HTML5 video element
</a>

<video width="640" height="320" controls id="myVideo" style="display:none;">
    <source src="https://www.html5rocks.com/en/tutorials/video/basics/Chrome_ImF.mp4" type="video/mp4">
    <source src="https://www.html5rocks.com/en/tutorials/video/basics/Chrome_ImF.webm" type="video/webm">
    <source src="https://www.html5rocks.com/en/tutorials/video/basics/Chrome_ImF.ogv" type="video/ogg">
    Your browser doesn't support HTML5 video tag.
</video>

<a data-fancybox href="https://www.youtube.com/watch?v=_sI_Ps7JSEk&amp;autoplay=1&amp;rel=0&amp;controls=0&amp;showinfo=0">
    YouTube video - hide controls and info
</a>

<a data-fancybox href="https://vimeo.com/191947042?color=f00">
    Vimeo video - custom color
</a>

Iframe

<a data-fancybox data-type="iframe" data-src="http://codepen.io/fancyapps/full/jyEGGG/" href="javascript:;">
	Webpage
</a>

<a data-fancybox data-type="iframe" data-src="https://mozilla.github.io/pdf.js/web/viewer.html" href="javascript:;">
    Sample PDF file 
</a>

Inline

<div style="display: none;" id="hidden-content">
	<h2>Hello</h2>
	<p>You are awesome.</p>
</div>

<a data-fancybox data-src="#hidden-content" href="javascript:;">
	Trigger the fancybox
</a>

Ajax

<a data-fancybox data-type="ajax" data-src="my_page.com/path/to/ajax/" href="javascript:;">
	AJAX content
</a>

<a data-fancybox data-type="ajax" data-src="my_page.com/path/to/ajax/" data-filter="#two" href="javascript:;">
	AJAX content
</a>

*/

//print_r($module);

$name = $module -> param;
$sets = $module -> settings;
$id = set($module -> settings['id'], true);

if (empty($sets['folder'])) {
	if (!empty($module -> this)) {
		
		$path = [
			PATH_LOCAL . datapath($module -> this, false, 'parse'),
			null,
			null,
			null
		];
		
		if (file_exists($path[0]) && is_dir($path[0])) {
			$sets['folder'] = $module -> this;
		} else {
			
			$path[1] = strrpos($path[0], DS);
			$path[0] = substr($path[0], 0, $path[1]) . '.' . substr($path[0], $path[1] + 1);
			$path[1] = strrpos($path[0], DS) + 1;
			$path[2] = substr($path[0], 0, $path[1]);
			$path[3] = substr($path[0], $path[1]);
			$path[1] = strlen(PATH_LOCAL);
			
			if (file_exists($path[0]) && is_file($path[0])) {
				$sets['folder'] = substr($path[2], $path[1], -1);
				$sets['list'] = [$path[3]];
			}
			
		}
		
		unset($path);
		
	} elseif (file_exists(PATH_LOCAL . 'media' . DS . $template -> page['name'])) {
		$sets['folder'] = 'media.' . $template -> page['name'];
	} else {
		$sets['folder'] = 'media.' . $name;
	}
}

$init = [
	'path' => PATH_LOCAL . datapath($sets['folder'], false, 'parse') . DS,
	'url' => '/' . URL_LOCAL . datapath($sets['folder'], true, 'parse') . '/',
	'list' => objectIs($sets['list']) ? $sets['list'] : null,
	'captions' => null
];

// подготовка списка файлов

if (empty($init['list'])) {
	$init['list'] = localList($init['path'], [
		'return' => 'files',
		'type' => !empty($sets['type']) ? (is_array($sets['type']) ? $sets['type'] : dataParse($sets['type'])) : ['jpg', 'jpeg', 'png', 'webp'],
		'skip' => !empty($sets['skip']) ? (is_array($sets['skip']) ? $sets['skip'] : dataParse($sets['skip'])) : null
	]);
}

// сортировка

if (objectIs($init['list']) && !empty($sets['sort'])) {
	
	// работа sort:
	// передается строка из двух аргументов, разделенных двоеточием - 'type:flag'
	// type - это тип сортировки
	//   shuffle - перемешать в случайном порядке
	//   desc - в обратном порядке
	//   любое другое значение, в том числе если оно пропущено, сортирует массив в прямом порядке
	// flag - это флаг, определяющий метод
	//   regular - SORT_REGULAR, обычное сравнение элементов
	//   numeric - SORT_NUMERIC, числовое сравнение элементов
	//   string - SORT_LOCALE_STRING, сравнивает элементы как строки с учетом текущей локали
	//   * по-умолчанию сортировка производится с флагом SORT_NATURAL и SORT_FLAG_CASE
	//   * подробнее - см. https://www.php.net/manual/ru/function.sort.php
	
	$sets['sort'] = dataParse($sets['sort']);
	
	if (objectIs($sets['sort'])) {
		
		if (!empty($sets['sort'][1])) {
			
			if ($sets['sort'][1] === 'string') {
				$sets['sort'][1] = SORT_LOCALE_STRING;
			} elseif ($sets['sort'][1] === 'numeric') {
				$sets['sort'][1] = SORT_NUMERIC;
			} else {
				$sets['sort'][1] = SORT_REGULAR;
			}
			
		}
		
		if ($sets['sort'][0] === 'shuffle') {
			shuffle($init['list']);
		} else {
			if ($sets['sort'][0] === 'desc') {
				rsort(
					$init['list'],
					empty($sets['sort'][1]) ? SORT_NATURAL | SORT_FLAG_CASE : $sets['sort'][1]
				);
			} else {
				sort(
					$init['list'],
					empty($sets['sort'][1]) ? SORT_NATURAL | SORT_FLAG_CASE : $sets['sort'][1]
				);
			}
		}
		
	}
	
}

// обрезка, укорачивание или лимитирование

if (objectIs($init['list']) && !empty($sets['limit']) && is_numeric($sets['limit']) && $sets['limit'] > 0) {
	
	// работа limit:
	// массив обрезается до указанного числа элементов
	
	$init['list'] = array_slice($init['list'], 0, $sets['limit']);
	
}

// подготовка заголовков и описаний

$init['captions'] = iniPrepareJson(localFile($init['path'] . 'captions.ini'), true);
objectLang($init['captions']);

// подготовка контента

$init['content'] = iniPrepareJson(localFile($init['path'] . 'content.ini'), true);
objectLang($init['content']);

if (objectIs($init['list'])) {
	
	foreach ($init['list'] as $item) {
		
		$item = substr($item, 0, strripos($item, '.'));
		$file = iniPrepareJson(localFile($init['path'] . $item . '.ini'), true);
		objectLang($file);
		
		// вставка заголовков и описаний
		
		//echo htmlentities(print_r($init['captions'][$item], 1)) . '<br><br>';
		
		if (
			is_string($init['captions'][$item]) &&
			mb_strpos($init['captions'][$item], '{') !== false
		) {
			
			$str = $init['captions'][$item];
			$str = mb_substr($str, mb_strpos($str, '{'));
			$str = mb_substr($str, 0, mb_strpos($str, '}') + 1);
			
			$arr = dataParse($str, false);
			
			//echo print_r($arr, 1) . '<br><br>';
			
			if (array_key_exists('link', $arr)) {
				$arr = '<a' . set($arr['href'], ' href="/' . array_shift($arr['href']) . '/"') . set($arr['class'], ' class="' . objectToString($arr['class']) . '"') . '>' . str_replace('_', ' ', array_shift($arr['link'])) . '</a>';
			}
			
			$init['captions'][$item] = str_replace($str, $arr, $init['captions'][$item]);
			
			unset($str, $arr);
			
		}
		
		//echo print_r($link, 1) . '<br><br>';
		
		if (!empty($sets['captions'][$item])) {
			$init['captions'][$item] = $sets['captions'][$item];
		}
		
		if (empty($init['captions'][$item]) && objectIs($file)) {
			$init['captions'][$item] = reset($file);
		}
		
		if (!objectIs($init['captions'][$item])) {
			$init['captions'][$item] = [
				'full' => $init['captions'][$item],
				'default' => $init['captions'][$item],
				'seo' => $init['captions'][$item]
			];
		} elseif (!empty($init['captions'][$item]['default'])) {
			if (empty($init['captions'][$item]['full'])) {
				$init['captions'][$item]['full'] = $init['captions'][$item]['default'];
			}
			if (!empty($sets['seo']) && empty($init['captions'][$item]['seo'])) {
				$init['captions'][$item]['seo'] = $init['captions'][$item]['default'];
			}
		}
		
		if (!empty($init['captions'][$item]['default'])) {
			$init['captions'][$item]['default'] = clear(
				html_entity_decode($init['captions'][$item]['default']),
				null,
				empty($sets['captionsasis']) ? true : (objectIs($sets['captionsasis']) ? $sets['captionsasis'] : false)
			);
		}
		if (!empty($init['captions'][$item]['full'])) {
			$init['captions'][$item]['full'] = clear(
				html_entity_decode($init['captions'][$item]['full']),
				null,
				empty($sets['captionsasis']) ? true : (objectIs($sets['captionsasis']) ? $sets['captionsasis'] : false)
			);
		}
		if (!empty($init['captions'][$item]['seo'])) {
			$init['captions'][$item]['seo'] = clear(html_entity_decode($init['captions'][$item]['seo']), 'notagsspaced');
		}
		
		// вставка контента
		
		if (empty($sets['contentdisable'])) {
			
			if (!empty($sets['content'][$item])) {
				$init['content'][$item] = $sets['content'][$item];
			}
			
			if (empty($init['content'][$item]) && objectIs($file)) {
				$init['content'][$item] = reset($file);
				$init['content'][$item] = set($init['content'][$item]['content'], true);
			}
			
			if (!empty($init['content'][$item])) {
				$init['content'][$item] = clear(
					html_entity_decode($init['content'][$item]),
					null,
					empty($sets['contentasis']) ? true : (objectIs($sets['contentasis']) ? $sets['contentasis'] : false)
				);
			}
			
		}
		
		unset($file);
		
	}
	
	unset($item);
	
	//echo '<br><br><br><br><br><br><br><br><br><br>';
	//print_r($sets['content']);
	//print_r($init);
	
} else {
	logging('module \'media\' as \'' . $module -> param . '\' on \'' . $template -> page['name'] . '\' page can not find slides in folder \'' . NAME_LOCAL . '.' . $sets['folder'] . '\'', 'module \'media\' can not find slides');
}

require $module -> path . 'process' . DS . 'prepare.php';
require $module -> path . 'process' . DS . 'classes.php';

//print_r($init);
//print_r($sets);

?>