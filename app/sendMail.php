<?php
ini_set ('display_errors', 1);
error_reporting(E_ALL);
$to = '******',
$subject = 'На сайте произошли изменения';

$message = "<html>
<head>
	<title>На сайте произошли изменения</title>
</head>
<body>
	<p>Список изменений на отслеживаемом сайте:</p>
	<table border=\"1\" width=\"100%\" cellpadding=\"5\">
		<tr>
		<th>Было</th><th>Стало</th>
		</tr>
		$table
	</table>
</body>
</html>
";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
if (mail($to, $subject, $message, $headers)) echo "mail sended";