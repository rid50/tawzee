<?php
//print $_SERVER['DOCUMENT_ROOT'];
//$path = parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH);

//print  $path;
require_once('session.php');

//error_log((isset($_GET['applicationNumber']) ? 'true' : 'false') . PHP_EOL, 3, "errors.log");

//if (!isset($_GET['cgi']))
	$url = "http://". $_SERVER['SERVER_NAME'] . ":8084/TawzeeJasperReports/JasperServlet";
//else
	//$url = "http://". $_SERVER['SERVER_NAME'] . "/cgi-bin/RunJasperReportsCGI.pl";

if (!isset($_GET['CheckConnection'])) {
	if ((isset($_GET['renderAs']) ? $_GET['renderAs'] : $_POST['renderAs']) == "png")
		header("Content-Type: image/png");
	else
		header("Content-Type: application/pdf");
	
	$postdata = http_build_query(
		array(
			'reportName' => isset($_GET['reportName']) ? $_GET['reportName'] : $_POST['reportName'],
			'applicationNumber' => isset($_GET['applicationNumber']) ? $_GET['applicationNumber'] : $_POST['applicationNumber'],
			'keyFieldValue' => isset($_GET['keyFieldValue']) ? $_GET['keyFieldValue'] : $_POST['keyFieldValue'],
			'renderAs' => isset($_GET['renderAs']) ? $_GET['renderAs'] : $_POST['renderAs'],
		)
	);
} else {
	header('Content-type: text/plain; charset=utf-8');
	$postdata = http_build_query(
		array()
	);
	
	$url .= "?CheckConnection";
}

	//error_log($url . PHP_EOL, 3, "errors.log");		
	

//$header = "Content-Type: application/pdf";

//	error_log($postdata . PHP_EOL, 3, "errors.log");		


$opts = array('http' =>
    array(
        'method'  => 'POST',
        //'header'  => $header,
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postdata
    )
);

$context = stream_context_create($opts);

try {
	//error_log($url . PHP_EOL, 3, "errors.log");		
	
	$handle = fopen($url, "rb", false, $context);

	//error_log("handle: " . ($handle == null) . PHP_EOL, 3, "errors.log");		
	
	//$handle = fopen("http://tawsilat/get_image.php", "rb", false, $context);

	if ($handle) {
		//error_log("handle: " . ($handle == null) . PHP_EOL, 3, "errors.log");		
		print stream_get_contents($handle);
		fclose($handle);
	} else {
		//header('Content-type: text/plain; charset=utf-8');
		if (isset($_GET['CheckConnection'])) {
			http_response_code(200);
			print "666";		// server is not running
		} else {
			http_response_code(500);
		}
	}
	//print fgets($child);
} catch (PDOException $e) {
	//error_log("Error: " . $e->getMessage() . PHP_EOL, 3, "errors.log");		
	header('Content-type: text/plain; charset=utf-8');
	print $e->getMessage();
}
//throw new Exception($applicationNumber);

//print stream_get_contents($child);

//fclose($child);

?>