<?php
session_start();

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	require_once('c:/simplesaml/lib/_autoload.php');
else
	require_once('/var/www/simplesamlphp/lib/_autoload.php');
	//require_once('/home/y...../public_html/simplesamlphp/lib/_autoload.php');

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
	
	$as->requireAuth();
	$attributes = $as->getAttributes();
	//$url = $url . '?loginName=' . $attributes["LoginName"];
	//$_SESSION['loginName'] = $attributes["LoginName"];
}

?>

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="description" content="MEW Distribution Department">
  
    <title>Ministry of Electricity and Water</title>
	
	<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="favicon.ico" />

	<!--link rel="stylesheet" media="all" href="themes/smoothness/jquery-ui-1.10.3.custom.min.css" /-->	
	<!--link rel="stylesheet" media="all" href="themes/uilightness/jquery-ui.min.css" /-->	
	<!--link rel="stylesheet" media="all" href="themes/smoothness/jquery-ui.min.css" /-->
	<link id="stylesheet" rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.min.css" />
	<!--link id="stylesheet" rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.min.css" /-->
	
	<!--link rel="stylesheet" media="all" href="themes/redmond/jquery-ui.min.css" /-->
	
	
	<link rel="stylesheet" media="all" href="css/ui.jqgrid.css" />	

	<link rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="js/themes/default/style.css" />
	<link rel="stylesheet" href="js/themes/default/style2.css" />

    <link rel="stylesheet" media="screen" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" media="print" type="text/css" href="css/style.css"/>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
	<!--script src="//code.jquery.com/jquery-1.11.1.min.js"></script--> 

	<!--script src="js/jquery-ui.min.js" type="text/javascript"></script-->
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<!--script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js" type="text/javascript"></script-->
    
    <script src="js/jqGridJs/i18n/grid.locale-ar.js" type="text/javascript"></script>
    <script src="js/jqGridJs/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="js/jqGridJs/jquery.jqGrid.min.js" type="text/javascript"></script>
    <script src="js/jqGridJs/grid.filtergrid.js" type="text/javascript"></script>
	
	<script src="js/jquery.blockUI.js" type="text/javascript"></script>
    <!--script src="js/jquery.hotkeys.js" type="text/javascript"></script-->
	
	<script src="js/jstree.min.js" type="text/javascript"></script>
	<script src="js/jstreecheckbox2.js" type="text/javascript"></script>
	
	<script src="js/jquery.ui.datepicker-ar.js" type="text/javascript"></script>
	<script src="js/jquery.i18n.properties-min-1.0.9.js" type="text/javascript"></script>
    <script src="js/script.js" type="text/javascript"></script>
    <script src="js/grid.js" type="text/javascript"></script>
    <script src="js/my-helpers.js" type="text/javascript"></script>
    <script src="js/xml-helpers.js" type="text/javascript"></script>

    <script src="js/format.20110630-1100.min.js"></script>
	
    <!--script src="sliderengine/amazingslider.js"></script>
    <script src="sliderengine/initslider-1.js"></script-->
	
	<style type="text/css">
		.accessRejected {
			background-image: url(images/rejected.png) !important;
			background-position:0px 0px !important;
			background-repeat:repeat !important;
			background-size: auto !important;
			width: inherit;
		}
/*	
		.ui-accordion .ui-accordion-content {
			overflow: hidden;
			padding: 1em 8px;
		}
*/	
	</style>	
</head>

