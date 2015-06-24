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
	//echo 'data: {"opid" : "approved"}';
	//printf ('data: {"time" : "%s"}' . "\n", date('d/m H:i:s'));	

	printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n", date('d/m H:i:s'));	

	echo "\n";
	//echo "data: kuku\n\n";

//	print 'data: {"opid" : "approved"}' . PHP_EOL;
//	print PHP_EOL;

    ob_flush();
	//ob_end_flush();
	//ob_end_flush();
    flush();
	//ob_start(); 

}

//send();
//ob_end_clean();

while(!connection_aborted()) {
//while (true) {
	if (!$_opid)
		send();
    sleep(1);
    //usleep(500000);
}

//gc_collect_cycles();