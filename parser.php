<?php
ini_set ('display_errors', 1);
error_reporting(E_ALL);
$url = 'http://fh79378p.bget.ru/informbox/';
$checklist = [
	'title'	=>	'#title>(.*)<\/title#',
	'header'	=>	'#h1>(.*)<\/h1#',
	'text'		=>	'#<p.*?>(.*)<\/p#s'
	];
$pageSrc = file_get_contents($url);

foreach ($checklist as $variable => $pattern) {
	preg_match($pattern, $pageSrc, $matches);
	$new[$variable] = $matches[1];
}

require_once('app/Db.php');
$db = Db::getConnection();
$tracked = ['title', 'header', 'text'];
$query = "
	SELECT
		(SELECT	value AS title
		   FROM	changes
		  WHERE	element = 'title'
		  ORDER	BY event_time DESC LIMIT 1) AS title
		  ,
		(SELECT	value AS header
		   FROM	changes
		  WHERE	element = 'header'
		  ORDER	BY event_time DESC LIMIT 1) AS header
		  ,
		(SELECT	value AS text
		   FROM	changes
		  WHERE	element = 'text'
		  ORDER	BY event_time DESC LIMIT 1) AS text";
$result = $db->prepare($query);
$result->execute();
$old = $result->fetch(PDO::FETCH_ASSOC);
print_r($old);
exit;
foreach ($old[0] as $trackedItem => $value) {
	if ($value != $new[$trackedItem]) {
		$newItem['name'] = $trackedItem;
		$newItem['value'] = $new[$trackedItem];
		$changes[] = $newItem;
	}
}
print_r($changes);
if (isset($changes)) {
	$table = "";
	foreach ($changes as $value) {
		$query = "INSERT INTO changes ( element, value )
		VALUES ( :name, :value )";
		$result = $db->prepare($query);
		$result->bindParam(':name', $value['name'], PDO::PARAM_STR);
		$result->bindParam(':value', $value['value'], PDO::PARAM_STR);
		$result->execute();
		$table .= "<tr><th colspan=\"2\"><b>{$value['name']}</b></th></tr>";
		$table .= "<tr><td>{$old[$value['name']]}</td>";
		$table .= "<td>{$new[$value['name']]}</td></tr>";
	}
	include('app/sendMail.php');
} else echo 'there are no changes on the site';
