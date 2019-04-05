<?php
$probability = 70; //вероятность изменения состояния, %
require_once('Db.php');
$db = Db::getConnection();
$items = array('titles', 'headers', 'texts');
foreach ($items as $item) {
	if (mt_rand(1, 100) < $probability) change($item, $db);
}

function change(string $item, $db) {
	$query = "SELECT COUNT(id) FROM $item";
	$result = $db->prepare($query);
	$result->execute();
	$countIds = $result->fetch()[0];
	$column = mb_substr($item, 0, -1) . "_id";
	$newId = mt_rand(1, $countIds); //т.к. FLOOR(RAND()) в SQL пессимизирует крайние значения диапазона
	$query = "UPDATE state SET $column = $newId";
	$result = $db->prepare($query);
	$result->execute();
}
