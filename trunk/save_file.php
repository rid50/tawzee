<?php
require_once('session.php');

//foreach ($_POST as $key=>$element) {
//	echo $key."<br/>";
//}

//var_dump($_POST);
//print_r($_POST);

$fileName = $_POST['fileName'];
$param = $_POST['param'];
$file = fopen($fileName,"w");
fwrite($file, $param);
fclose($file);
//echo $_SERVER['LOGON_USER']
?>
