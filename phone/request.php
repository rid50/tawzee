<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>وزارة الكهرباء والماء</title>
<meta name="viewport" content="width=device-width,user-scalable=no">
<link rel="stylesheet" href="jquery.mobile-1.0.min.css" />
<script src="jquery.js"></script>
<script src="jquery.mobile-1.0.min.js"></script>
</head>

<body>

<div data-role="dialog">

  <div data-role="header">
    <h1>حالة طلب</h1>
  </div>
  
  <div data-role="content">
     
<?php
	require_once('../db_repo.php');
	$result  = "";
	try {
		if (isset($_POST['applicationNumber'])){
			$dbrep = new DatabaseRepository();		
			$obj = new stdClass();
			$obj->applicationNumber = $_POST['applicationNumber'];
			if ($_POST['requestFor'] == "status") {
				$result = $dbrep->getApplicationStatus($obj);
				if ($result != null) {
					print '<div dir="rtl"><img src="../images/complete32.png" alt="complete" width="32" height="32">';
					print '<span style="position:absolute;line-height:32px;font-size:1.5em">&nbsp;&nbsp;وافق: ' . $result . '</span></div>';
					//print '<span style="display:inline-block;vertical-align:top;line-height:32px;">Approved: ' . $result . '</span>';
				} else {
					print '<div dir="rtl"><img src="../images/information32.png" alt="information" width="32" height="32">';		
					print '<span style="position:absolute;line-height:32px;font-size:1.5em">&nbsp;&nbsp;ليست مستعدة بعد</span></div>';
					//print '<span style="display:inline-block;vertical-align:top;line-height:32px;">Not ready yet</span>';
				}
				print '<a data-role="button" data-inverse="true" href="index.html#status-request">Close</a>';
			} else if ($_POST['requestFor'] == "signature") {
				$obj->ownerPhone = $_POST['ownerPhone'];
				$obj->setSignature = $_POST['setSignature'];
				$result = $dbrep->setOwnerSignature($obj);
				print '<div dir="rtl"><img src="../images/information32.png" alt="information" width="32" height="32">';		
				print '<span style="position:absolute;line-height:32px;font-size:1.5em">&nbsp;&nbsp;منته</span></div>';
				print '<a data-role="button" data-inverse="true" href="index.html#signature-provision">Close</a>';
			} else if ($_POST['requestFor'] == "application") {
				//require_once('../jetty_proxy.php');
			
				//throw new Exception($_SERVER['SERVER_NAME']);
				print '<a data-role="button" data-inverse="true" href="index.html#application-form">Close</a>';
			}
		}
	} catch (Exception $e) {
		$result[] = array('error' => $e->getMessage());
	}
	//print $result;
	
	if (isset($result[0]['error'])) {
	//header('Content-type: application/json; charset=utf-8');
		$json = json_encode($result);
		print(isset($_GET['callback']) ? "{$_GET['callback']}($json)" : $json);
	}

?>
     
     
  </div>
  
</div>
</body>
</html>