<body dir="ltr">
	<form action="#">
    <div id="wrapper">
    	<header>
			<div class="floatLeft">
				<a href="#" class="psc-logo"><img src="images/logo.png" alt="Back to Home" title="Back to Home" /></a>
			</div>
			<div class="floatRight">
				<a href="#" id="flagUK"><img src="images/FlagUK.png" alt="English" title="English" /></a>
				<a href="#" id="flagKuwait"><img src="images/FlagKuwait.png" alt="Arabic" title="Arabic" /></a>
				<div id="userLoginSelectDiv" style="float:inherit; margin-top:6px"><span>User</span>:&nbsp;<select id="userLoginSelect" style="width:140px"></select></div>
			</div>
			
        </header>
        
        <!--div id="main-div" class="ui-widget"-->
        <div id="main-div">
            <section id="left-section" class="floatLeft ui-corner-all">
			</section>

            <section id="right-section" class="floatRight ui-corner-all">			
				<div id="accordion" style="display:none">
					<span>Application</span>
					<div>
						<button id="sync" class="floatLeft" title="Go to last selected row"></button>
						<div><span>App</span>#&nbsp;<input type="text" id="app-number-search" maxlength="10" size="10" class="text ui-widget-content ui-corner-all" /></div>
						<br/>
						<div id="formButtonSet555" class="button-set" style="display:inline">
							<button id="newForm" title="Add form"></button>
							<button id="editSelectedForm" title="Edit form"></button>
							<button id="printSelectedForm" title="Print form"></button>
						</div>
						<!--button id="startService" title="Start Service"></button-->
						
						
						<!--button id="takePicture" title="Take a snap">Make</button-->
						<!--button id="getPicture" title="Get a picture">Get</button-->
					</div>
					<span>Form</span>
					<div style="text-align:right;">
						<a href="#" id="application-form-link" data-form="main-form" style="white-space:nowrap">Electric Delivery Application</a><br/><br/>
						<a href="#" id="load-form-link" data-form="load-form">Load Requirements</a>
						<!--button id="addNew" title="Add new">Add</button>
						<button id="delete" title="Delete form">Delete</button-->
					</div>
					<span>Report Preview</span>
					<div>
						<div id="signatureImages">
						</div>
						<!--button id="signButton" title="Sign the document">Sign</button>
						<img src="images/pinault.png" title="François-Henri Pinault" width="160px" height="100px"  />
						<img src="images/barack.jpg" title="Barack Obama" width="160px" height="100px"  />
						<img src="images/visa.jpg" title="Visa Card" width="160px" height="100px"  /-->

					</div>
					<span>Drawings & Scanned Images</span>
					<div>
						<div id="attachmentTitles">
						</div>
					</div>
					<span>User Assignment</span>
					<div>
						<fieldset id="userImportExport" style="text-align:center;">
							<legend>Users</legend>
							<button id="userImportButton">Import</button>
							<button id="userExportButton">Export</button>
						</fieldset>
					</div>
					<span>Access Control</span>
					<div style="text-align:center;">
						<button id="saveAccessControlSettings">Save Settings</button>
					</div>
				</div>
            </section>
        </div>
        
        <footer class="footer">
			<div id="copyright" class="footerLeft floatLeft">
				&copy; Copyright 2014 Ministry of Electricity and Water &nbsp;|&nbsp; All Rights Reserved
			</div>
        </footer>
    </div>
	</form>	

<div id="resourceManagement" style="display:none">
	<div id="jstree" dir="ltr"></div>
	<div id="jstree_userlist" dir="ltr" style="display:none"></div>
	<div id="jstree_resourcelist" dir="ltr" style="display:none"></div>	
	<fieldset id="userLegend" style="display:none">
		<legend>Legend</legend>
		<img src="images/folder_headquarter.png" style="width:16px; height:16px" alt="User"/><span style="vertical-align:top">Headquarter</span><br/>
		<img src="images/folder_consultancy.png" style="width:16px; height:16px" alt="User"/><span style="vertical-align:top;">Consultancy Office</span><br/>
		<img src="images/folder_lightning.png" style="width:16px; height:16px" alt="User"/><span style="vertical-align:top;">Emergency Control Office</span><br/>
		<img src="images/user.png" alt="User"/><span style="vertical-align:top">User</span><br/>
		<img src="images/manager.png" alt="Manager"/><span style="vertical-align:top">Manager</span><br/>
		<img src="images/director.png" alt="Director"/><span style="vertical-align:top">Director</span><br/>
	</fieldset>
	<fieldset id="aclLegend" class="jstree-default" dir="ltr" style="display:none">
		<legend>Legend</legend>
		<span>Show | Enable</span><br/>
		<div class="jstree-checked">
			<i class="jstree-icon jstree-checkbox"></i>&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="jstree-icon jstree-checkbox"></i>
		</div>
	</fieldset>
	
</div>
<!--
<div id="terms-and-conditions" style="display:none; position:relative;">
	<div id="scroller-anchor"></div>
	<div id="scroller" style="position:relative; top:50px">
		<img src="images/conditions_icon.png" alt="Terms & Conditions" title="Terms & Conditions" />
	</div>
