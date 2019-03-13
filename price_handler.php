<?php

	// имя временной папки
$folder = 'mytmp';
	// имя конечной папки
$last_folder = 'processed_prices';
	
if(!is_dir($last_folder)){
	mkdir($last_folder, 0777) or die('Не удалось создать конечную папку');
}
	// конечный файл
$last_file = mktime().'_'.rand(0, 10).'processed_price.csv';

if(!is_file($last_file)){
	fclose(fopen($last_folder.'/'.$last_file, "ab")) or die('Не удалось создать конечный файл');
}
	// если временная папка не существует, создать ее
if(!is_dir($folder)){
	mkdir($folder, 0770) or die('Не удалось создать временный каталог'); 
}
	// получаем список все прайс листов
$price_list = glob('prices/*.csv', GLOB_NOSORT);

echo PHP_EOL.'Память в начале: ';
print_r( memory_get_usage() );

	// открываем массив прайс листов
$file = fopen($price_list[0], 'rb');	

		// проходим по строкам из открытого файла
	foreach(get_str($file) as $str){

		$vendor_prefix = substr($str[1], 0, 1);
		$path = $folder.'/'.$str[0];
			// создаем папки по группам
		if(!is_dir($path)){
			mkdir($path, 0770, true);
		}
			// записываем временные файлы
		$f = fopen($path.'/'.$vendor_prefix.'.csv', 'ab') or die('Не удалось создать файл');
		fputcsv($f, $str);
		fclose($f);	
	}
	
	// закрываем прайс лист
fclose($file);

exit();
	// получаем список всех файлов
$tmp_file_list = glob($folder.'/*/*.csv');

// print_r($tmp_file_list);
// echo PHP_EOL;

	// получаем временный файл
foreach(get_file($tmp_file_list) as $tmp_file){
		
		// открываем временный файл
	$f_tmp = fopen($tmp_file, "rb");

		// получаем строку из временного файла
	foreach(get_str($f_tmp) as $str){
			// записываем строку в массив
		$data[] = $str;

			// проходим по массиву ищем совпадения марки и артикула
		for($i=0; $i<count($data)-1; $i++){
				// если находим, то сравниваем цену,
			if( ($str[0] === $data[$i][0]) && ($str[1] ===  $data[$i][1]) ){
				// echo "есть совпадения".PHP_EOL;
				if($data[$i][3] > $str[3]){
					$data[$i][3] = $str[3];
					
				}
				array_pop($data);
				break;				
			}
		}		
	}

	$f = fopen($last_folder.'/'.$last_file, "a+b");

	$leng = count($data);
		foreach(get_elem($data) as $data_str){
			if(is_array($data_str)){
				fputcsv($f, $data_str);
			}
		}
	unset($data);
	fclose($f);


		// закрываем временный файл
	fclose($f_tmp);
}

	// возвращаем строку из прайс листа
function get_str($file){
	for($i=0; $i<1000000; $i++){
		$str = fgetcsv($file, 100);
		yield $str;
	}
}

	// возвращает строку из файла
function get_elem($arr){
	for($j=0; $j<count($arr); $j++){
		yield $arr[$j];
	}
}
	// возвращает файл
function get_file($file_list){
	for($k=0; $k<count($file_list); $k++){
		yield $file_list[$k];
	}
}


