<?php

//error_log((isset($_GET['applicationNumber']) ? 'true' : 'false') . PHP_EOL, 3, "errors.log");

$url = "http://". $_SERVER['SERVER_NAME'] . ":8084/TawzeeJasperReports/JasperServlet";

if (!isset($_GET['CheckConnection'])) {
	if ($_GET['renderAs'] == "png")
		header("Content-Type: image/png");
	else
		header("Content-Type: application/pdf");
	
	$postdata = http_build_query(
		array(
			'reportName' => $_GET['reportName'],
			'applicationNumber' => $_GET['applicationNumber'],
			'keyFieldValue' => $_GET['keyFieldValue'],
			'renderAs' => $_GET['renderAs'],
		)
	);
} else {
	header('Content-type: text/plain; charset=utf-8');
	$postdata = http_build_query(
		array()
	);
	
	$url .= "?CheckConnection";
}

//$header = "Content-Type: application/pdf";

$opts = array('http' =>
    array(
        'method'  => 'POST',
        //'header'  => $header,
        'content' => $postdata
    )
);

$context = stream_context_create($opts);

try {
	//error_log($url . PHP_EOL, 3, "errors.log");		
	
	$handle = fopen($url, "rb", false, $context);
	
	//$handle = fopen("http://tawsilat/get_image.php", "rb", false, $context);

	if ($handle) {
		print stream_get_contents($handle);
		fclose($handle);
	} else {
//		header('Content-type: text/plain; charset=utf-8');
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