<?php

//@ini_set("output_buffering", "Off");
//@ini_set('implicit_flush', 1);
//@ini_set('zlib.output_compression', 0);

//set_time_limit(0);
date_default_timezone_set('Asia/Kuwait');

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

//header("Content-Type: text/event-stream");
//header("Content-Type: text/html");
//header("Cache-Control: no-cache");
//header("connection: keep-alive");

$_opid = false;

function set($opid)
{
	$_opid = $opid;
}

//while(@ob_end_clean());
//ob_implicit_flush();

function send()
{ 
error_log("==== M5555 ==== ", 3, "error.log");

/*
   	while (ob_get_level()) {
		ob_end_flush();
	}

	if (ob_get_length() === false) {
		ob_start();
	}
*/
//header("Content-Type: text/html");
//header("Cache-Control: no-cache");

	
	//echo "event: ping\n";
	echo 'data: {"opid" : "kuku"}' . "\n";
	//printf ('data: {"time" : "%s"}' . "\n", date('d/m H:i:s'));	

	//printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n", date('d/m H:i:s'));	
	echo "\n";
	//echo "data: kuku\n\n";

//	print 'data: {"opid" : "approved"}' . PHP_EOL;
//	print PHP_EOL;

	//ob_end_flush();
	ob_flush();
    flush();
	//ob_start(); 

}

//error_log("==== KUKU ==== ", 3, "error.log");


//error_log("==== isset ==== " . (string)(!isset($_GET['msg']) == false), 3, "error.log");
$msg = "";
if (!isset($_GET['msg'])) {
//error_log("==== P0000 ====", 3, "error.log");
	$posts = explode("&", file_get_contents('php://input'));
	foreach($posts as $tmp) {
		//$param[explode('=', $tmp)[0]] = explode('=', $tmp)[1];	//PHP version issues
		$p = explode('=', $tmp);
		$msg = $p[1];
error_log("==== P[0] ==== " . $p[1], 3, "error.log");
	}
}

//$msg = $_GET['msg'];
//error_log("==== M222 ==== " . $msg, 3, "error.log");

if(!empty($msg)){
	send();
}

//send();
//ob_end_clean();
while(!connection_aborted()) {
	echo ': ' . sha1(mt_rand()) . "\n\n";
	//printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n\n", date('d/m H:i:s'));
	//print 'data: {"opid" : "approved"}' . PHP_EOL . PHP_EOL;
   	//while (ob_get_level()) {
		ob_flush();
		flush();
	//}
    usleep(0.5 * 1000000);
}
/*
while(!connection_aborted()) {
//while (true) {
	if (!$_opid)
		send();
    sleep(1);
    //usleep(500000);
}
*/
//gc_collect_cycles();