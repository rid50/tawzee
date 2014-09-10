<?php
//error_reporting(E_ALL);
//ini_set('allow_url_fopen', '1');

//session_start();
//header('Content-type: text/plain; charset=utf-8');

//$applicationNumber = $_GET['applicationNumber'];
//$thumb = $_GET['thumb'];
//$outFilePath = "c:/temp/file.log";

if (isset($_GET['applicationNumber'])) {
//	error_log((isset($_GET['applicationNumber']) ? "true" : "false") . "\r\n", 3, "errors.log");
	header("Content-Type: image/jpg");

	//$header = "Content-Type: image/jpg";

	$postdata = http_build_query(
		array(
			'applicationNumber' => $_GET['applicationNumber'],
			'id' => $_GET['id'],
			'thumb' => $_GET['thumb']
		)
	);
} else {
	//error_log((isset($_GET['applicationNumber']) ? 'true' : 'false') . PHP_EOL, 3, "errors.log");
	header("Content-Type: image/png");

	//$header = "Content-Type: image/pmg";

	$postdata = http_build_query(
		array(
			'id' => $_GET['id'],
		)
	);
}

/*
$postdata = http_build_query(
    array(
        'applicationNumber' => '12345',
        'thumb' => 'thumb'
    )
);
*/
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
	//header("Content-Type: image/jpg");
	//$child = popen('get_attachments.php $applicationNumber', 'r');
	//$handle = popen('http://tawsilat/get_attachments.php' . ' > ' . $outFilePath, 'rb');
	//if (isset($_GET['applicationNumber']) {
	//	$handle = fopen("http://tawsilat/get_attachment.php", "rb", false, $context);
	//else
	//	$handle = fopen("http://tawsilat/get_signature.php", "rb", false, $context);

	$dir = $_SERVER['SCRIPT_NAME'];
	//error_log($dir . PHP_EOL, 3, "errors.log");		
	//error_log(strrpos($dir, '/') . PHP_EOL, 3, "errors.log");		
	$dir = substr($dir, 0, strrpos($dir, '/'));
	//error_log($dir . PHP_EOL, 3, "errors.log");		
	$url = strtolower(array_shift(explode("/",$_SERVER['SERVER_PROTOCOL'])))."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$dir;	
	//error_log($url . PHP_EOL, 3, "errors.log");		
	
	$handle = fopen($url."/get_image.php", "rb", false, $context);
	//$handle = fopen("http://tawsilat/get_image.php", "rb", false, $context);

	//if ($handle)
	//	print 'Ok';
	//else
	//	print 'No';
	//print fgets($child);
	print stream_get_contents($handle);
	fclose($handle);
} catch (PDOException $e) {
	header('Content-type: text/plain; charset=utf-8');
	print $e->getMessage();
}
//throw new Exception($applicationNumber);

//print stream_get_contents($child);

//fclose($child);

?>