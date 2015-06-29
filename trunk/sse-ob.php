<?php
//session_start();
//require_once('sse.php');
//header("Content-Type: text/html");
/*
$msg = $_GET['msg'];
//if(empty($msg)){
//	send();
//}

//error_log("==== MSG0 ==== " . $msg, 3, "error.log");

$postdata = http_build_query(
	array(
		'msg' => $_GET['msg']
	)
);
	
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postdata
    )
);

$context = stream_context_create($opts);
	
$dir = $_SERVER['SCRIPT_NAME'];
$dir = substr($dir, 0, strrpos($dir, '/'));
$url = "http://" . $_SERVER['SERVER_NAME'] . $dir;

//error_log($url . PHP_EOL, 3, "error.log");		
$handle = fopen($url . "/sse.php", "rb", false, $context);
//print stream_get_contents($handle);
fclose($handle);
*/

if(session_status() != PHP_SESSION_ACTIVE)
	session_start();

$current_id = session_id();
session_write_close();

//Set a global session with session_id = 11
session_id(11);
session_start();

$_SESSION["myob"] = 1;
session_write_close();

session_id($current_id);
session_start();

/*
error_log("==== M4444 ====: " . session_id(), 3, "error.log");

$dir = $_SERVER['SCRIPT_NAME'];
$dir = substr($dir, 0, strrpos($dir, '/'));
$url = "http://" . $_SERVER['SERVER_NAME'] . $dir;

//$request = "http://localhost/test/process1.php?sessionid=".$_REQUEST["PHPSESSID"];
$request = $url . "/sse.php?msg=" . $_GET['msg'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request);
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_exec($ch);
curl_close($ch);
*/
//	ob_start();
//	printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n", date('d/m H:i:s'));	
//	echo "\n";
