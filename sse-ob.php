<?php
//require_once('sse.php');
//header("Content-Type: text/html");

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

//	ob_start();
//	printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n", date('d/m H:i:s'));	
//	echo "\n";
