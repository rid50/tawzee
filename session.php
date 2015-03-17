<?php
session_start();

//$path = parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH);
//print !isset($_SESSION['loginName']);

//print $_SERVER['SCRIPT_NAME'];
//check that the session exists
//if(!isset($_SESSION['loginName']))
if(!isset($_SESSION['loginName']) && $_SERVER['SCRIPT_NAME'] != "/phone/request.php")
{
	//the session does not exist, redirect
	$redirect = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
	$redirect .= $_SERVER['HTTP_HOST'];
	$redirect .= '/index.php';
	header("location: $redirect");
}

/*
foreach ($_SESSION as $key=>$value)
{
	print $key . " - " . $value . "<br/>";
}
*/
/*
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	require_once('c:/simplesaml/lib/_autoload.php');
else
	require_once('/var/www/simplesamlphp/lib/_autoload.php');
	//require_once('/home/y...../public_html/simplesamlphp/lib/_autoload.php');

$session = SimpleSAML_Session::getInstance();

if (!isset($session) || !$session->isAuthenticated( )) {
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
?>
