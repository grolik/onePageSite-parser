<?php
require_once('Db.php');
$db = Db::getConnection();

//удаляем старые таблицы, если необходимо
$query = "DROP TABLE IF EXISTS state";
$result = $db->prepare($query);
$result->execute();
$query = "DROP TABLE IF EXISTS titles";
$result = $db->prepare($query);
$result->execute();
$query = "DROP TABLE IF EXISTS headers";
$result = $db->prepare($query);
$result->execute();
$query = "DROP TABLE IF EXISTS texts";
$result = $db->prepare($query);
$result->execute();
$query = "DROP TABLE IF EXISTS changes";
$result = $db->prepare($query);
$result->execute();

//создаём новые таблицы
$query = "CREATE TABLE state ( title_id INT(2) NOT NULL , header_id INT(2) NOT NULL  , text_id INT(2) NOT NULL ) ENGINE = InnoDB;";
$result = $db->prepare($query);
$result->execute();
$query = "CREATE TABLE titles ( id INT(3) NOT NULL AUTO_INCREMENT , title VARCHAR(255) NOT NULL , PRIMARY KEY (id) ) ENGINE = InnoDB;";
$result = $db->prepare($query);
$result->execute();
$query = "CREATE TABLE headers ( id INT(3) NOT NULL AUTO_INCREMENT , header VARCHAR(255) NOT NULL , PRIMARY KEY (id) ) ENGINE = InnoDB;";
$result = $db->prepare($query);
$result->execute();
$query = "CREATE TABLE texts ( id INT(3) NOT NULL AUTO_INCREMENT , text TEXT NOT NULL , PRIMARY KEY (id) ) ENGINE = InnoDB;";
$result = $db->prepare($query);
$result->execute();

//таблица по умолчанию
$query = "INSERT INTO state ( title_id , header_id , text_id ) VALUES ( 1 , 1 , 1 )";
$result = $db->prepare($query);
$result->execute();

//log парсера
$query = "CREATE TABLE changes ( event_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , element VARCHAR(6) NOT NULL , value` TEXT NOT NULL ) ENGINE = InnoDB;";
$result = $db->prepare($query);
$result->execute();

//пример работы с regexp
$titlesSrc = file_get_contents('http://book-online.com.ua/content.php?book=331');
preg_match_all('/_st">\n(.*?)<\//', $titlesSrc, $matches);
array_shift($matches);
$matches = $matches[0];
array_splice($matches, -2); //убираем заключение и послесловие
foreach ($matches as &$value) {
	$value = $db->quote($value);
}
foreach ($matches as $title) {
	$query = "INSERT INTO titles ( title ) VALUES ($title)";
	$result = $db->prepare($query);
	$result->execute();
}
foreach ($matches as $header) {
	$query = "INSERT INTO headers ( header ) VALUES ($header)";
	$result = $db->prepare($query);
	$result->execute();
}

//пример работы со строками
for($i = 1; $i < 36; $i++) {
	$textsrc = file_get_contents("https://www.weblitera.com/book/?id=25&lng=2&ch=$i&l=ru");
	$text = mb_stristr($textsrc, '<td>');
	$text = str_replace('<td>', "", $text);
	$text = mb_stristr($text, '</td>', TRUE);
	$text = strip_tags($text);
	$text = mb_substr($text, 0, 2000);
	$texts[] = $text;
}
foreach ($texts as $text) {
	$query = "INSERT INTO texts ( text ) VALUES (:text)";
	$result = $db->prepare($query);
	$result->bindParam(':text', $text, PDO::PARAM_STR);
	$result->execute();
}
