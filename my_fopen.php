<?php
//error_reporting(E_ALL);
//ini_set('allow_url_fopen', '1');

//session_start();
//header('Content-type: text/plain; charset=utf-8');

//$applicationNumber = $_GET['applicationNumber'];
//$thumb = $_GET['thumb'];
//$outFilePath = "c:/temp/file.log";

if (isset($_GET['applicationNumber'])) {
	//error_log((isset($_GET['applicationNumber']) ? "true" : "false") . "\r\n", 3, "errors.log");
	//header("Content-Type: image/jpg");

	$postdata = http_build_query(
		array(
			'applicationNumber' => $_GET['applicationNumber'],
			'id' => $_GET['id'],
			'thumb' => $_GET['thumb']
		)
	);
} else {
	//error_log((isset($_GET['applicationNumber']) ? 'true' : 'false') . PHP_EOL, 3, "errors.log");
	//header("Content-Type: image/png");

	$postdata = http_build_query(
		array(
			'id' => $_GET['id'],
		)
	);
}

$opts = array('http' =>
    array(
        'method'  => 'POST',
        //'header'  => $header,
        'content' => $postdata
    )
);

$context = stream_context_create($opts);

//header('Content-type: text/plain; charset=utf-8');
try {
	$dir = $_SERVER['SCRIPT_NAME'];
	$dir = substr($dir, 0, strrpos($dir, '/'));
	//$url = strtolower(array_shift(explode("/",$_SERVER['SERVER_PROTOCOL'])))."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$dir;	
	$url = "http://".$_SERVER['SERVER_NAME'].$dir;	
	//error_log($url . PHP_EOL, 3, "errors.log");		
	
	$handle = fopen($url."/get_image.php", "rb", false, $context);
	//$handle = fopen("http://tawsilat/get_image.php", "rb", false, $context);
	//print fgets($child);
	print stream_get_contents($handle);
	fclose($handle);
} catch (PDOException $e) {
	//error_log("Error: " . $e->getMessage() . PHP_EOL, 3, "errors.log");		
	header('Content-type: text/plain; charset=utf-8');
	print $e->getMessage();
}
//throw new Exception($applicationNumber);

//print stream_get_contents($child);

//fclose($child);

?>