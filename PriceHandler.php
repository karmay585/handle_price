<?php

class PriceHandler
{	
	private $folder;
	private $tmp_folder;
	private $result_dir;
	private $price_name;
	private $complete_price_dir;


	public function __construct($dir, $result_dir, $complete_dir)
	{
		$this->folder = $dir;
		$this->result_dir = $result_dir;
		$this->complete_price_dir = $complete_dir;
	}
		/*
			- метод возвращает массив прайс-листов из папки
		*/
	public function get_files_list(){
		$files_list_arr = glob( $this->folder.'/*.csv', GLOB_NOSORT);
		return $files_list_arr;
	}
		/*
		* метод сбрасывает на диск буфер временных файлов
		вид буфера $buffer['марка']['префикс']['строки']
		*/
	private function clear_buffer($buffer){
		foreach($buffer as $folder => $file){
			$path = $this->tmp_folder.'/'.$folder;
			if(!is_dir($path)){
				mkdir($path, 0770, true);
			}
			$vendor_prefix = key($file);

			$f_op = fopen($path.'/'.$vendor_prefix.'.csv', 'a+b');
				foreach($file[$vendor_prefix] as $string){			
					fputcsv($f_op, $string, ';');
				}
			fclose($f_op);
		}
	}
		/*
		- мтод создает дерево файлов из прайс листа
		*/
	public function create_tree($price)
	{
			// открываем дескриптор файла
		$file = fopen($price, "rb");
		flock($file, LOCK_EX);
			// получаем назване прайс-листа, который взят в обработку
		$this->price_name = basename($price, '.csv');
			// создаем временную папку для этого прайса
		$this->tmp_folder = $this->price_name.'_tmp';
		
		$buffer = [];
			// проходим построчно по файлу
		foreach($this->get_str($file) as $file_str){

			if(!isset($count)) $count=0;
				// создаем префикс артикула
			$vendor_prefix = substr($file_str[1], 0, 5);
				// путь к создаваемым файлам
			$path = $this->tmp_folder.'/'.$file_str[0];

				// создаем массив
			$buffer[$file_str[0]][$vendor_prefix][] = $file_str;
			$count++;


			if($count > 10){
				$this->clear_buffer($buffer);
				$buffer = [];
			}	
		}
		fclose($file);
	}

		/*
			метод обрабатывает временные файлы
		*/
	public function create_processed_price()
	{
		if(!is_dir($this->result_dir)){
			mkdir($this->result_dir, 0770);
		}

		foreach($this->get_file( $this->get_tmp_file_list() ) as $tmp_file){

			$f_tmp = fopen($tmp_file, "rb");

			while( ($str = fgetcsv($f_tmp, 100, ';')) !== false ){
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
			fclose($f_tmp);
			unlink($tmp_file);
			
			$f = fopen($this->result_dir.'/'.$this->price_name.'_complete', "a+b");

			foreach($data as $data_str){
				if(is_array($data_str)){
					fputcsv($f, $data_str, ';');
				}
			}
			unset($data);
			fclose($f);
		}
		//rmdir($this->tmp_folder);
		
	}

		/*
		- метод получает список временных прайс-файлов 
		*/
	public function get_tmp_file_list()
	{
		return glob($this->tmp_folder.'/*/*.csv', GLOB_NOSORT);
	}


		/*
		- генератор возвращает файл из списка временных файлов
		*/
	private function get_file($file_arr)
	{
		for($k=0; $k<count($file_arr); $k++){
			yield $file_arr[$k];
		}
		unset($k);
	}
		/*
			- генератор, передает по 1 строке из открытого файла
		*/
	private function get_str($f)
	{
		while( ($str=fgetcsv($f, 100, ';'))!== false ){
			yield $str;
		}		
	}
}