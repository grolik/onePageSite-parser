<?php
require_once('app/Db.php');
$db = Db::getConnection();
$query = 'SELECT title_id , header_id, text_id FROM state';
$result = $db->prepare($query);
$result->execute();
$row = $result->fetch(PDO::FETCH_ASSOC);
$titleId	=	$row['title_id'];
$headerId	=	$row['header_id'];
$textId		=	$row['text_id'];
$query = "
	SELECT	title , header , text
	  FROM	titles , headers , texts
	 WHERE	titles.id = :titleId
	   AND	headers.id = :headerId
	   AND	texts.id = :textId";
$result = $db->prepare($query);
$result->bindParam(':titleId', $titleId, PDO::PARAM_STR);
$result->bindParam(':headerId', $headerId, PDO::PARAM_STR);
$result->bindParam(':textId', $textId, PDO::PARAM_STR);
$result->execute();
$row = $result->fetch(PDO::FETCH_ASSOC);
$title	=	$row['title'];
$header	=	$row['header'];
$text	=	$row['text'];
include ('app/template.php');