</div>
-->
<div id="main-form" class="forms" style="display:none; padding:20px; font-size:1.2em" class="floatLeft"
	data-link="application-form-link" data-parent-table="Application" data-child-table="ApplicationDetail" data-key-field="application-number">
		<div>
			<!--div id="terms-anchor"></div-->
			<div id="terms" style="display:inline">
				<!--img src="images/terms_conditions.png" alt="Terms & Conditions" title="Terms & Conditions" /-->
				<button id="terms-button" title="Terms & Conditions">Terms & Conditions</button>
			</div>
			<!--script type="text/javascript"> 
				$(function() {
				
					var s = $("#scroller");
					s.css({
						position: "fixed",
						top: ($("#left-section").offset().top + 51) + "px",
						left: ($("#left-section").offset().left + 1) + "px",
					});
				
					//moveScroller();
				});
			</script--> 

			<div class="button-set" style="display:inline">
				<button id="addFormApp" title="Add form"></button>
				<button id="saveFormApp" title="Save form"></button>
				<button id="printFormApp" title="Print form"></button>
				<button id="deleteFormApp" title="Delete form"></button>
			</div>
			
			<!--input type="text" id="error_box" /-->
			<!--div style="position: absolute; top:10px; right:0px;">
				<button id="add" title="Add a new form"></button>
				<button id="save" title="Save form"></button>
				<button id="print" title="Print form"></button>
				<button id="delete" title="Delete form"></button>
			</div-->
			<br/>
			<label for="application-number">Application Number</label>
			<input type="text" id="application-number" autofocus maxlength="24" class="text ui-widget-content ui-corner-all" data-is-required="true"/>
			<label for="application-date">Application Date</label>
			<input type="text" id="application-date" maxlength="10" class="rid50-datepicker text ui-widget-content ui-corner-all" data-is-required="true"/><br/>
			
			<label for="owner-name">Owner Name</label>
			<input type="text" id="owner-name" maxlength="24" style="width:140px" class="text ui-widget-content ui-corner-all" data-is-required="true"/>
			<label for="project-name">Project Name</label>
			<input type="text" id="project-name" maxlength="24" style="width:140px" class="text ui-widget-content ui-corner-all" data-is-required="true"/>
			<label for="controlCenterId">Control Center</label>
			<select id="controlCenterId" class="ui-widget-content ui-corner-all" style="width:140px" data-validation='^([1-9])', data-validation-message="$.i18n.prop('ControlCenter') + ' ' + $.i18n.prop('CannotBeEmpty')"></select><br/>
						
			<label for="area">Area</label>
			<input type="text" id="area" maxlength="15" size="10" class="text ui-widget-content ui-corner-all" data-is-required="true"/>
			<label for="block">Block</label>
			<input type="text" id="block" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" data-is-required="true"/>
			<label for="plot">Plot</label>
			<input type="text" id="plot" maxlength="8" size="8" class="text ui-widget-content ui-corner-all" data-is-required="true"/>
			<label for="construction-exp-date">Expiration Date</label>
			<input type="text" id="construction-exp-date" maxlength="10" size="10" class="text ui-widget-content ui-corner-all" data-is-required="true"/><br/>
			
			<label for="project-type">Project Type:</label>
			<input type="radio" id="private-housing" name="project-type" />
			<label for="private-housing">Private Housing</label>
			<input type="radio" id="investment" name="project-type" />
			<label for="investment">Investment</label>
			<input type="radio" id="commercial" name="project-type" />
			<label for="commercial">Commercial</label>
			<input type="radio" id="governmental" name="project-type" />
			<label for="governmental">Governmental</label>
			<input type="radio" id="agricultural" name="project-type" />
			<label for="agricultural">Agricultural</label>
			<input type="radio" id="industrial" name="project-type" />
			<label for="industrial">Industrial</label><br/>
			
			<label for="residence-total-area">Total Area</label>
			<input type="text" id="residence-total-area" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="square-meters">m2</label>
			<label for="construction-area">Construction Area</label>
			<input type="text" id="construction-area" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="square-meters">m2</label>
			<label for="ac-area">AC Area</label>
			<input type="text" id="ac-area" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="square-meters">m2</label><br/>
			
			<label for="current-load">Current Load</label>
			<input type="text" id="current-load" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="kilo-watt">KWT</label>
			<label for="extra-load">Extra Load</label>
			<input type="text" id="extra-load" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="kilo-watt">KWT</label><br/>
			
			<label for="load-after-delivery">Maximum Load After Delivery</label>
			<input type="text" id="load-after-delivery" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="kilo-watt">KWT</label>
			<label for="conductive-total-load">Conductive Total Load</label>
			<input type="text" id="conductive-total-load" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="kilo-watt">KWT</label><br/>

			<label for="feed-points">Number of Supply Points</label>
			<input type="text" id="feed-points" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="site-feed-point">Site Feeding Point:</label>
			<input type="radio" id="vault" name="site-feed-point" />
			<label for="vault">Vault</label>
			<input type="radio" id="ground" name="site-feed-point" />
			<label for="ground">Ground</label>
			<input type="radio" id="mezzanine" name="site-feed-point" />
			<label for="mezzanine">Mezzanine</label>
			<input type="radio" id="other" name="site-feed-point" />
			<label for="other">Other</label><br/>

			<label for="requirements" style="text-decoration:underline">Requirements for the delivery of electricity:</label><br/>
			<input type="radio" id="build" name="requirements" />
			<label for="build">Build a room inside a residence</label>
			<input type="radio" id="build2" name="requirements" />
			<label for="build2">Building a room for private secondary transfer station</label><br/>
			<input type="radio" id="build3" name="requirements" />
			<label for="build3">Installing a power factor improvement</label><br/>
				
			<fieldset id="cable-size" class="access-control" style="display:inline">
			<legend>Cable Size(mm2)</legend>
			<!--label for="cable-size">Cable Size(mm2)</label-->
			<input type="radio" id="cs35" name="cable-size" />
			<label for="cs35">35</label>
			<input type="radio" id="cs150" name="cable-size" />
			<label for="cs150">150</label>
			<input type="radio" id="cs300" name="cable-size" />
			<label for="cs300">300</label>
			</fieldset>
			
			<fieldset id="fuze" class="access-control" style="display:inline">
			<legend>Fuze(amps)</legend>
			<!--label for="fuze">Fuze(amps)</label-->
			<input type="radio" id="f100" name="fuze" />
			<label for="f100">100</label>
			<input type="radio" id="f200" name="fuze" />
			<label for="f200">200</label>
			<input type="radio" id="f300" name="fuze" />
			<label for="f300">300</label>
			</fieldset>
			
			<fieldset id="meter" class="access-control" style="display:block">
			<legend>Meter(amps)</legend>
			<!--label for="meter">Meter(amps)</label-->
			<input type="radio" id="m1" name="meter">
			<label for="m1">40</label>
			<input type="radio" id="m2" name="meter">
			<label for="m2">50</label>
			<input type="radio" id="m3" name="meter" />
			<label for="m3">75</label>
			<input type="radio" id="m4" name="meter" />
			<label for="m4">125</label>
			<input type="radio" id="m5" name="meter" />
			<label for="m5">200/5</label>
			<input type="radio" id="m6" name="meter" />
			<label for="m6">300/5</label>
			</fieldset><br/>
		
			<table id="table-application" class="access-control" data-label="Kqa" cellspacing="0" cellpadding="0">
				<tr>
					<td style="white-space:nowrap;">
						<label for="switchCapacity">Switch</label>
					</td>
					<td colspan="2">1000
						<label for="kqa">K.Q.A</label>
					</td>
					<td colspan="2">1250
						<label for="kqa">K.Q.A</label>
					</td>
					<td colspan="2">1600
						<label for="kqa">K.Q.A</label>
					</td>
				</tr>
				<tr>
					<td rowspan="2">
						<label for="number">Number</label>
					</td>
					<td rowspan="2">
						<label for="loadAfterDelivery">Load After Delivery</label>
						<label for="summer">Summer</label>&nbsp;&nbsp;&nbsp;KW
					</td>
					<td rowspan="2">
						<label for="meterSize">Meter Size</label>
						<label for="amp">(AMP)</label>
					</td>
					<td rowspan="2">
						<label for="loadAfterDelivery">Load After Delivery</label>
						<label for="summer">Summer</label>&nbsp;&nbsp;&nbsp;KW
					</td>
					<td rowspan="2">
						<label for="meterSize">Meter Size</label>
						<label for="amp">(AMP)</label>
					</td>
					<td rowspan="2">
						<label for="loadAfterDelivery">Load After Delivery</label>
						<label for="summer">Summer</label>&nbsp;&nbsp;&nbsp;KW
					</td>
					<td rowspan="2">
						<label for="meterSize">Meter Size</label>
						<label for="amp">(AMP)</label>
					</td>
				</tr>
				<tr>
					<td>
						<button class="addRow" tabindex="-1" title="Add a new row"></button>
					</td>
				</tr>
				<tr class="tr-application-detail">
					<td>
						<input type="text" class="Switch" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="K1000KWT" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="K1000AMP" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="K1250KWT" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="K1250AMP" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="K1600KWT" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="K1600AMP" style="width:80px;" data-is-required="true" />
					</td>
					<td>
						<button class="deleteRow" tabindex="-1" title="Delete the row"></button>
					</td>
				</tr>
			</table>
			<hr/>
			<table id="table-possibility" class="access-control" data-label="possibility" style="width: 100%">
				<tr>
					<td style="border:0px" class="floatLeft">
						<fieldset id="possibility-yes" class="possibility" style="text-align:left">
							<legend>There is a possibility of delivering <br/> an electric current</legend>
							<br/>
							<p style="height:20px"><input id="station-no" type="checkbox" name="possibility-yes" />
							<label for="station-no">Station Number</label>(
							<input type="text" id="station-number" style="width:80px; vertical-align:middle;"/>&nbsp;)</p>

							<p style="height:20px"><input id="special-adapter" type="checkbox" name="possibility-yes" />
							<label for="special-adapter">Special Adapter</label></p>
							
							<p style="height:20px"><input id="private-station" type="checkbox" name="possibility-yes" />
							<label for="private-station">Private Station</label></p>

						</fieldset>
					</td>				
					<td style="border:0px" class="floatRight">
						<fieldset id="possibility-no" class="possibility" style="text-align:left">
							<legend>There is no a possibility of delivering <br/> an electric current</legend>
							<br/>
							<p style="height:20px"><input id="no-electric-grid-access" type="checkbox" name="possibility-no" />
							<label for="no-electric-grid-access">There is no electric grid access</label>

							<p style="height:20px"><input id="wanted-another-site" type="checkbox" name="possibility-no" />
							<label for="wanted-another-site">Wanted site for a major transfer station</label></p>
							
							<p style="height:20px"><input id="required-secondary-site" type="checkbox" name="possibility-no" />
							<label for="required-secondary-site">Required secondary transfer station site</label></p>

						</fieldset>
					</td>				
				</tr>
			</table>
			<div style="height: 50px"></div>
		</div>
