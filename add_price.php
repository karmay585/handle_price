<?php 

if(!is_dir('prices')) mkdir('prices', 0770);

$auto_models = ['Nissan', 'Audi', 'BMW', 'Chevrolet', 'Toyota', 'Renaut', 'KIA', 'Mazda', 'Alfa Romeo', 'Mersedes', 'Saab', 'Skoda'];
$article_letter = ['A', 'B', 'C', 'D', 'E', 'F' ];

	// Генерируем 10 файлов
for($k=0; $k<10; $k++){
	$file = "prices/file{$k}.csv";
		// если не существует, то создаем
	fclose(fopen($file, "a+b"));
		// открываем фал для записи
	$f = fopen($file, "a+b");

	for($i=0; $i <=5000000; $i++){
		$str_array = [$auto_models[rand(0, 11)], $article_letter[rand(0,5)].rand(1000000, 2000000), rand(1, 100),  rand(100, 500).','.rand(0, 99) ];
		fputcsv($f, $str_array, ';');
	}
	fclose($f);
}