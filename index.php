<?php
	/*
Необходимо указать названия для исходной папки, папки с результатами, 
и  для папки, куда будут перемещены обработанные файлы. Сначала получаем список
файлов. Далее в цикле обрабатываем их, из файла создается дерево каталогов, с 
временными файлами, и файл перемещается, чтобы не был обработан другим скриптом
повторно, далее вызывается метод, обрабатывающий дерево файлов.
Временная папка создается из имени исходного файла
Файл результат создается из имени исходного файла.
Файл add_price генерирует 5 000 000 записей
	*/

$start = microtime(true);
	// исходная папка с файлами
$prices = 'prices';
	// папка с результатами
$result_dir = 'result_dir';
	// папка, куда перемещаются обработанные прайсы
$complete_price_dir = 'complete_price_dir';

require_once "PriceHandler.php";
	// чтобы запустить проверку, необходимо создать объект класса
	// передать 3 параметра
	// папка с исходными прайсами, папка с результатами, папка, куда будут перенесены исходные прайсы 
$qwe = new PriceHandler($prices, $result_dir, $complete_price_dir);

if(count($price_list = $qwe->get_files_list()) == 0){
	exit('Нет файлов в исходной папке');
}

for($i=0; $i<count($price_list); $i++){

		// $file = fopen($price_list[$i], "rb");
	$file = fopen($price_list[$i], "rb");
		if(!flock($file, LOCK_EX | LOCK_NB)){
			echo 'Файл заблокирован'.PHP_EOL;
			continue;
		}
			// создание дерева
		echo 'Файл '.$price_list[$i].PHP_EOL;
		echo PHP_EOL.'Создание дерева: '.round(microtime(true) - $start, 2).' сек.'.PHP_EOL;
		$qwe->create_tree($price_list[$i], $file);
		
			// обработка дерева
		
		echo 'Файл '.$price_list[$i].PHP_EOL;
		echo PHP_EOL.'Обработка дерева: '.round(microtime(true) - $start, 2).' сек.'.PHP_EOL;
		$qwe->create_processed_price();
}