</div>

<div id="load-form" class="forms" data-link="load-form-link" style="display:none; padding:20px; font-size:1.2em" class="floatLeft"
		data-key-field="file-number">
		<div>
			<div id="formButtonSet" class="button-set" style="display:inline">
				<button id="addFormLoad" title="Add form"></button>
				<button id="saveFormLoad" title="Save form"></button>
				<button id="printFormLoad" title="Print form"></button>
				<button id="deleteFormLoad" title="Delete form"></button>
			</div>

			<label for="file-number">File Number</label>
			<input type="text" id="file-number" autofocus maxlength="24" class="text ui-widget-content ui-corner-all" data-is-required="true" />
			<label for="load-date">Date</label>
			<input type="text" id="load-date" maxlength="10" style="width:110px;" class="rid50-datepicker text ui-widget-content ui-corner-all" data-is-required="true" /><br/>

			<label for="project-name2">Project Name</label>
			<input type="text" id="project-name2" readonly="readonly" tabindex="-1" maxlength="24" class="text ui-widget-content ui-corner-all" /><br/>

			<label for="area2">Area</label>
			<input type="text" id="area2" readonly="readonly" tabindex="-1" maxlength="15" size="10" class="text ui-widget-content ui-corner-all" />
			<label for="block2">Block</label>
			<input type="text" id="block2" readonly="readonly" tabindex="-1" maxlength="5" size="5" class="text ui-widget-content ui-corner-all" />
			<label for="plot2">Plot</label>
			<input type="text" id="plot2" readonly="readonly" tabindex="-1" maxlength="8" size="8" class="text ui-widget-content ui-corner-all" />
			
			<label for="construction-exp-date2">Expiration Date</label>
			<input type="text" id="construction-exp-date2" readonly="readonly" tabindex="-1" maxlength="10" size="10" class="text ui-widget-content ui-corner-all" /><br/>
			
			<label for="owner-name2">Owner Name</label>
			<input type="text" id="owner-name2" readonly="readonly" tabindex="-1" maxlength="20"  size="20" class="text ui-widget-content ui-corner-all" />

			<label for="feed-points2">The Number of Feed Points</label>
			<input type="text" id="feed-points2" readonly="readonly" tabindex="-1" maxlength="10" size="10" class="text ui-widget-content ui-corner-all" /><br/>

			<div style="text-align:center; font-size:1.1em">
				<label for="load_required" style="text-decoration:underline">Electrical Loads required by the Project</label>
			</div><br/>
			<table id="table-load" class="access-control" data-label="load_required" cellspacing="0" cellpadding="0" style="margin:0 auto;">
				<tr>
					<td rowspan="2">
						<label for="description">Description</label>
					</td>
					<td colspan="2" rowspan="2">
						<label for="CL">Connector Load</label><br/>
						<label for="kw">(KW)</label>
					</td>
					<td colspan="2">
						<label for="maximum-loads">Maximum Diverse Loads</label>
					</td>
					<td  colspan="2" rowspan="2" style="width:220px;">
						<label for="remarks">Remarks</label>
					</td>
				</tr>
				<tr>
					<td>
						<label for="summer">Summer</label>
						<label for="kw">(KW)</label>
					</td>
					<td>
						<label for="winter">Winter</label>
						<label for="kw">(KW)</label>
					</td>
					<td>
						<button class="addRow" tabindex="-1" title="Add a new row"></button>
					</td>
				</tr>
				<tr class="tr-load-detail">
					<td>
						<input type="text" class="description" style="width:200px;" data-is-required="true" />
					</td>
					<td colspan="2">
						<input type="text" class="connector-load" style="width:100px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="summer-load" style="width:100px;" data-is-required="true" />
					</td>
					<td>
						<input type="text" class="winter-load" style="width:100px;" data-is-required="true" />
					</td>
					<td colspan="2">
						<input type="text" class="remarks" style="width:170px;" />
					</td>
					<td>
						<button class="deleteRow" tabindex="-1" title="Delete the row"></button>
					</td>
				</tr>
				<tr>
					<td>
						<label for="total-load">Total</label>
					</td>					
					<td colspan="2">
						<input type="text" readonly="readonly" tabindex="-1" style="width:100px;" />
					</td>
					<td>
						<input type="text" readonly="readonly" tabindex="-1" style="width:100px;" />
					</td>
					<td>
						<input type="text" readonly="readonly" tabindex="-1" style="width:100px;" />
					</td>
					<td colspan="2">
					</td>
				</tr>
				<tr>
					<td rowspan="2">
						<label for="power-factor">Power Factor</label>
					</td>
					<td>
						<label for="summer">Summer</label>
					</td>
					<td>
						<input type="text" id="power-factor-summer" style="width:60px;" data-is-required="true"/>
					</td>
					<td colspan="2" rowspan="2">
						<label for="maximum-loads">Maximum Diverse Loads</label>
					</td>
					<td>
						<label for="summer">Summer</label>
					</td>
					<td>
						<input type="text" id="maximum-loads-summer" style="width:80px;" data-is-required="true" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="winter">Winter</label>
					</td>
					<td>
						<input type="text" id="power-factor-winter" style="width:60px;" data-is-required="true" />
					</td>
					<td>
						<label for="winter">Winter</label>
					</td>
					<td>
						<input type="text" id="maximum-loads-winter" style="width:80px;" data-is-required="true" />
					</td>
				</tr>
			</table>
			<div style="height: 50px"></div>
		</div>
