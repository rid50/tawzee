<?php
	header("Content-Type: application/pdf");
	$postdata = http_build_query(
		array(
			'reportName' => 'TawzeeApplicationForm',
			'applicationNumber' => isset($_GET['applicationNumber']) ? $_GET['applicationNumber'] : $_POST['applicationNumber'],
			'keyFieldValue' => isset($_GET['applicationNumber']) ? $_GET['applicationNumber'] : $_POST['applicationNumber'],
			'renderAs' => 'pdf',
		)
	);
	
	$opts = array('http' =>
		array(
			'method'  => 'POST',
			//'header'  => $header,
			'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
			'content' => $postdata
		)
	);				
	
	$context = stream_context_create($opts);
	
	//$url = "http://tawzee:8084/TawzeeJasperReports/JasperServlet";
	$url = "http://". $_SERVER['SERVER_NAME'] . ":8084/TawzeeJasperReports/JasperServlet";
	
	$handle = fopen($url, "rb", false, $context);

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
?>