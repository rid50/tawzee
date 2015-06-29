<?php

if(session_status() != PHP_SESSION_ACTIVE) {
	//$_SESSION["myob"] = 0;
	session_start();
	set_time_limit(0);
	set_global(0);

	//The interval of sending a signal to keep the connection alive
	//default: 300 seconds
	$_keep_alive_time = 300;
	
	//seconds to sleep after the data has been sent
	//default: 0.5 seconds
	$_sleep_time = 2;
	
	$_start = time();	//start time
	error_log("start time:" . time() . " ", 3, "error.log");
	
	
}
//$_opid = 0;
	
function check_global() {	
	$current_id = session_id();
	session_write_close();
	
	//Set a global session with session_id = 11
	session_id(11);
	session_start();
	
	$ob = 0;
	if(isset($_SESSION["myob"])) {
//error_log("==== GLOBAL OB ==== " . (string)($_SESSION["myob"]), 3, "error.log");
		$ob = $_SESSION["myob"];
		
	}
	
	session_write_close();
	
	session_id($current_id);
	session_start();
//error_log("==== OB ==== " . (string)($ob), 3, "error.log");
	return $ob;
}

function set_global($val) {	
	$current_id = session_id();
	session_write_close();
	
	//Set a global session with session_id = 11
	session_id(11);
	session_start();
	$_SESSION["myob"] = $val;
	session_write_close();
	
	session_id($current_id);
	session_start();
}

//error_log("==== session id ==== " . (string)(session_id() == ""), 3, "error.log");
//error_log("==== session id ==== " . (string)(session_status()), 3, "error.log");
//if (session_id() == "") {
//if(session_status() != PHP_SESSION_ACTIVE) {
//	session_id(1);
//	session_start();
//error_log("==== session started ==== " . (string)(session_status()), 3, "error.log");
//error_log("==== session ID ==== " . (string)(session_id()), 3, "error.log");
//}

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

function set($opid)
{
error_log("==== set ==== " . (string)($opid == 1), 3, "error.log");
	$_opid = $opid;
error_log("==== set2 ==== " . (string)($_opid == 1), 3, "error.log");
}

//while(@ob_end_clean());
//ob_implicit_flush();

function send()
{ 
//error_log("==== M5555 ====: " . session_id(), 3, "error.log");
//error_log("==== M5555 ====: ", 3, "error.log");

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

/*
//error_log("==== isset ==== " . (string)(!isset($_GET['msg']) == false), 3, "error.log");
$msg = "";
if (!isset($_GET['msg'])) {
error_log("==== _GET['msg'] not set ====", 3, "error.log");
	$posts = explode("&", file_get_contents('php://input'));
	foreach($posts as $tmp) {
		//$param[explode('=', $tmp)[0]] = explode('=', $tmp)[1];	//PHP version issues
		$p = explode('=', $tmp);
		$msg = $p[1];
		error_log("==== P[0] ==== " . $p[1], 3, "error.log");
	}
} else {
error_log("==== _GET['msg'] set ====", 3, "error.log");
	$msg = $_GET['msg'];
	error_log("==== CURL ==== " . $msg, 3, "error.log");
}

//$msg = $_GET['msg'];
//error_log("==== M222 ==== " . $msg, 3, "error.log");

if(!empty($msg)){
	set(1);
	//send();
}
*/
//send();
//ob_end_clean();
while(!connection_aborted()) {
//	echo ': ' . sha1(mt_rand()) . "\n\n";
	//printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n\n", date('d/m H:i:s'));
	//print 'data: {"opid" : "approved"}' . PHP_EOL . PHP_EOL;
   	//while (ob_get_level()) {
//error_log("==== _opid2 ==== " . (string)($_opid == 1), 3, "error.log");
	if (check_global()) {
	//if ($_opid) {
		set_global(0);
		send();
	} else {
		if(SSEUtils::time_mod($_start, $_keep_alive_time) == 0) {
			//error_log(date('d/m H:i:s'), 3, "error.log");
			error_log(time() . " ", 3, "error.log");
			error_log($_start . " ", 3, "error.log");

			//No updates needed, send a comment to keep the connection alive.
			//From https://developer.mozilla.org/en-US/docs/Server-sent_events/Using_server-sent_events
			echo ': '.sha1(mt_rand()) . "\n\n";
			ob_flush();
			flush();
		}
	}
	
//	ob_flush();
//	flush();
	//}
    usleep($_sleep_time * 1000000);
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

class SSEUtils {
	/*
	* @method SSEUtils::time_mod
	* @param $start the start timestamp
	* @param $n the time interval
	* @description Calculate the modulus of time
	*/
	static public function time_mod($start,$n){
		//error_log(time() . " ", 3, "error.log");
		//error_log($start . " ", 3, "error.log");
		//error_log($n . " ", 3, "error.log");

		return (time() - $start) % $n;
	}
}
