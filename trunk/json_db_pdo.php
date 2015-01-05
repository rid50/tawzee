<?php
require_once('session.php');

/*
session_start();

//foreach ($_SESSION as $key=>$value)
//{
//	print $key . " - " . $value . "<br/>";
//}

if(isset($_SESSION['loginName']))
print $_SESSION['loginName'];
else
print 'NO';
die();

//check that the session exists
if(!isset($_SESSION['loginName']))
{
	//the session does not exist, redirect
	$redirect = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$redirect .= $_SERVER['HTTP_HOST'];
	$redirect .= '/index.php';
	header("location: $redirect");
}
*/
require('db_repo.php');
	
$param = null;
if (isset($_POST['func'])) {
	$func = $_POST['func'];
	if (isset($_POST['param'])){ $param = $_POST['param']; }
	//$param = $_POST['param'];
} else {
	$func = $_GET['func'];
	if (isset($_GET['param'])){ $param = $_GET['param']; }
	//$param = $_GET['param'];
}

try {
	$dbrep = new DatabaseRepository();
	
	if (method_exists($dbrep, $func)) {
		$result = $dbrep->$func($param);
	} else
		throw new Exception("Failed to execute the method: $func");
	
	//$result = $dbrep->getData();
} catch (Exception $e) {
	$result[] = array('error' => $e->getMessage());
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');

if ($func != "getActors" || isset($result[0]['error'])) {
	header('Content-type: application/json; charset=utf-8');
	$json = json_encode($result);
	print(isset($_GET['callback']) ? "{$_GET['callback']}($json)" : $json);
} else {
	header('Content-type: text/xml; charset=utf-8');
	print $result;
}

?>
