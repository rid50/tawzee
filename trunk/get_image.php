<?php
//require_once('session.php');

//session_start();
/*
//foreach ($_SESSION as $key=>$value)
foreach ($_SERVER as $key=>$value)
{
	error_log($key . " - " . $value . "\r\n", 3, "errors.log");
}

error_log($_SERVER['HTTP_USER_AGENT'] . " ****" . "\r\n", 3, "errors.log");
error_log($_GET['userAgent'] . " ****" . "\r\n", 3, "errors.log");
*/
//if (!(isset($_GET['userAgent']) && $_GET['userAgent'] == "jetty"){) {

if(isset($_SERVER['HTTP_USER_AGENT']))
{
	if (strtolower(array_shift(explode("/", $_SERVER['HTTP_USER_AGENT']))) != "java")
		require_once('session.php');
}
	
//}
//require_once('is_authenticated.php');
/*
print $_GET['applicationNumber'];
die();


if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	require_once('c:/simplesaml/lib/_autoload.php');
else
	require_once('/var/www/simplesamlphp/lib/_autoload.php');
	//require_once('/home/y...../public_html/simplesamlphp/lib/_autoload.php');

$session = SimpleSAML_Session::getInstance();

print $session->isAuthenticated();
die();

if (!isset($session) || !$session->isAuthenticated()) {
	//SimpleSAML_Utilities::redirect( '/' . $config->getBaseURL() . 'saml2/sp/initSSO.php', array('RelayState' => 'http://www.aragorn2.cool/testsso/authenticated.html') );
	//print $config->getBaseURL();
	$redirect = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$redirect .= $_SERVER['HTTP_HOST'];
	$redirect .= '/index.php';

	//print $redirect;
	//die();
	
	SimpleSAML_Utilities::redirect($redirect);
}
*/
//print $_GET['applicationNumber'];
//die();

date_default_timezone_set('Asia/Kuwait');

ini_set('memory_limit', '-1');

//print "OOOOOOOOOO";
//exit;
//throw new Exception($applicationNumber);

$param = null;

if (isset($_GET['id']) || isset($_POST['id'])) {
	$param['id'] = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
	$param['applicationNumber'] = isset($_GET['applicationNumber']) ? $_GET['applicationNumber'] : $_POST['applicationNumber'];
	$param['thumb'] = isset($_GET['thumb']) ? $_GET['thumb'] : $_POST['thumb'];
} else {
	//$gets = $_SERVER['QUERY_STRING'];
	//$posts = file_get_contents('php://input');
	//$arr = explode("&", $posts)[0];
	//$applicationNumber = explode("=", $arr)[1];
	$posts = explode("&", file_get_contents('php://input'));
	foreach($posts as $tmp) {
		//$param[explode('=', $tmp)[0]] = explode('=', $tmp)[1];	//PHP version issues
		$p = explode('=', $tmp);
		$param[$p[0]] = $p[1];
	}
}

$ini = parse_ini_file("config.ini", true);
$domain = $ini["defaultDomain"];

if ($_SERVER["USERDOMAIN"] != null && (strtolower($_SERVER["USERDOMAIN"]) == "mew" || strtolower($_SERVER["USERDOMAIN"]) == "adeliya"))
	$domain = strtolower($_SERVER["USERDOMAIN"]);
	
$dsn = $ini[$domain]["dsn"];
//if (!preg_match('/;$/', $dsn))
//	$dsn .= ';';

//header("Content-Type: image/jpg");
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');
	
//$dsn = explode(';', $dsn, 2);
//$connection = strtolower($dsn[0]) . ';dbname=' . strtolower($dsn[1]);
	
//error_log($connection, 3, "errors.log");

$driver = explode(':', $dsn, 2);
//$driver = strtolower($driver[0]);

//dsn = "oci:dbname=//homam.mew.gov.kw:1521/tawzee;charset=UTF8"

//error_log($driver . "\r\n", 3, "errors.log");
	