</div>

<!--
<div id="formButtonSet">
	<button id="add" title="Add a new form"></button>
	<button id="save" title="Save form"></button>
	<button id="print" title="Print form"></button>
	<button id="delete" title="Delete form"></button>
</div>
-->

<!--
<div id="amazingslider" style="display:none;">
	<img src="get_scanned_image.php?param[applicationNumber]=12345" />
-->
<!--
<div id="amazingslider" style="display:none; margin-top:60px; margin-left:40px; max-width:700px; float:left; clear:both;">
    <div id="amazingslider-1" style="display:block;position:relative;margin:16px auto 32px;">
        <ul class="amazingslider-slides" style="display:none;">
			<li><a href="images-slides/20140213104947335-lightbox.png" class="html5lightbox"><img src="images-slides/20140213104947335.png" alt="الرسم اثنين - قسمة المنزل" /></a></li>
            <li><a href="images-slides/20140213105038910-lightbox.png" class="html5lightbox"><img src="images-slides/20140213105038910.png" alt="رسم واحد - كامل تخطيط الأسلاك تحجيمها" data-description="رسم واحد - كامل تخطيط الأسلاك تحجيمها" /></a></li>
            <li><a href="images-slides/20140213105128933-lightbox.png" class="html5lightbox"><img src="images-slides/20140213105128933.png" alt="رسم" /></a></li>
            <li><a href="images-slides/20140213105203627-lightbox.png" class="html5lightbox"><img src="images-slides/20140213105203627.png" alt="رسم" /></a></li>
            <li><a href="images-slides/20140213105238049-lightbox.png" class="html5lightbox"><img src="images-slides/20140213105238049.png" alt="قطاع شبكات التوزيع الكهربائية" data-description="وزارة الكهرباء والماءطلب ترخيص ايصال تيار كهربائي " /></a></li>
            <li><a href="images-slides/20140213105308681-lightbox.png" class="html5lightbox"><img src="images-slides/20140213105308681.png" alt="قطاع شبكات التوزيع الكهربائية" data-description="وزارة الكهرباء والماءطلب ترخيص ايصال تيار كهربائي " /></a></li>
        </ul>
        <ul class="amazingslider-thumbnails" style="display:none;">

			<li><img src="images-slides/20140213104947335-tn.png" /></li>
            <li><img src="images-slides/20140213105038910-tn.png" /></li>
            <li><img src="images-slides/20140213105128933-tn.png" /></li>
            <li><img src="images-slides/20140213105203627-tn.png" /></li>
            <li><img src="images-slides/20140213105238049-tn.png" /></li>
            <li><img src="images-slides/20140213105308681-tn.png" /></li>

        </ul>
        <div class="amazingslider-engine" style="display:none;"><a href="http://amazingslider.com">JavaScript Slideshow</a></div>
    </div>
	
