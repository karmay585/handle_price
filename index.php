<?php

ini_set("error_reporting", E_ALL);
$host = 'localhost';
$user = 'phpmyadmin';
$password = '010203';
$database = 'guests_book';

	// создаем базу данных, если не создана
$link = new MYSQLI($host, $user, $password);
if($link){
	$sql = "CREATE DATABASE IF NOT EXISTS {$database} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
	$link->query($sql);
}
$link->close();
unset($link);

$connect = new MYSQLI($host, $user, $password, $database) or exit('Не удалось соединиться с базой данных '.$database);

	// создаем таблицу, если не создана
$sql = " CREATE TABLE IF NOT EXISTS `{$database}`.`comments` (
	`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`author_name` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
	`author_email` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
 	PRIMARY KEY (`id`)
 	)
	ENGINE InnoDB 
	CHARSET=utf8mb4
	COLLATE utf8mb4_unicode_ci;";

$connect->query($sql);


	// сохранение записи
if(isset($_POST['add_comment'])){
	if(!empty($_POST['name'])){
		$name = htmlentities(trim($_POST['name']));
	}
	if(!empty($_POST['email'])){
		$email = htmlentities(trim($_POST['email']));
	}
	if(!empty($_POST['comment'])){
		$text = htmlentities(trim($_POST['comment']));
	}
}

if(isset($name) AND isset($email) AND isset($text)){
	$sql = "INSERT INTO `comments` (`author_name`, `author_email`, `text`) 
			VALUES ( '$name', '$email', '$text' )";
			
	$connect->query($sql);
	
	unset($name);
	unset($email);
	unset($text);
}
	// удаление записи
if( isset($_POST['del_comment'])) {
	$id = (int)$_POST['comment_id'];
	$sql = "DELETE FROM `comments` WHERE `id`= {$id} ";
	$connect->query($sql);
}

// вывод записей
	// подсчет записей
	$sql = "SELECT COUNT(*) AS count FROM `comments` ";
	$res= $connect->query($sql);
	$count = $res->fetch_assoc();
	// количество записей на странице
$j = 3;
if(isset($_GET['page'])){
	$page = (int)$_GET['page'];
}else{
	$page =0;
}

$page_count = ceil((int)$count['count']/$j);

if( $page< 0){
	$page = 0;
}elseif($page > $page_count){
	$page = $page_count;
}

$page_num = $page*$j;
		// страница
$sql = "SELECT `id`, `author_name`, `author_email`, `text`
		FROM `comments` 
		ORDER BY id DESC 
		LIMIT $page_num, $j
		";
$comments = $connect->query($sql);

?>


<style>
*{
	padding: 0;
	margin: 0;
	box-sizing: border-box;
}
html, body{
	display: flex;
	justify-content: center;
}
.page_wrap{
	max-width: 1280px;
	align-self: center;
	padding: 10px;
}
form{
	display: flex;
	flex-direction: column;
	padding: 20px;
}
input, p, h2{
	margin: 5px;
}
#name, #email, #submit, #delete{
	align-self: flex-start;
}
.paginate{
	display: flex;
	justify-content: center;
}
.paginate a{
	padding: 2px 5px;
	margin: 10px;
	text-decoration: none;
}
footer{
	height: 100px;
	margin: 20px 0 0;
}
.blue{
	border-bottom: 2px blue dotted;
}
</style>


<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<div class="page_wrap">
		<article>
			<h1>Заголовок статьи</h1>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum minus amet velit est vitae magni impedit cum laudantium voluptas dolorem, dolore dolores assumenda sequi magnam, totam alias minima consectetur labore.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere ex sit ea sunt eos distinctio nulla aut inventore ipsa sapiente quas at, ipsum consequatur aperiam alias ullam nesciunt assumenda. Similique.</p>

		</article>
		<h2>Oставить комментарий:</h2>
		<form action="" method="POST">
			<input type="hidden" name="add_comment" value="1" />
			<label for="name"><p>Укажите имя</p></label>
			<input id="name" name="name" type="text" value="<?php if(isset($name)) echo $name;?>" required/>
			<label for="email"><p>Укажите почту</p></label>
			<input id="email" name="email" type="email" value="<?php if(isset($email)) echo $email;	?>" required/>
			<label for="comment"><p>Введите текст</p></label>
			<textarea name="comment" id="comment" cols="30" rows="10" required><?php if(isset($text)) echo $text;?></textarea>
			<input type="submit" name="submit" value="Сохранить" id="submit" />
		</form>

		<div class="comments_wrap">

			<?php while( $comment = $comments->fetch_assoc() ){ ?>
				<p>Номер записи <?php echo $comment['id'] ?></p>
				<form action="" method="POST"> 
					<div class="comment">
						<p>
							<?php echo $comment['text']; ?>
						</p>
						<p>
							Автор: <?php echo $comment['author_name']; ?>
						</p>
					</div>
					<input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
					<input type="hidden" name="del_comment" value="1" />
					<input type="submit" id="delete" name="submit" value="Удалить" />
				</form>
				
			<?php } ?>
			<div class="paginate">
				<?php 
					for($i=0; $i<$page_count; $i++){ 
						if($i === $page ){ ?>
							<a href="?page=<?php echo $i; ?>" class="blue"><span > <?php echo $i+1; ?></span> </a>
					<?php	}else{ ?>
						<a href="?page=<?php echo $i; ?>"><span> <?php echo $i+1; ?></span> </a>
					<?php }
					}	?>
				
			</div>
		</div>
		<footer></footer>
	</div>
	
</body>
</html>