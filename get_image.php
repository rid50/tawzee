<?php
session_start();
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
if (!preg_match('/;$/', $dsn))
	$dsn .= ';';

//header("Content-Type: image/jpg");
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');
	
//$dsn = explode(';', $dsn, 2);
//$connection = strtolower($dsn[0]) . ';dbname=' . strtolower($dsn[1]);
	
//error_log($connection, 3, "errors.log");
	
try {
	$dbh = new PDO($dsn, $ini[$domain]["username"], $ini[$domain]["password"]);
	//$dbh = new PDO($connection, $ini[$domain]["username"], $ini[$domain]["password"]);
	//$dbh = new PDO($dsn . 'dbname=' . $dbName, $ini[$domain]["username"], $ini[$domain]["password"]);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//throw new Exception($applicationNumber);
//$applicationNumber = '12345';
$opti = getopt("content");
//throw new Exception($opt);
	//$st = "SELECT Image FROM Attachments WHERE ApplicationNumber='12345'" . " AND ID = 34";

	//error_log($param['applicationNumber'], 3, "errors.log");
	
	if (isset($param['applicationNumber'])) {
		if (isset($param['thumb']))
			$st = "SELECT Thumb FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = '{$param['id']}'";
		else
			$st = "SELECT Image FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = '{$param['id']}'";
	} else {
			$st = "SELECT Image FROM SignatureList WHERE ID = '{$param['id']}'";
	}
	
	//error_log($st . "\r\n", 3, "errors.log");
	
	
	//$st = "SELECT Image FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = 34";
	//$st = "SELECT Image FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'" . " AND ID = 34";
	//throw new Exception($st);
	
	
	$ds = $dbh->query($st);

	//$result = array();

	//list($name, $type, $size, $content) = mysql_fetch_array($result);
	
	//error_log($ds->rowCount() . PHP_EOL, 3, "errors.log");
	
	if ($ds->rowCount() != 0) {
		$r = $ds->fetch(PDO::FETCH_ASSOC);
		//$result = r2;
	} else
		throw new Exception('not found');

		
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
			print $r['Thumb'];
		} else {
			header("Content-Type: application/pdf");
			
			//header('Content-Disposition: inline;');
			//header('Content-Disposition: attachment; filename="tawzee.pdf"');

			print $r['Image'];
		}
	} else {
			header("Content-Type: image/png");
			print $r['Image'];
	}

	//print $thumb;
	//print $r['Image'];
	exit;

} catch (Exception $e) {
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
