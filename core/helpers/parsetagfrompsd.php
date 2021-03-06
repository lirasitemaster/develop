<?php defined('isENGINE') or die; ?>

<?php

$path = PATH_LOCAL . 'parse' . DS;

$files = localList($path, ['return' => 'files', 'subfolders' => true]);

$arr = [];

//echo $path;
//print_r($files);

foreach ($files as $key => $item) {
	
	$file = file_get_contents($path . $item);
	$file = mb_convert_encoding($file, 'UTF-8', 'ASCII, UTF-8, Windows-1251');
	
	$item = mb_convert_encoding($item, 'UTF-8', 'Windows-1251');
	
	$arr[$key]['path'] = $item;
	$arr[$key]['file'] = mb_substr($item, mb_strrpos($item, '\\') + 1);
	$mask = [
		'<dc\:description>[\r\n\s\w\W]*?<\/dc\:description>',
		'<dc\:subject>[\r\n\s\w\W]*?<\/dc\:subject>'
	];
	preg_match('/' . $mask[0] . '/ui', $file, $arr[$key]['desc']);
	preg_match('/' . $mask[1] . '/ui', $file, $arr[$key]['shop']);
	
	/*$arr[$key]['desc'] = preg_replace('/<rdf\:li.*?>(.*)?<\/rdf\:li>/ui', '$1', $arr[$key]['desc'][0]);*/
	$arr[$key]['desc'] = trim(strip_tags($arr[$key]['desc'][0]));
	$arr[$key]['shop'] = trim(strip_tags($arr[$key]['shop'][0]));
	$arr[$key]['desc'] = preg_replace('/,&#xA;/ui', '|', $arr[$key]['desc']);
	
	//$arr[$key]['desca'] = preg_split('/\|/ui', $arr[$key]['desc']);
	$arr[$key] = array_merge($arr[$key], preg_split('/\|/ui', $arr[$key]['desc']));
	unset($arr[$key]['desc']);
	
	
}

if ($arr[0]['path'] === 'complete.list' || $arr[0]['path'] === 'complete.csv') {
	array_shift($arr);
}
if ($arr[0]['path'] === 'complete.list' || $arr[0]['path'] === 'complete.csv') {
	array_shift($arr);
}

$fp = fopen($path . 'complete.csv', 'w');

foreach ($arr as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);



$out = print_r($arr, 1);

echo $out;

file_put_contents($path . 'complete.list', "\xEF\xBB\xBF" . $out);

//print_r($arr);

//<dc:description>


?>


<?/*
<div class="row">
	
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<img src="<?= URL_LOCAL; ?>media/gallery/mirmexa-kmv-0001.jpg">
		
		<p>
		Первый гипермаркет под брендом «Мир Меха» был открыт в 2010 году в Пятигорске. Всего за полгода работы «Мир Меха» получил признание среди покупателей и завоевал почетный титул лучшего мехового гипермаркета в г. Пятигорске.
		</p>
		<p>
		«Мир Меха» работает с 2010 года, и уже имеет опыт по продаже действительно качественной меховой одежды, постоянный поиск новых направлений, стилей и модных тенденций делают «Мир Меха» одним из лидеров взыскательного и требовательного мехового рынка. Мы по-настоящему увлечены европейскими новинками и тенденциями в области меховой моды - и рады предложить их нашим покупателям.
		</p>
		<p>
		В «Мире Меха» представлены дилеры и представители ведущих фабрик России и Европы. На протяжении долгого времени выпускают и продают оптом и в розницу замечательные изделия, меховые фабрики Пятигорска.
		</p>
		<p>
		В магазине представлено более 30 тысяч наименований верхней одежды: дубленки, шубы, модные жилеты из разнообразного цветного меха и головные уборы. Все изделия представлены в полном модельном и размерном ряде. Коллекции обновляются постоянно.
		</p>
		<p>
		Отличительными особенностями «Мира Меха» является большой ассортимент и широкий спектр оказания услуг и гибкая ценовая политика. Также существуют очень выгодные бонусные и дисконтные программы по скидкам, постоянно проводятся различные рекламные акции, о которых вы также можете узнать на нашем сайте.
		</p>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<p>
		При личном посещении «Мира Меха» в Пятигорске, Вы будете приятно удивлены низким ценам на изделия, широте выбора, глубинами цветов изделий, ассортиментом различных модельных рядов.
		</p>
		<p>
		«Мир Меха» предлагает услуги многопрофильного ателье, в котором вы можете откорректировать любое купленное изделие, обменный пункт валюты, терминал приема кредитных карт, удобные системы кредитования, кафе и бар для приятного провождения времени, отдел парфюма - все это доступно нашим клиентам. Каждой покупательнице в подарок предоставляется профессиональная фотография в мехах на троне снежной королевы.
		</p>
		<p>
		«Мир Меха» не забывает о маленьких модниках и модницах - детская верхняя одежда не уступает взрослой. Сезонные коллекции включают полный перечень детской продукции: шубки из мутона, дубленки, жилеты, безрукавки, кожаные куртки и головные уборы. В «Мире Меха» детям не будет скучно. Они прекрасно проведут здесь время. Яркие машинки для передвижения по торговому центру придутся по душе и мальчикам, и девочкам.
		</p>
		
		<img src="<?= URL_LOCAL; ?>media/gallery/mirmexa-kmv-0002.jpg">
	</div>
	
</div>

*/?>