try {
	if ($driver[0] != 'oci') {
		$dbh = new PDO($dsn, $ini[$domain]["username"], $ini[$domain]["password"]);
		//$dbh = new PDO($connection, $ini[$domain]["username"], $ini[$domain]["password"]);
		//$dbh = new PDO($dsn . 'dbname=' . $dbName, $ini[$domain]["username"], $ini[$domain]["password"]);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} else {
		$pos = strpos($driver[1], "//") + 2;
		$pos2 = strpos($driver[1], ";");

		//error_log($driver[1] . "\r\n", 3, "errors.log");
		//error_log($pos . "\r\n", 3, "errors.log");
		//error_log($pos2 . "\r\n", 3, "errors.log");
		
		//error_log(substr($driver[1], $pos, $pos2 - $pos) . "\r\n", 3, "errors.log");
		
		$dbh = oci_connect($ini[$domain]["username"], $ini[$domain]["password"], substr($driver[1], $pos, $pos2 - $pos));
	}
	
	
	$opti = getopt("content");
//throw new Exception($opt);
	//$st = "SELECT Image FROM Attachments WHERE ApplicationNumber='12345'" . " AND ID = 34";

	//error_log($param['applicationNumber'], 3, "errors.log");
	
	if (isset($param['applicationNumber'])) {
		$stcount = "SELECT COUNT(*) FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = '{$param['id']}'";
		if (isset($param['thumb']))
			$st = "SELECT Thumb AS \"Thumb\" FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = '{$param['id']}'";
		else
			$st = "SELECT Image AS \"Image\" FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = '{$param['id']}'";
	} else {
		$stcount = "SELECT COUNT(*) FROM SignatureList WHERE ID = '{$param['id']}'";
		$st = "SELECT Image AS \"Image\" FROM SignatureList WHERE ID = '{$param['id']}'";
	}

	//error_log($st . "\r\n", 3, "errors.log");

	if ($driver[0] != 'oci') {
		$ds = $dbh->query($stcount);
		if ($ds->fetchColumn() > 0) {
			$ds = $dbh->query($st);
			$r = $ds->fetch(PDO::FETCH_ASSOC);
		} else
			throw new Exception('not found');
	} else {
		$stid = oci_parse($dbh, $stcount);
		oci_execute($stid);
		if (oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			$stid = oci_parse($dbh, $st);
			oci_execute($stid);
			$r = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);		
		} else
			throw new Exception('not found');
	}
/*	
	if ($ds->fetchColumn() > 0) {
		$ds = $dbh->query($st);
		$r = $ds->fetch(PDO::FETCH_ASSOC);
	} else
		throw new Exception('not found');
*/
	//imagejpeg($r2);
	//$image = imagecreatefromjpeg($r);
	//if (isset($_GET['thumb'])) {
	//$thumb = 'thumb';	

	if (false) {
		$image = imagecreate(560, 260);
		$bg = imagecolorallocate($image, 255, 255, 255);
		$textcolor = imagecolorallocate($image, 0, 0, 255);
		imagestring($image, 5, 0, 0, print_r($param['id'], true), $textcolor);
		imagejpeg($image, null);
		exit;
	}
/*
	if (isset($param['thumb'])) {
		$image = imagecreatefromstring($r['Image']);
		$width = imagesx($image); 
		$height = imagesy($image); 
		$thumb_width = 160; 
		$thumb_height = 110; 
		
		//list($width, $height) = getimagesize($im);
		//throw new Exception($height);
		
		$image_p = imagecreatetruecolor($thumb_width, $thumb_height);

		//$image = imagecreatefromjpeg('images/temp.jpg');
		//list($width, $height) = getimagesize('images/temp.jpg');
		//throw new Exception($width);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
		imagedestroy($image);
		
		//ob_start();
		imagejpeg($image_p, null);
		//$data = ob_get_contents();
		//ob_end_clean();
	} else
		print $r['Image'];
*/
	if (isset($param['applicationNumber'])) {		// attachments
		if (isset($param['thumb'])) {
			header("Content-Type: image/jpg");
			if ($driver[0] != 'oci')
				print $r['Thumb'];
			else
				print $r['Thumb']->load();
		} else {
			header("Content-Type: application/pdf");
			if ($driver[0] != 'oci')
				print $r['Image'];
			else
				print $r['Image']->load();
		}
	} else {
			header("Content-Type: image/png");
			if ($driver[0] != 'oci')
				print $r['Image'];
			else
				print $r['Image']->load();
	}
	
	//print $thumb;
	//print $r['Image'];
	exit;

} catch (Exception $e) {
	//error_log($e . "\r\n", 3, "errors.log");
	if ($e->getMessage() == 'not found') {
		$filename = 'images/notification_error.png';
		header("Content-Length: " . filesize($filename));
		//header('HTTP/1.0 404 File Not Found');
		$fp = fopen($filename, 'rb');
		fpassthru($fp);
		exit;
	} else {
		header('Content-Type: text/plain; charset=utf-8');
		print $e->getMessage();
	}
}
?>
