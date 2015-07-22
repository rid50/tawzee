<?php

//if(session_status() != PHP_SESSION_ACTIVE) {

	//ini_set("session.gc_maxlifetime", 60);
	//ini_set("session.gc_probability", 100);
	//ini_set("session.gc_divisor", 100); 

	//prevent buffering
	if(function_exists('apache_setenv')){
		@apache_setenv('no-gzip', 1);
	}
	
	@ini_set('zlib.output_compression', 0);

	//@ini_set('implicit_flush', 1);
	//while (ob_get_level() != 0) {
	//	ob_end_flush();
	//}
	
	//ob_implicit_flush(1);
	
	//error_log(session_id() . " ----- " . session_status() . PHP_EOL, 3, "error.log");

	//session_start();

	//error_log(session_id() . " ----- " . session_status() . PHP_EOL, 3, "error.log");

	//error_log("OB: " . (string)($_SESSION["myob"]) . PHP_EOL, 3, "error.log");
	
	//file_put_contents('./sss.txt', 'session started');

	//echo ': '.sha1(mt_rand()) . "\n\n";
	//ob_flush();
	//flush();

	@set_time_limit(0);
	
	date_default_timezone_set('Asia/Kuwait');

	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	//header('Connection: keep-alive');
	
	//error_log("1) session id:" . session_id() . PHP_EOL, 3, "error.log");
	
	//set_global(5);

//	$_SESSION["myob"] = 0;
	
	//error_log("3) session id:" . session_id() . PHP_EOL, 3, "error.log");
	
	//The interval of sending a signal to keep the connection alive
	//default: 10 seconds
	$_keep_alive_time = 10;
	
	//seconds to sleep after the data has been sent
	//default: 1 seconds
	$_sleep_time = 5;
	
	
	//$_global_session_id = "g11";
	
	$_start = time();	//start time
	//error_log("start time:" . time() . PHP_EOL, 3, "error.log");
	//error_log("start time:" . time() . PHP_EOL, 3, getcwd() . "/error.log");
	//error_log("max_execution_time:" . ini_get('max_execution_time') . PHP_EOL, 3, "error.log");
	
	
//}
//$_opid = 0;
	
function check_global() {	
	//$current_id = session_id();
	//session_write_close();

//error_log("OB: " . (string)($_SESSION['myob']) . PHP_EOL, 3, "error.log");
	
//error_log("Session ID11: " . (string)(session_id(11)) . PHP_EOL, 3, "error.log");
	//session_id($_global_session_id);
	
//	$ob = sprintf('data: {"op" : "setOwnerSignature", "date" : "%s"}', date('d/m/Y H:i:s'));
	
	$ob = apc_fetch('myob');
//error_log("OB: " . (string)($ob) . PHP_EOL, 3, "error.log");
    if($ob === false) {
//error_log("OB2: " . (string)($ob) . PHP_EOL, 3, "error.log");
		$ob = 0;
    } else
		apc_delete('myob');

	
/*	
	session_id("g11");
	session_start();
error_log("Check Global: " . (string)(isset($_SESSION["myob"]) == true) . PHP_EOL, 3, "error.log");
	$ob = 0;
	if(isset($_SESSION["myob"])) {
		$ob = $_SESSION["myob"];
		unset($_SESSION["myob"]);
	}
	session_write_close();
*/
	//	error_log("else Session ID11: " . (string)(session_id(11)) . PHP_EOL, 3, "error.log");
	//	error_log("else OB2: " . (string)($_SESSION["myob"]) . PHP_EOL, 3, "error.log");
	
	
	//if (session_id($current_id) === "") {
	//	session_start();
	//	error_log("OB11: " . (string)($_SESSION["myob"]) . PHP_EOL, 3, "error.log");
	//} else {
	//	error_log("else OB11: " . (string)($_SESSION["myob"]) . PHP_EOL, 3, "error.log");
	//}

	//session_id($current_id);
	//session_start();
//error_log("OB at end: " . (string)($_SESSION['myob']) . PHP_EOL, 3, "error.log");
	//error_log("==== OB from g11 ==== " . (string)($ob) . PHP_EOL, 3, "error.log");
	//error_log("Session ID: " . (string)(session_id()) . PHP_EOL, 3, "error.log");
	
	return $ob;
}
/*
function set_global($val) {	
	$current_id = session_id();
	//session_write_close();
	
	//Set a global session with session_id = 11
	if (session_id(11) === "") { session_start(); }
	//session_id(11);
	//session_start();

	//error_log("2) session id:" . session_id() . PHP_EOL, 3, "error.log");
	
	$_SESSION["myob"] = $val;
	session_write_close();
	
	if (session_id($current_id) === "") { session_start(); }
	//session_id($current_id);
	//session_start();
}
*/
function send($ob)
{ 
	echo $ob;
	echo "\n\n";
	//echo "event: ping\n";
	//echo 'data: {"opid" : "kuku"}' . "\n";
	//printf ('data: {"time" : "%s"}' . "\n", date('d/m H:i:s'));	
	//printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n", date('d/m H:i:s'));	
	//echo "\n";
	//echo "data: kuku\n\n";

//	print 'data: {"opid" : "approved"}' . PHP_EOL;
//	print PHP_EOL;

	//ob_end_flush();
	ob_flush();
    flush();
	//ob_start(); 
}

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

while(true) {
//    usleep($_sleep_time * 1000000);

//	printf ('data: {"op" : "setOwnerSignature", "time" : "%s"}' . "\n\n", date('d/m H:i:s'));
	//print 'data: {"opid" : "approved"}' . PHP_EOL . PHP_EOL;
	//ob_flush();
    //flush();
	

	//if (false) {
	if ($ob = check_global()) {
		//error_log($ob . PHP_EOL, 3, "error.log");
		//set_global(0);
		//error_log("Cycle: " . date('d/m H:i:s') . " --- " . session_id() . PHP_EOL, 3, "error.log");

		send($ob);
	} else {
		if(SSEUtils::time_mod($_start, $_keep_alive_time) == 0) {
			//error_log(date('d/m H:i:s'), 3, "error.log");
			//error_log((time() - $_start) . PHP_EOL, 3, "error.log");
			//error_log($_start . PHP_EOL, 3, "error.log");

			//No updates needed, send a comment to keep the connection alive.
			//From https://developer.mozilla.org/en-US/docs/Server-sent_events/Using_server-sent_events
			echo ': ' . sha1(mt_rand()) . "\n\n";
			ob_flush();
			flush();
		}
	}

    usleep($_sleep_time * 1000000);
}

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