</div>
-->

<!--			
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345" />
  	    		</li>
-->				

<!--

            <li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345&thumb" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345&thumb" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345&thumb" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345&thumb" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345&thumb" />
  	    		</li>
  	    		<li>
  	    	    <img src="get_scanned_image.php?param[applicationNumber]=12345&thumb" />
  	    		</li>
-->				

	<div id="signature-container" style="direction:ltr; display:none; overflow:hidden;">
	</div>

	<div id="report-container" style="direction:ltr; display:none; overflow:hidden;">
	</div>
	
	<div id="flexslider-container" style="direction:ltr; display:none; overflow:hidden;">
      <div class="slider">
        <div id="slider" class="flexslider">
          <ul class="slides">
          </ul>
        </div>
        <div id="carousel" class="flexslider">
          <ul class="slides">
          </ul>
        </div>
      </div>
	</div>

<div id="divGrid" style="display:none;">
	<form>
		<div>
			<span>Search File#:</span>
			<input type="text" id="grid_search_field" onkeydown="doSearch(arguments[0]||event)" style="direction:ltr; text-align:left;" class="text ui-widget-content ui-corner-all" />
			<button onclick="gridReload()" id="gridSubmitButton">Go</button>
			<input type="checkbox" id="autosearch" onclick="enableAutosubmit(this.checked)" />
			<span>Enable Autosearch</span>
			<!--div id="grid_search_hidden_field" style="visibility: hidden;"></div-->
		</div>

		<div id="myjqGrid">
			<div>
				<table id="grid" ></table>
				<div id="pager"></div>
			</div>
			<!--div style="padding-top:10px;">
			<table id="grid_d"></table>
			<div id="pager_d"></div>
			</div-->
		</div>
	</form>
</div>

<div style="display:none">
	<a id="openModalLink" href="#openModal"></a>
</div>

<div id="openModal" class="modalbg">
	<div class="dialog">
		<a href="#close" title="Close" class="close">X</a>
		<img src="images/conditions.jpg" alt="Terms & Conditions" />
	</div>
</div>

<!--
<div id="scannedImage" style="display:none;">
	<img src="images-slides/20140213104947335-tn.png" />
</div>
-->

  <!-- FlexSlider -->
  <script defer src="js/jquery.flexslider-min.js"></script>
<!--
  <script type="text/javascript">
    //$(function(){
    //  SyntaxHighlighter.all();
    //});
	
    $(window).load(function(){
      $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 160,
        itemMargin: 5,
        asNavFor: '#slider'
      });

      $('#slider').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: "#carousel",
        start: function(slider){
          $('body').removeClass('loading');
        }
      });
    });
	
  </script>
-->

</body>
</html>
