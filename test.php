<?php 
/*
|
| @ Package: easyCrud
|
| @ Author: izni burak demirtaş / @izniburak <info@burakdemirtas.org>
| @ Web: http://burakdemirtas.org
| @ URL: https://github.com/izniburak/easy-crud
| @ Licence: The MIT License (MIT) - Copyright (c) - http://opensource.org/licenses/MIT
|
*/

header('content-type: text/html; charset=utf-8');

require 'vendor/autoload.php';
	
try
{
	$db = new PDO("mysql:host=localhost;dbname=test", "root", "");
	$db->exec("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
	$db->exec("SET CHARACTER SET 'utf8'");
}
catch ( PDOException $e )
{
	print $e->getMessage();
}
	
$crud = new buki\easyCrud($db);
