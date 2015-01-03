<?php
/*

$returnURL = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
$returnURL .= $_SERVER['HTTP_HOST'];
$returnURL .= $_SERVER['SCRIPT_NAME'];

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { //HTTPS } 
		$url = strtolower(array_shift(explode("/", $_SERVER['SERVER_PROTOCOL'])))."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
	
		print $_SERVER['SERVER_PROTOCOL'] . "<br/>";
		print explode("/", $_SERVER['SERVER_PROTOCOL']) . PHP_EOL;
		print array_shift(explode("/", $_SERVER['SERVER_PROTOCOL'])) . PHP_EOL;
		die();
*/
session_start();

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	require_once('c:/simplesaml/lib/_autoload.php');
else
	require_once('/var/www/simplesamlphp/lib/_autoload.php');
	//require_once('/home/y...../public_html/simplesamlphp/lib/_autoload.php');
	
/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();
/* Check if valid local session exists.. */
//if (!isset($session) || !$session->isValid('saml2') )
if (!isset($session) || !$session->isAuthenticated( )) {
	//SimpleSAML_Utilities::redirect( '/' . $config->getBaseURL() . 'saml2/sp/initSSO.php', array('RelayState' => 'http://www.aragorn2.cool/testsso/authenticated.html') );
	//print $config->getBaseURL();
	SimpleSAML_Utilities::redirect('http://tawzee/');
}
/*
 else {
	print 'NO';
}

	die();
*/	

	
	
//SimpleSAML_Utilities::redirect( '/' . $config->getBaseURL() .
/*
if (SimpleSAML_Auth_Simple::isAuthenticated())
	print 'OK';
else
	print 'NO';
	
die();
*/	
//$url = 'http://mewdesigncomps/index.html';

$ini = parse_ini_file("config.ini");
$idp = $ini["IdP"];
$idpSource = $ini["IdPSource"];

$_SESSION['ini_lang'] = $ini["lang"];

//throw new Exception(http_negotiate_language(array('en-US', 'ar-KW')));
//throw new Exception($_SERVER['HTTP_ACCEPT_LANGUAGE']);

//throw new Exception((preg_match('/^ar/', $_SERVER['HTTP_ACCEPT_LANGUAGE'])) == true);

if ($idp == "SAML") {
	if ($idpSource == "DB")
		$as = new SimpleSAML_Auth_Simple('mewSQLAuth');
	else {
		//print(strpos($_SERVER["HTTP_VIA"], 'mew.gov.kw'));
		//return;
		if ($_SERVER["HTTP_VIA"] != null && strpos($_SERVER["HTTP_VIA"], 'mew.gov.kw') !== false)
			$as = new SimpleSAML_Auth_Simple('mewADAuth');
		else
			$as = new SimpleSAML_Auth_Simple('mewSQLAuth');
	}
	
	//if (!SimpleSAML_Auth_Simple::isAuthenticated()) {
	if (!$as->isAuthenticated ()) {
	/*
		$url = strtolower(array_shift(explode("/", $_SERVER['SERVER_PROTOCOL'])))."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
	
		print $_SERVER['SERVER_PROTOCOL'] . "<br/>";
		print explode("/", $_SERVER['SERVER_PROTOCOL']) . PHP_EOL;
		print array_shift(explode("/", $_SERVER['SERVER_PROTOCOL'])) . PHP_EOL;
		die();
	*/	
		//$param = array (
		//	'ReturnTo' => 'http://tawzee/' 
		//);
		//$as->requireAuth ( $param );
	
		//$as->requireAuth();
		//$attributes = $as->getAttributes();
		//$url = $url . '?loginName=' . $attributes["LoginName"];
		//$_SESSION['loginName'] = $attributes["LoginName"];
	}
/*	
	if (!$as->isAuthenticated ()) {
		die ( 'ok' );
	} else {
		//$as->requireAuth();
		//$attributes = $as->getAttributes();
		//$url = $url . '?loginName=' . $attributes["LoginName"];
		//$_SESSION['loginName'] = $attributes["LoginName"];
	}
*/	
}

require('db_repo.php');
	
$param = null;
if (isset($_POST['func'])) {
	$func = $_POST['func'];
	if (isset($_POST['param'])){ $param = $_POST['param']; }
	//$param = $_POST['param'];
} else {
	$func = $_GET['func'];
	if (isset($_GET['param'])){ $param = $_GET['param']; }
	//$param = $_GET['param'];
}

try {
	$dbrep = new DatabaseRepository();
	
	if (method_exists($dbrep, $func)) {
		$result = $dbrep->$func($param);
	} else
		throw new Exception("Failed to execute the method: $func");
	
	//$result = $dbrep->getData();
} catch (Exception $e) {
	$result[] = array('error' => $e->getMessage());
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 1 Jan 1990 00:00:00 GMT');

if ($func != "getActors" || isset($result[0]['error'])) {
	header('Content-type: application/json; charset=utf-8');
	$json = json_encode($result);
	print(isset($_GET['callback']) ? "{$_GET['callback']}($json)" : $json);
} else {
	header('Content-type: text/xml; charset=utf-8');
	print $result;
}

?>
