<?php

$start = microtime(true);
require_once "PriceHandler.php";
	// чтобы запустить проверку, необходимо создать объект класса
	// передать 3 параметра
	// папка с исходными прайсами, папка с результатами, папка, куда будут перенесены исходные прайсы 
$qwe = new PriceHandler('prices', 'result_dir', 'complete_folder');

$qwe1 = $qwe->get_files_list();
echo PHP_EOL.'Получение списка: '.round(microtime(true) - $start, 6).' сек.'.PHP_EOL;
		
print_r( memory_get_usage() );

	// создание дерева
$qwe->create_tree('prices/file0.csv');
echo PHP_EOL.'Создание дерева: '.round(microtime(true) - $start, 2).' сек.'.PHP_EOL;
print_r( memory_get_usage() );

	// обработка дерева
$qwe->create_processed_price();
echo PHP_EOL.'Обработка дерева: '.round(microtime(true) - $start, 2).' сек.'.PHP_EOL;
print_r( memory_get_usage() );


function qwe($elem){
	echo '<pre>';
	print_r($elem);
	echo '</pre>';
}

function asd($elem){
	echo '<pre>';
	var_dump($elem);
	echo '</pre>';
}
echo PHP_EOL.'Время выполнения скрипта: '.round(microtime(true) - $start, 2).' сек.'.PHP_EOL;