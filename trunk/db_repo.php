<?php
require_once('session.php');

class DatabaseRepository {
	private $dsn;
	private $username;
	private $password;
	private $result;
	
	private $driver;
	
	public function __construct() {
		date_default_timezone_set('Asia/Kuwait');
		//date_default_timezone_set('UTC');
		$ini = parse_ini_file("config.ini", true);
		$domain = $ini["defaultDomain"];
		if ($_SERVER["USERDOMAIN"] != null && (strtolower($_SERVER["USERDOMAIN"]) == "mew" || strtolower($_SERVER["USERDOMAIN"]) == "adeliya"))
			$domain = strtolower($_SERVER["USERDOMAIN"]);
			
		$this->dsn = $ini[$domain]["dsn"];
		if (!preg_match('/;$/', $this->dsn))
			$this->dsn .= ';';
			
		$this->username = $ini[$domain]["username"];
		$this->password = $ini[$domain]["password"];

		//error_log($this->dsn . " === "  . $this->username . " === "  . $this->password, 3, "error.log");
	}

	private function array_group_by( $array, $id ){
		$groups = array();
		foreach( $array as $row ) {
			$dt = strftime('%d/%m/%Y', strtotime($row -> docDate));
			$row -> docDate = $dt;
			$groups[ $row -> $id ][] = $row;
			unset($row -> $id);
		}
		return $groups;
	}

	/**
	 * Create a database connection.
	 * @return PDO  The database connection.
	 */
	//private function connect($dbName = "tawzee") {								//!!!!!!!!!!!!!!!!!!!!!!!!!!! hard coded database name
	private function connect($dbName = "") {
		//throw new Exception('Domain: ' . $_SERVER["USERDOMAIN"]);

		//error_log('dbName: ' . $this->dsn . 'dbname=' . $dbName . " \n", 3, "error.log");
/*
		if ($dbName == "")
			$dsn = $this->dsn . 'dbname=' . $dbName;
		else {
			$dsn = 
			$dsn = explode(';', $this->dsn, 2);
			error_log('json_encode($dsn): ' . json_encode($dsn) . " \n", 3, "error.log");
		
		}
*/

		$tns = "  
		(DESCRIPTION =
			(ADDRESS_LIST =
			  (ADDRESS = (PROTOCOL = TCP)(HOST = homam.mew.gov.kw)(PORT = 1521))
			)
			(CONNECT_DATA =
			  (SERVICE_NAME = tawzee)
			)
		  )
			   ";


		if (substr($this->dsn, 0, 3) != 'oci') {
			$dsn = explode(';', $this->dsn, 2);
			if ($dbName == "") {
				if ($dsn[1] == "")
					throw new Exception('No database set, failed to connect to ' . $this->dsn);
				else
					$connection = strtolower($this->dsn);
			} else
				$connection = strtolower($dsn[0]) . ';dbname=' . $dbName;
		} else
			$connection = $this->dsn;
		
		//$dbh = new PDO($this->dsn . 'dbname=' . $dbName, $this->username, $this->password);
		//$connection = "oci:dbname=" . $tns;
		
		//$connection = "oci:dbname=//homam.mew.gov.kw:1521/tawzee";
		//$this->username = "tawzee"; $this->password = "tawzee";

		//throw new Exception($connection);
			   
		try {
			$dbh = new PDO($connection, $this->username, $this->password);
		} catch (PDOException $e) {
			throw new Exception('Failed to connect to \'' .	$this->dsn . '\': '. $e->getMessage());
		}

		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		/* Ensure that we are operating with UTF-8 encoding.
		 * This command is for MySQL. Other databases may need different commands.
		 */
		$this->driver = explode(':', $this->dsn, 2);
		$this->driver = strtolower($this->driver[0]);

		//throw new Exception($driver);
		
		/* Driver specific initialization. */
		switch ($this->driver) {
			case 'mysql':
				/* Use UTF-8. */
				$dbh->exec("SET NAMES 'utf8'");
				break;
			case 'pgsql':
				/* Use UTF-8. */
				$dbh->exec("SET NAMES 'UTF8'");
				break;
			case 'oci':
				//$dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); 
				/* Use UTF-8. */
				//$dbh->exec("SET NAMES 'UTF8'");
				break;
		}
		
		return $dbh;
	}
	
	private function rebuildDMLSelect(&$st) {	// Rebuild DML Select Statement
		if ($this->driver != 'oci')
			return;
			
		$st = ltrim($st);
		$pos = strpos($st, " ") + 1;
		$pos2 = strpos($st, "FROM", $pos);
		$middle = substr($st, $pos, $pos2 - $pos - 1);
		$column_names = explode(",", $middle);
		$middle = "";
		foreach ($column_names as &$value) {
			if (stripos($value, " AS ") !== false) {
				$pos11 = strpos($value, " ");
				$pos22 = strrpos($value, " ", -1) + 1;
				if (substr($value, $pos22, 1) == "\"")
					$middle .= $value . ",";
				else
					$middle .= substr($value, 0, $pos11) . " AS \"" . substr($value, $pos22) . "\",";
				
				//throw new Exception(substr($value, $pos2, 1) . " --- " . strlen(substr($value, $pos2, 1)));
			//$val = explode('.', $value, 2);
			} else
				$middle .= $value . " AS \"" . trim($value) . "\",";
			//$middle .= $value . " AS \"" . trim(($val[1] == "") ? $val[0] : $val[1]) . "\",";
		}
		
		$st = substr($st, 0, $pos) . substr_replace($middle, " ", -1, 1) . substr($st, $pos2);
		//throw new Exception($st);
	}

	
	public function startWebServer() {
		//shell_exec('start /b /dc:\\tawsilat\\jetty\\ java -jar start.jar');
		exec('start /b /dc:\\tawsilat\\jetty\\ java -jar start.jar');
		/*
		$cmd = "c:/tawsilat/jetty/ java -jar start.jar";
		throw new Exception($cmd);
		if (substr(php_uname(), 0, 7) == "Windows"){ 
			pclose(popen("start /b /d" . $cmd, "r"));  
		} 
		else { 
			exec($cmd . " > /dev/null &");   
		} 
		*/
	}

	public function getApplicationStatus($param) {
		$dbh = $this->connect();
		//print_r($param);
		//throw new Exception("SELECT CompletionDate AS \"CompletionDate\" FROM Application WHERE ApplicationNumber = \"$param->applicationNumber\"");
		try {
			$sth = $dbh->query("SELECT CompletionDate AS \"CompletionDate\" FROM Application WHERE ApplicationNumber = \"$param->applicationNumber\"");
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			
		} catch (PDOException $e) {
			throw new Exception('Failed to prepare query: ' . $e->getMessage());
		}

		//str = print_r($param, true);
		//throw new Exception(print_r($param, true));
			
		return $row['CompletionDate'];
	}
	
	public function getUserAttributes($param) {
		$dbh = $this->connect();
		
		try {
			$sth = $dbh->prepare('SELECT loginName AS "loginName", upn AS "upn", displayName AS "displayName" FROM UserRepository WHERE loginName = :loginName');
		} catch (PDOException $e) {
			throw new Exception('Failed to prepare query: ' . $e->getMessage());
		}

		$assoc_ar = json_decode($param['loginNames'], true);
		
		$this->result = array();
				
		foreach ($assoc_ar as $key => $value) {
			foreach ($value as $key2 => $value2) {
				if ($value2 == "") {
					if (isset($_SESSION['loginName'])) {
						$loginName = $_SESSION['loginName'];
					} else {
						$loginName = 'basma';
					}
				} else {
					$loginName = $value2;
				}
				
				$logName = "'" . $loginName . "'";
				//throw new Exception($loginName);
				
				try {
					//if ($this->driver != 'oci')
						$res = $sth->execute(array('loginName' => $loginName));
						//$res = $sth->execute(array("ridavidenko"));
						//$res = $sth->execute();
					//else
					//	$res = $sth->execute(array('loginName' => "\'" . $loginName . "\'"));
				} catch (PDOException $e) {
					throw new Exception('Failed to execute query: ' . $e->getMessage());
				}

				try {
					$row = $sth->fetch(PDO::FETCH_ASSOC);
				} catch (PDOException $e) {
					throw new Exception('Failed to fetch result set: ' . $e->getMessage());
				}

				//throw new Exception("Row: " . $logName);
				
				if ($row) {
					$this->result[] = array(
						'LoginName' => $loginName,
						'DisplayName' => $row['displayName'],
						'UserPrincipalName' => $row['upn'],
					);
				}
			}
		}
			
		if (count($this->result) == 0)
			throw new Exception("1008"); 	// The user  does not exist in user repository (AD or userRepository table)

		return $this->result;
		
/*
		SimpleSAML_Logger::info('mewmodule:' . $this->authId . ': Got ' . count($data) . ' rows from database');

		if (count($data) === 0) {
			// No rows returned - invalid loginName/password.
			SimpleSAML_Logger::error('mewmodule:' . $this->authId . ': No rows in result set. Probably wrong loginName/password.');
			throw new SimpleSAML_Error_Error('WRONGUSERPASS');
		}
*/
		/* Extract attributes. We allow the resultset to consist of multiple rows. Attributes
		 * which are present in more than one row will become multivalued. NULL values and
		 * duplicate values will be skipped. All values will be converted to strings.
		 */
/*
		 $attributes = array();
		foreach ($data as $row) {
			foreach ($row as $name => $value) {

				if ($value === NULL) {
					continue;
				}

				$value = (string)$value;

				if (!array_key_exists($name, $attributes)) {
					$attributes[$name] = array();
				}

				if (in_array($value, $attributes[$name], TRUE)) {
					// Value already exists in attribute.
					continue;
				}

				$attributes[$name][] = $value;
			}
		}

		SimpleSAML_Logger::info('mewmodule:' . $this->authId . ': Attributes: ' . implode(',', array_keys($attributes)));
*/
	}

	
	public function getRowNumber($param) {
		$dbh = $this->connect();
		try {
			//$st = "SELECT id FROM Application WHERE ApplicationNumber = '2/12345'";
			$st = "SELECT ApplicationNumber AS \"ApplicationNumber\" FROM Application ORDER BY ApplicationDate desc";
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			//throw new Exception($st);
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}

		//$param['applicationNumber'] = '2/12345';
		$index = -1;
		while($r = $ds->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT, $index++)) {
			$r2 = (object)$r;
			if ($r2 -> ApplicationNumber == $param['applicationNumber']) {
				$this->result = array();
				$this->result['page'] = (int)($index / $param['rowNum']);
				$this->result['rowNumber'] = $index % $param['rowNum'];
				break;
			}
		}
		
		return $this->result;
	}	
	
	public function getApps($param) {
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$searchField = null;
		if (isset($_GET['searchField'])){ $searchField = $_GET['searchField']; }
		$searchOper = null;		// eq, bw, bn, ew, en, cn, nc, ne, lt, le, gt, ge, in, ni
		if (isset($_GET['searchOper'])){ $searchOper = $_GET['searchOper']; }

		//$searchOper = $_GET['searchOper'];	// eq, bw, bn, ew, en, cn, nc, ne, lt, le, gt, ge, in, ni
		//$searchString = $_GET['searchString'];
		
		if (isset($_GET['searchString']))
			$searchString = trim($_GET['searchString']);

		//throw new Exception('searchString: ' . $searchString);
			
		if(!$sidx) $sidx = 1;

		//$addressPieces = null;
		//if ($searchField == 'address') {
		//	$addressPieces = explode("|", $searchString);
		//}
		
		$where = "";
		switch ($searchOper) {
			case 'eq':
				$where .= "$searchField = '$searchString'";
				break;
			case 'ne':
				$where .= "$searchField <> '$searchString'";
				break;
			case 'bw':	//begin with
				$where .= "$searchField LIKE '$searchString%'";
				break;
			case 'bn':	//doesn't begin with
				$where .= "$searchField NOT LIKE '$searchString%'";
				break;
			case 'ew':	//ends with
				$where .= "$searchField LIKE '%$searchString'";
				break;
			case 'en':	//doesn't end with
				$where .= "$searchField NOT LIKE '%$searchString'";
				break;
			case 'cn':	//contains
				$where .= "$searchField LIKE '%$searchString%'";
				break;
			case 'nc':	//doesn't contain
				$where .= "$searchField NOT LIKE '%$searchString%'";
				break;
			case 'lt':	// less then
				$where .= "$searchField < '$searchString'";
				break;
			case 'le':	// less or equal
				$where .= "$searchField <= '$searchString'";
				break;
			case 'gt':	//more then
				$where .= "$searchField > '$searchString'";
				break;
			case 'ge':	//more or equal
				$where .= "$searchField >= '$searchString'";
				break;
			case 'in':	//in
				$where .= "$searchField IN($searchString)";
				break;
			case 'ni':	// not in
				$where .= "$searchField NOT IN ($searchString)";
				break;
		}
		
		//throw new Exception('Where: ' . ($where == ""));
		//throw new Exception('Where: ' . $where);

		//$dbh = $this->connect();
		$dbh = $this->connect(isset($param['dbName']) ? $param['dbName'] : '');
		
		try {
			$st = "SELECT COUNT(*) AS \"count\" FROM Application";
			
			if ($where != "")
				$st .= " WHERE " . $where;
			//throw new Exception('Statement: ' . $st);
//			الشويخ الصناعية 2 71
			
			$ds = $dbh->query($st);
			$r = $ds->fetch(PDO::FETCH_ASSOC);

			$count = $r['count'];

			if( $count > 0 ) {
				$total_pages = ceil($count/$limit);
				if ($page > $total_pages) $page = $total_pages;
			} else {
				$total_pages = 0;
			}

			$start = $limit * $page - $limit;

			$st = "SELECT ApplicationNumber AS \"ApplicationNumber\", ApplicationDate AS \"ApplicationDate\", OwnerName AS \"OwnerName\", OwnerPhone AS \"OwnerPhone\", ProjectName AS \"ProjectName\", ControlCenterId AS \"ControlCenterId\", ProjectType AS \"ProjectType\", AreaName AS \"Area\", Block AS \"Block\", Plot AS \"Plot\", ConstructionExpDate AS \"ConstructionExpDate\", FeedPoints AS \"FeedPoints\"
				FROM Application LEFT JOIN Area ON Application.AreaID = Area.ID";

			if ($where == "")
				$st .= " ORDER BY $sidx $sord";
			else
				$st .= " WHERE " . $where . " ORDER BY $sidx $sord";

			if ($this->driver != 'oci')
				$st .= " LIMIT $start, $limit";
			else {
				$st = "SELECT * FROM (
				  SELECT a.*, ROWNUM rnum FROM (
					$st
				  ) a WHERE rownum <= $start + $limit
				) WHERE rnum > $start";
			}
			//throw new Exception($start);
/*			
			if ($where == "")
				if ($this->driver != 'oci')
					$st .= " ORDER BY $sidx $sord LIMIT $start, $limit";
				else
					$st .= " ORDER BY $sidx $sord OFFSET $start ROWS FETCH NEXT $limit ROWS ONLY;";
			else
				if ($this->driver != 'oci')
					$st .= " WHERE " . $where . " ORDER BY $sidx $sord LIMIT $start, $limit";
				else
					$st .= " WHERE " . $where . " ORDER BY $sidx $sord OFFSET $start ROWS FETCH NEXT $limit ROWS ONLY;";
*/
			//throw new Exception('Statement: ' . $st);
				
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		//$this->result = array();
		if (!isset($this->result)) $this->result = new stdClass();
		$this->result->page = $page;
		$this->result->total = $total_pages;
		$this->result->records = $count;
		$this->result->userdata = $userdata;
		$i = 0;
		while($r = $ds->fetch(PDO::FETCH_ASSOC)) {
			$r2 = (object)$r;
			//throw new Exception($r2->ApplicationNumber . " --- " . $r2->ApplicationDate);
			//if ($i == 1)
				//throw new Exception($r2->OwnerName);
			if ($this->driver == 'oci')
				$r2->ApplicationDate = DateTime::createFromFormat('d-M-y', $r2->ApplicationDate)->format('Y-m-d');
			//$r2->ApplicationDate = date_format(date_create_from_format('Y-m-d', $r2->ApplicationDate), 'd/m/Y'));
			$this->result->rows[$i]['cell'] = $r2;
			//$this->result->rows[$i]['cell'] = (object)$r2;
			$i++;
		}
		return $this->result;
	}

	public function getApp($param) {	
		$dbh = $this->connect();
		try {
			//$st = "SELECT ResidenceTotalArea, ConstructionArea, ACArea, CurrentLoad, ExtraLoad, LoadAfterDelivery, ConductiveTotalLoad, FeedPoints, SiteFeedPoint,
			$st = "SELECT ResidenceTotalArea, ConstructionArea, ACArea, CurrentLoad, ExtraLoad, LoadAfterDelivery, ConductiveTotalLoad, SiteFeedPoint,
				Requirements, CableSize, Fuze, Meter, PossibilityYes, PossibilityNo, StationNumber, 
				Switch, K1000KWT, K1000AMP, K1250KWT, K1250AMP, K1600KWT, K1600AMP
				FROM Application r LEFT JOIN ApplicationDetail d ON r.ApplicationNumber = d.ApplicationNumber
				WHERE r.ApplicationNumber='{$param['applicationNumber']}'";
				

			$this->rebuildDMLSelect($st);
			//throw new Exception($st);

//								FROM ApplicationRequirements AS r LEFT JOIN ApplicationRequirementsDetail AS d ON r.ApplicationNumber = d.ApplicationNumber 

			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			//throw new Exception($st);
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();

		$index = -1;
		while($r = $ds->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT, $index++)) {
			$r2 = (object)$r;
			if ($index == 0)
				$this->result[] = array('ResidenceTotalArea' => $r2 -> ResidenceTotalArea, 'ConstructionArea' => $r2 -> ConstructionArea,
										'ACArea' => $r2 -> ACArea, 'CurrentLoad' => $r2 -> CurrentLoad, 'ExtraLoad' => $r2 -> ExtraLoad,
										'LoadAfterDelivery' => $r2 -> LoadAfterDelivery, 'ConductiveTotalLoad' => $r2 -> ConductiveTotalLoad,
										'FeedPoints' => $r2 -> FeedPoints, 'SiteFeedPoint' => $r2 -> SiteFeedPoint,
										'Requirements' => $r2 -> Requirements, 'CableSize' => $r2 -> CableSize,
										'Fuze' => $r2 -> Fuze, 'Meter' => $r2 -> Meter,
										'PossibilityYes' => $r2 -> PossibilityYes, 'PossibilityNo' => $r2 -> PossibilityNo, 'StationNumber' => $r2 -> StationNumber
				);

			if (!($r2 -> Switch == null))
				$this->result[] = array('Switch' => $r2 -> Switch, 'K1000KWT' => $r2 -> K1000KWT, 'K1000AMP' => $r2 -> K1000AMP, 
									'K1250KWT' => $r2 -> K1250KWT, 'K1250AMP' => $r2 -> K1250AMP, 'K1600KWT' => $r2 -> K1600KWT, 'K1600AMP' => $r2 -> K1600AMP);
		}
		return $this->result;
	}
	
	
	public function getLoad($param) {
		$dbh = $this->connect();
		try {
			$st = "SELECT lo.FileNumber AS FileNumber, LoadDate, Description,
				PowerFactorSummer, PowerFactorWinter, MaximumLoadsSummer, MaximumLoadsWinter,
				ConnectorLoad, SummerLoad, WinterLoad, Remarks
				FROM ApplicationLoad lo LEFT JOIN ApplicationLoadDetail d ON lo.FileNumber = d.FileNumber 
				WHERE ApplicationNumber='{$param['applicationNumber']}'";
				
			$this->rebuildDMLSelect($st);
			//throw new Exception($st);

			//$st = "SELECT ApplicationNumber, FileNumber, LoadDate FROM ApplicationLoad WHERE ApplicationNumber=555";
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			//throw new Exception($st);
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();
/*		
		$first = true;
		if ($ds->rowCount() != 0) {
			$r2 = (object)$ds->fetch(PDO::FETCH_ASSOC);
			//$r2 = (object)$r;
			$this->result[] = array('FileNumber' => $r2 -> FileNumber, 'LoadDate' => $r2 -> LoadDate);
		}
*/
		$index = -1;
		while($r = $ds->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT, $index++)) {
			$r2 = (object)$r;
			//if ($first) {
			//	$this->result[] = array('FileNumber' => $r2 -> FileNumber, 'LoadDate' => $r2 -> LoadDate);
			//	$first = false;
			//}
			if ($index == 0) {
				if ($this->driver == 'oci')
					$r2->LoadDate = DateTime::createFromFormat('d-M-y', $r2->LoadDate)->format('Y-m-d');
					
				$this->result[] = array('FileNumber' => $r2 -> FileNumber, 'LoadDate' => $r2 -> LoadDate,
										'PowerFactorSummer' => $r2 -> PowerFactorSummer, 'PowerFactorWinter' => $r2 -> PowerFactorWinter,
										'MaximumLoadsSummer' => $r2 -> MaximumLoadsSummer, 'MaximumLoadsWinter' => $r2 -> MaximumLoadsWinter
				);
			}
			
			if (!($r2 -> ConnectorLoad == null && $r2 -> SummerLoad == null && $r2 -> WinterLoad == null))
				$this->result[] = array('Description' => $r2 -> Description, 'ConnectorLoad' => $r2 -> ConnectorLoad, 
									'SummerLoad' => $r2 -> SummerLoad, 'WinterLoad' => $r2 -> WinterLoad, 'Remarks' => $r2 -> Remarks);
		}
		return $this->result;
	}	
	
	public function getAreas() {
		$dbh = $this->connect();
		try {
			$st = "SELECT ID, AreaName AS \"AreaName\" FROM Area ORDER BY AreaName ASC";
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();
		while($r = $ds->fetch(PDO::FETCH_ASSOC)) {
			$this->result[] = (object)$r;
		}
		return $this->result;
	}

	public function byFileNumber($param) {
		return $this->get($param);
	}

	public function byAddress($param) {
		return $this->get($param);
	}

	public function insertForm($param) {
		return $this->insertUpdate($param, "insert");
	}
	
	public function updateForm($param) {
		return $this->insertUpdate($param, "update");
	}
	
	function hyphensToCamel($input)
	{
		$input = preg_replace_callback('/^[a-z]/', function($match) {
			return strtoupper($match[0]);
		}, $input);

		return preg_replace_callback('/-([a-z])/', function($match) {
			return strtoupper($match[1]);
		}, $input);
	}

//	function insertUpdate($param, $op) {
	function insertUpdate($param) {
		$dbh = $this->connect();

		//if ($op == 'insert') {
			//$str = preg_replace( "/^[a-z]/", '$0', $param["schema"][2]["primary-key"] );
			//$str = $this->hyphensToCamel($param["schema"][2]["primary-key"]);
			//throw new Exception($str);
			
			//$que = $dbh->query("SELECT COUNT(*) FROM Application WHERE ApplicationNumber = {$this->hyphensToCamel($param['schema'][2]['primary-key'])}");
			//$que = $dbh->query("SELECT COUNT(*) FROM Application WHERE ApplicationNumber = {$param[$param['schema'][2]['primary-key']]}");
			

			//throw new Exception($param['schema']);
			$inserNewOrUpdateKey = false;
			if ($param['schema'] == 'main-form') {
				$inserNewOrUpdateKey = $param["application-number-old-value"] == null || $param["application-number"] != $param["application-number-old-value"];
				if ($inserNewOrUpdateKey) {		// insert new / update key
					$que = $dbh->query("SELECT COUNT(*) FROM Application WHERE ApplicationNumber = '{$param['application-number']}'");
			
			//throw new Exception("kuku");

					if ($param["application-number-old-value"] == null)		// insert record
						$param['application-number'] = "somevalue";		// will be updated soon
				}
			} else if ($param['schema'] == 'load-form') {
				$inserNewOrUpdateKey = $param["file-number"] != $param["file-number-old-value"];
				if ($inserNewOrUpdateKey)		// insert new / update key
					$que = $dbh->query("SELECT COUNT(*) FROM ApplicationLoad WHERE FileNumber = '{$param['file-number']}'");
			} else
				throw new Exception('Wrong form: ' . $param['schema']);;

			if($inserNewOrUpdateKey && $que->fetchColumn() != 0)
				throw new Exception("23000"); //"The document already exists"

			//throw new Exception($param["file-number-old-value"]);
				
			//$que = $dbh->query("SELECT LAST_INSERT_ID()");
			//$application = $dbh->lastInsertId();
			//$param['application-number'] = $que->fetchColumn();
			
			//throw new Exception($que->fetchColumn());
			//$date = new DateTime('now');
			//throw new Exception($date->format('Y-m-d'));

			//throw new Exception((new DateTime())->format('Y-m-d-H-i-s'));
			
			//if ($param['schema'] == 'main-form')
			//	$param['application-number'] = "somevalue";		// will be updated soon
				
			//$param['application-date'] = (new DateTime())->format('d/m/Y');
			//$param['application-date'] = (new DateTime('now', new DateTimeZone('Asia/Kuwait')))->format('d/m/Y');
			//throw new Exception($param['application-date']);
		//}
		
		$dbh->beginTransaction();

		$this->result = new stdClass;
		//$this->result = array({});
		//$this->result->ar = 1;
		//throw new Exception($this->result);
		
		try {
			if (isset($param['area-id'])) {
				$areaId = $param['area-id'];
				$areaName = $param['area'];
				if ($areaId == NULL && $areaName != NULL) {
					//$stArea = $dbh->prepare("INSERT INTO Area(ID, AreaName) VALUES(NULL, '{$areaName}');");
					$stArea = $dbh->prepare("INSERT INTO Area(AreaName) VALUES('$areaName')");
					$stArea->execute();

					if ($this->driver == 'oci') {
						$que = $dbh->query("SELECT Area_ID_SEQ.CURRVAL AS lastInsertID FROM DUAL");
						$areaId = $que->fetchColumn(0);
					} else
						$areaId = $dbh->lastInsertId();
						
					$param['area-id'] = $areaId;
					//$this->result = array();
					//$this->result[] = array('areaId' => $areaId);
					//$this->result = object('areaId' => $areaId);
					$this->result->areaId = $areaId;
				}
			}
/*
			$st = "SELECT ApplicationNumber, ApplicationDate, OwnerName, ProjectName, ProjectType, AreaName, Block, Plot, ConstructionExpDate 
				FROM Application LEFT JOIN Area ON application.AreaID = area.ID ";
		
			$st = "SELECT ResidenceTotalArea, ConstructionArea, ACArea, CurrentLoad, ExtraLoad, LoadAfterDelivery, ConductiveTotalLoad, FeedPoints, SiteFeedPoint,
				Requirements, CableSize, Fuze, Meter, PossibilityYes, PossibilityNo, StationNumber, 
				Switch, K1000KWT, K1000AMP, K1250KWT, K1250AMP, K1600KWT, K1600AMP
				FROM ApplicationRequirements AS r LEFT JOIN ApplicationRequirementsDetail AS d ON r.ApplicationNumber = d.ApplicationNumber 
				WHERE r.ApplicationNumber='{$param['applicationNumber']}'";
*/		

			$columnsToUpdate = "";
			$ar = array();
			$fields = "";
			$values = "";

			//throw new Exception($param['application-date']);
			
			foreach ($param as $key => $val) {
				//if ($key == 'feed-points')
				//	throw new Exception($key . " --- " . $val);

				if ($key == 'schema' || $key == 'table' || $key == 'area' || $key == 'application-number-old-value' || $key == 'file-number-old-value')
					continue;

				//if ($this->driver == 'oci' && $key == 'application-number' && $val == null)
				//	continue;
					
				if ($key == 'application-date' || $key == 'load-date') {
					if ($param[$key] != '')
						if ($this->driver == 'oci')
							$val = DateTime::createFromFormat('d/m/Y', $param[$key])->format('d-M-y');
						else
							$val = DateTime::createFromFormat('d/m/Y', $param[$key])->format('Y-m-d');
					else
						$val = null;
				}
				
				//if ($param[$key] != '') {
					//$key = str_replace('-', '', $key);
					$key = $this->hyphensToCamel($key);

					$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . $key . " = '{$val}'";
					$fields .= ($fields == "" ? '' : ',') . $key;
					$values .= ($values == "" ? ':' : ',:') . $key;
					$ar[$key] = $val;
					
					
//INSERT INTO table (id, data) VALUES(uid_seq.NEXTVAL, 255); followed by SELECT uid_seq.CURRVAL AS lastInsertID FROM DUAL; would also be perfectly valid.... but LASTVAL doesn't exist –  Mark Baker Feb 17 					
					
					//$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . $key . " = '{$val}'";
					//$fields .= ($fields == "" ? '' : ',') . $key;
					//$values .= ($values == "" ? ':' : ',:') . $key;
					//$ar[$key] = $val;
				//}

				//if ($key == 'applicationdate')
					//break;
			} 			

			if ($param['schema'] == 'main-form') {
				//if ($op == 'insert') {
				if ($param["application-number-old-value"] == "") {		// insert new
					//if ($this->driver == 'oci') {
					//	$fields .= ($fields == "" ? '' : ',') . 'ID';
					//	$values .= ($values == "" ? '' : ',') . 'APPLICATION_ID_SEQ.NEXTVAL';
					//	$fields .= ($fields == "" ? '' : ',') . 'APPLICATIONNUMBER';
					//	$values .= ($values == "" ? '' : ',') . 'APPLICATION_ID_SEQ.NEXTVAL';
					//}
					
					$st = "INSERT INTO Application (" . $fields . ")" . "VALUES (" . $values . ")";
					//throw new Exception($st);

					$ds = $dbh->prepare($st);
					$ds->execute($ar);

					if ($this->driver == 'oci' && $param['schema'] == 'main-form') {
						$que = $dbh->query("SELECT APPLICATION_ID_SEQ.CURRVAL AS lastInsertID FROM DUAL");
						$lastInsertId = $que->fetchColumn(0);
						$st = "UPDATE Application SET ApplicationNumber = '{$lastInsertId}' WHERE id = '{$lastInsertId}'";
					} else if ($this->driver == 'oci' && $param['schema'] == 'load-form') {
						$que = $dbh->query("SELECT APPLICATIONLOAD_ID_SEQ.CURRVAL AS lastInsertID FROM DUAL");
						$lastInsertId = $que->fetchColumn(0);
						$st = "UPDATE Application SET ApplicationNumber = '{$lastInsertId}' WHERE id = '{$lastInsertId}'";
					} else {
						$lastInsertId = $dbh->lastInsertId();
						$st = "UPDATE Application SET ApplicationNumber = LAST_INSERT_ID() WHERE id = LAST_INSERT_ID()";
					}

					$ds = $dbh->prepare($st);
					$ds->execute();
					
					//throw new Exception($st);

					//$lastInsertId = $dbh->lastInsertId();

					//$st = "UPDATE Application SET ApplicationNumber = LAST_INSERT_ID() WHERE id = LAST_INSERT_ID()";
					//$ds = $dbh->prepare($st);
					//$ds->execute();
					$this->result->applicationNumber = $lastInsertId;
					$this->result->applicationDate = DateTime::createFromFormat('d/m/Y', $param['application-date'])->format('Y-m-d');

					
					//if ($this->driver == 'oci')
					//	$this->result->applicationDate = DateTime::createFromFormat('d-M-y', $param['application-date'])->format('Y-m-d');
					//else
					//	$this->result->applicationDate = DateTime::createFromFormat('d/m/Y', $param['application-date'])->format('Y-m-d');
					
					
					//$this->result = array('applicationNumber' => $lastInsertId);

				} else {
					if ($inserNewOrUpdateKey)		// update key
						$st = "DELETE FROM ApplicationDetail WHERE ApplicationNumber = '{$param['application-number-old-value']}'";
					else
						$st = "DELETE FROM ApplicationDetail WHERE ApplicationNumber = '{$param['application-number']}'";
						
					$ds = $dbh->prepare($st);
					$ds->execute();
			//throw new Exception($st);

					//$st = "INSERT INTO Application (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
					if ($inserNewOrUpdateKey)		// update key
						$st = "UPDATE Application SET " . $columnsToUpdate . " WHERE ApplicationNumber = '{$param['application-number-old-value']}'";
					else
						$st = "UPDATE Application SET " . $columnsToUpdate . " WHERE ApplicationNumber = '{$param['application-number']}'";

					$ds = $dbh->prepare($st);
					$ds->execute();
					
					//$this->createOwnerSignature($dbh, $param["owner-name"], $param["owner-phone"]);
					//$this->stampOwnerSignature($dbh, $param["application-number"], $param["owner-phone"]);
					
					
				}
			} else if ($param['schema'] == 'load-form') {
				if ($param["file-number-old-value"] == "") {		// insert new
					//$st = "INSERT INTO ApplicationLoad (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
					if ($this->driver == 'oci') {
						$fields .= ($fields == "" ? '' : ',') . 'ID';
						$values .= ($values == "" ? '' : ',') . 'APPLICATIONLOAD_ID_SEQ.NEXTVAL';
						$fields .= ($fields == "" ? '' : ',') . 'APPLICATIONNUMBER';
						$values .= ($values == "" ? '' : ',') . 'APPLICATIONLOAD_ID_SEQ.NEXTVAL';
					} 
					
					$st = "INSERT INTO ApplicationLoad (" . $fields . ")" . "VALUES (" . $values . ")";
					
					$ds = $dbh->prepare($st);
					$ds->execute($ar);
				} else {
					if ($inserNewOrUpdateKey)		// update key
						$st = "DELETE FROM ApplicationLoadDetail WHERE FileNumber = '{$param['file-number-old-value']}'";
					else
						$st = "DELETE FROM ApplicationLoadDetail WHERE FileNumber = '{$param['file-number']}'";
						
					$ds = $dbh->prepare($st);
					$ds->execute();

					if ($inserNewOrUpdateKey)		// update key
						$st = "UPDATE ApplicationLoad SET " . $columnsToUpdate . " WHERE FileNumber = '{$param['file-number-old-value']}'";
					else
						$st = "UPDATE ApplicationLoad SET " . $columnsToUpdate . " WHERE FileNumber = '{$param['file-number']}'";

					$ds = $dbh->prepare($st);
					$ds->execute();
				}
			} else
				throw new Exception('Wrong form: ' . $param['schema']);;
				
			//throw new Exception($st);
/*			
			if ($st != "") {
				$ds = $dbh->prepare($st);
				//$ds->execute();
				$ds->execute($ar);
			}
*/			
/*		
			if ($param['schema'] == 'main-form')
				$st = "DELETE FROM ApplicationDetail WHERE ApplicationNumber = '{$param['application-number']}'";
			else if ($param['schema'] == 'load-form')
				$st = "DELETE FROM ApplicationLoadDetail WHERE FileNumber = '{$param['file-number']}'";
				
			//throw new Exception($st);
			$ds = $dbh->prepare($st);
			$ds->execute($ar);
*/			

			//throw new Exception(count($param['table']));
			for ($i = 0; $i < count($param['table']); $i++) {
				//$columnsToUpdate = "";
				//$ar2 = array();
				if ($param['schema'] == 'main-form') {
					//$ar = ["ApplicationNumber" => $param['application-number']];		//PHP version issues
					$ar2["ApplicationNumber"] = $param['application-number'];
					$ar = $ar2;
					$fields = "ApplicationNumber";
					$values = ":ApplicationNumber";
				} else if ($param['schema'] == 'load-form') {
					//$ar = ["FileNumber" => $param['file-number']];		//PHP version issues
					$ar2["FileNumber"] = $param['file-number'];
					$ar = $ar2;
					$fields = "FileNumber";
					$values = ":FileNumber";
				}
				
				foreach ($param['table'][$i] as $key => $val) {
					//throw new Exception($param['table'][$i][$key] . " --- " . $val);
					//throw new Exception($key . " --- " . $val);
				
					if ($val != '') {
						//$key = str_replace('-', '', $key);
						$key = $this->hyphensToCamel($key);
						//$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . $key . " = '{$val}'";
						$fields .= ($fields == "" ? '' : ',') . $key;
						$values .= ($values == "" ? ':' : ',:') . $key;
						$ar[$key] = $val;
					}
				
				}
				if ($param['schema'] == 'main-form')
					$st = "INSERT INTO ApplicationDetail (" . $fields . ")" . "VALUES (" . $values . ")";
				else if ($param['schema'] == 'load-form')
					$st = "INSERT INTO ApplicationLoadDetail (" . $fields . ")" . "VALUES (" . $values . ")";
					//throw new Exception(print_r($ar));

				//throw new Exception($st);
					
				$ds = $dbh->prepare($st);
				$ds->execute($ar);

				//throw new Exception(print_r($ar));
				//throw new Exception($st);

			}
						
			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
		
			//if ((int)$e->getCode() == 23000)
			//	throw new Exception("23000"); //"The document $param[docFileNumber] or $param[originFileNumber] already exists"
			//else
				throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
			//1062 Duplicate entry '12348' for key 
		}
		
		//$this->result = null;
		
		return $this->result;
	}
	
	public function getActors() {
		$dbh = $this->connect();

		//$driver = explode(':', $this->dsn, 2);
		//$driver = strtolower($driver[0]);
		
		$nullfirst = "";
		if ($this->driver == 'oci')
			$nullfirst = " NULLS FIRST";
		
		try {
			//$ds = $dbh->query("SELECT o.OfficeId, Name, ArabicName, EmployeeId, ManagerId, Director FROM OfficeList AS o RIGHT OUTER JOIN EmployeeList AS e ON o.OfficeId = e.OfficeId ORDER BY e.OfficeId, e.ManagerId");
			$ds = $dbh->query("SELECT o.OfficeId AS \"OfficeId\", Name AS \"Name\", ArabicName AS \"ArabicName\", MemberOf AS \"MemberOf\", EmployeeId AS \"EmployeeId\", ManagerId AS \"ManagerId\", Director AS \"Director\" FROM OfficeList o RIGHT OUTER JOIN EmployeeList e ON o.OfficeId = e.OfficeId ORDER BY e.OfficeId ASC " . $nullfirst . ", e.ManagerId ASC " . $nullfirst);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute query: ' . $e->getMessage());
		}
			
		//throw new Exception("SELECT o.OfficeId AS \"OfficeId\", Name AS \"Name\", ArabicName AS \"ArabicName\", EmployeeId AS \"EmployeeId\", ManagerId AS \"ManagerId\", Director AS \"Director\" FROM OfficeList o RIGHT OUTER JOIN EmployeeList e ON o.OfficeId = e.OfficeId ORDER BY e.OfficeId ASC " . $nullfirst . ", e.ManagerId ASC " . $nullfirst);
			
		try {
			$data = $ds->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception('Failed to fetch result set: ' . $e->getMessage());
		}

		try {
			$ds = $dbh->query("SELECT o.OfficeId AS \"OfficeId\", Name AS \"Name\", ArabicName AS \"ArabicName\", MemberOf AS \"MemberOf\" FROM OfficeList o LEFT OUTER JOIN EmployeeList e ON o.OfficeId = e.OfficeId WHERE e.OfficeId IS NULL ORDER BY e.OfficeId ASC " . $nullfirst);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute query: ' . $e->getMessage());
		}
		
		try {
			$data2 = $ds->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception('Failed to fetch result set: ' . $e->getMessage());
		}
		
		//$case = $dbh->getDbConnection()->getPdoInstance()->getAttribute(PDO::ATTR_CASE); 		
		//throw new Exception($case);
		foreach ($data as $row) {
			$r = (object)$row;
			
			//$str = print_r($row, true);
			//throw new Exception($str);
			
			if ($r -> Director == 1)
				$directors[] = $r -> EmployeeId;

			//$str = print_r($r, true);
			//throw new Exception($str);
				
			if ($r -> OfficeId == null)
				continue;

			if ($r -> ManagerId == null) {
				if (!isset($ar[$r -> OfficeId]))
					$ar[$r -> OfficeId] = array('name' => $r -> Name, 'arname' => $r -> ArabicName, 'memberof' => $r -> MemberOf);
				$ar[$r -> OfficeId][$r -> EmployeeId] = array();
			} else
				$ar[$r -> OfficeId][$r -> ManagerId][] = $r -> EmployeeId;
		}

		foreach ($data2 as $row) {
			$r = (object)$row;

			if (!isset($ar[$r -> OfficeId]))
				$ar[$r -> OfficeId] = array('name' => $r -> Name, 'arname' => $r -> ArabicName, 'memberof' => $r -> MemberOf);
		}
		
/*
		$ar = 	array(
					0 => array(
						'ali' => array(	
							'abdalla',
							'amr'
						),
						'ahmed' => array(	
						),
					),
					2 => array(
						'hesham' => array(	
							'husain',
							'hosam'
						),
					)
				);
						
		$this->result = $ar;
		
	//	"Array
	//	(
	//		[0] => Array
	//			(
	//				[ali] => Array
	//					(
	//						[0] => amr
	//						[1] => abdalla
	//					)

	//				[ahmed] => Array
	//					(
	//					)

	//			)

	//		[1] => Array
	//			(
	//				[bader] => Array
	//					(
	//						[0] => basma
	//					)

	//			)		
	//	.....................		
		
*/		
		//$str = print_r($ar, true);
		//throw new Exception($str);
		//throw new Exception((string)count($ar));
		
		
		$w = new XMLWriter();
		$w->openMemory();
		//$w->startDocument('1.0','UTF-8');
		$w->startElement("department");
		$w->writeAttribute("directors", implode(",", $directors));
		$w->startElement("sections");
		foreach ($ar as $key => $value) {
			$w->startElement("section");
			$w->writeAttribute("id", $key);
			$w->writeAttribute("name",  $value['name']);
			$w->writeAttribute("arname",  $value['arname']);
			$w->writeAttribute("memberof",  $value['memberof']);
			$w->startElement("managers");
			foreach ($value as $key2 => $value2) {
				if ($key2 == 'name' || $key2 == 'arname' || $key2 == 'memberof')
					continue;
				$w->startElement("manager");
				$w->writeAttribute("name", $key2);
				foreach ($value2 as $value3) {
					$w->startElement("employee");
					$w->text($value3);
					$w->endElement();
				}
				$w->endElement();
			}
			$w->endElement();
			
			$w->endElement();
		}
		$w->endElement();
		
		$w->startElement("employees");
		foreach ($data as $row) {
			$r = (object)$row;
			$w->startElement("employee");
			$w->text($r -> EmployeeId);
			$w->endElement();
		}		
		$w->endElement();
		
		$w->endElement();
		
		//$file = fopen("actors_clone.xml","w");
		//fwrite($file, $w->outputMemory(true));
		//fclose($file);
		
		return $w->outputMemory(true);
	}
	
	public function saveActors($param) {
		$dbh = $this->connect();
		//$dbh->setAttribute("PDO::ATTR_ERRMODE", PDO::ERRMODE_EXCEPTION); 
		
		$xml = simplexml_load_string($param);

		$directors = explode(",", $xml['directors']);
		//throw new Exception('Failed to execute/prepare query: ' . $director);
		
		//$file = fopen("act.xml","w");
		//fwrite($file, $param);
		//fclose($file);

		//$xml = simplexml_load_file("act.xml");
		
		//throw new Exception('Failed to execute/prepare query: ' . $xml->sections->section[0]['arName']);
		//throw new Exception('Failed to execute/prepare query: ' . $xml['superuser']);
		
		$st = "delete from EmployeeList";
		$st .= ";delete from OfficeList";
		
		$director_val = 0;
		foreach ($xml->employees->employee as $employee) {
			$director_val = 0;
			if (in_array($employee, $directors))
				$director_val = 1;
			
			$st .= ";INSERT INTO EmployeeList (EmployeeId, Director) VALUES ('{$employee}', $director_val)";

			//$st .= ";INSERT INTO EmployeeList (EmployeeId) VALUES ('{$employee}')";
		//throw new Exception('Failed to execute/prepare query: ' . $st);
		}

		//$ds = $dbh->prepare($st);
		//$ds->execute();
		
		//$st = "";
		foreach ($xml->sections->section as $section) {
			if ($section['id'] == 0 || $section['id'] == NULL)
				throw new Exception('Failed to execute/prepare query: Office ID - 0 Or NULL is not allowed');
			$st .= ";INSERT INTO OfficeList (OfficeId, Name, ArabicName, MemberOf) VALUES ('{$section['id']}', '{$section['name']}', '{$section['arname']}', '{$section['memberof']}')";
			foreach ($section->managers->manager as $manager) {
				//$superuserval = 0;
				//if (in_array($manager['name'], $superusers))
				//	$superuserval = 1;
			
				//$st .= ";UPDATE EmployeeList SET OfficeId = {$section['id']}, ManagerId = NULL, SuperUser = $superuserval WHERE EmployeeId = '{$manager['name']}'";
				$st .= ";UPDATE EmployeeList SET OfficeId = {$section['id']}, ManagerId = NULL WHERE EmployeeId = '{$manager['name']}'";
				foreach ($manager->employee as $employee) {
					$st .= ";UPDATE EmployeeList SET OfficeId = {$section['id']}, ManagerId = '{$manager['name']}' WHERE EmployeeId = '{$employee}'";
				}
			}
		}

		$statements = explode(";", $st);
		//throw new Exception('Failed to execute/prepare query: ' . print_r($statements, false));
		try {
			$dbh->beginTransaction();
			foreach( $statements as $statement ) {
				$ds = $dbh->exec($statement);
			}
			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		return $this->result;		
	}
	
	public function createOU($param) {
		$dbh = $this->connect();

		try {
			$prest = $dbh->prepare("INSERT INTO OfficeList(Name, ArabicName, MemberOf) VALUES('{$param['name']}', '{$param['name']}', '{$param['memberof']}');");
		//throw new Exception("INSERT INTO OfficeList() VALUES('{$param['name']}', '{$param['name']}');");
			$prest->execute();
			$this->result[] =  $dbh->lastInsertId();
		} catch (PDOException $e) {
			$dbh->rollBack();
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		return $this->result;		
	}

	public function approve($param) {
		$dbh = $this->connect();

		if ($this->driver == 'oci')
			$dt = (new DateTime('now', new DateTimeZone('Asia/Kuwait')))->format('d-M-y');
		else
			$dt = (new DateTime('now', new DateTimeZone('Asia/Kuwait')))->format('Y-m-d');

		//throw new Exception("UPDATE Application SET CompletionDate = '{$dt}' WHERE ApplicationNumber = '{$param['application-number']}'");
			
		try {
			$dbh->exec("UPDATE Application SET CompletionDate = '{$dt}' WHERE ApplicationNumber = '{$param['application-number']}'");
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
	
	public function delete($param) {
		$dbh = $this->connect();

		try {
			if ($param['schema'] == 'main-form')
				$dbh->exec("DELETE FROM Application WHERE ApplicationNumber = '{$param['application-number']}'");
			else if ($param['schema'] == 'load-form')
				$dbh->exec("DELETE FROM ApplicationLoad WHERE FileNumber = '{$param['file-number']}'");
			else
				throw new Exception('Wrong form: ' . $param['schema']);;
		
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
	
	public function getAttachmentList($param) {
		$dbh = $this->connect();

		try {
			$st = "SELECT ID, Title AS \"Title\" FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'";
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();
		while($r = $ds->fetch(PDO::FETCH_ASSOC)) {
			$this->result[] = (object)$r;
		}

		return $this->result;

	}

	private function setGlobal($param) {
		apc_store('myob', $param);
//	$ob = apc_fetch("myob");
//error_log("OB_F: " . (string)($ob) . PHP_EOL, 3, "error.log");
		
/*
		session_id("g11");
		$started = session_start();

		error_log("Started: " . (string)($started == true) . PHP_EOL, 3, "error.log");

		$_SESSION["myob"] = $param;
		session_write_close();
*/		
	}
	
	
	private function curl_post($param) {
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, "http://tawzee:8084/SSE/sse?PostData=true");
		curl_setopt($ch, CURLOPT_URL, "http://tawzee:8084/SSE/sse");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . $param);
		curl_exec ($ch);
		curl_close ($ch);

		//throw new Exception(print_r(curl_getinfo($ch), true));
		
	}
	
	public function setOwnerSignature($param) {
		$dbh = $this->connect();

		try {
			//print_r($param);
			//var_dump($param);
			//error_log("setSignature: " . (string)($param->setSignature = NULL) . PHP_EOL, 3, "error.log");

			if ($param->setSignature != NULL)
				$dbh->exec("UPDATE Application SET OwnerSignature = 1 WHERE ApplicationNumber = \"$param->applicationNumber\" AND OwnerPhone = \"$param->ownerPhone\"");
			else
				$dbh->exec("UPDATE Application SET OwnerSignature = 0 WHERE ApplicationNumber = \"$param->applicationNumber\" AND OwnerPhone = \"$param->ownerPhone\"");
			
			//setGlobal(array('op' => 'setOwnerSignature', 'date' => (new DateTime('now', new DateTimeZone('Asia/Kuwait')))->format('Y-m-d')));
			//$this->setGlobal(sprintf('data: {"op" : "setOwnerSignature", "date" : "%s"}', date('d/m/Y H:i:s')));
			$this->curl_post(sprintf('{"op" : "setOwnerSignature", "date" : "%s"}', date('d/m/Y H:i:s')));
			//printf ('data: {"opid" : "approved", "time" : "%s"}' . "\n", date('d/m H:i:s'));	

		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
	
	public function stampOwnerSignature($dbh, $applicationnumber, $ownerphone) {
		if ($this->driver == 'oci') {
			$dateColumn = "Date_";
			$dt = (new DateTime('now', new DateTimeZone('Asia/Kuwait')))->format('d-M-y');
		} else {
			$dateColumn = "Date";
			$dt = (new DateTime('now', new DateTimeZone('Asia/Kuwait')))->format('Y-m-d');
		}
		
		//$dbh = $this->connect();
		
		try {
			$ds = $dbh->query("SELECT ID, Width FROM SignatureList WHERE EmployeeId = '$ownerphone'");
			$r = (object)$ds->fetch(PDO::FETCH_ASSOC);
			$signatureid = $r->ID;
			$leftPos = 595 / 2 - $r->Width / 2 + 40;

			$ds = $dbh->query("SELECT ApplicationNumber FROM ApplicationSignature WHERE ApplicationNumber = '$applicationnumber' AND SignatureID = $signatureid");
			if($ds->fetchColumn() == 0)
				$prest = $dbh->exec("INSERT INTO ApplicationSignature(ApplicationNumber, SignatureID, EmployeeName, $dateColumn, TopPos, LeftPos) VALUES ('$applicationnumber', $signatureid, '$ownerphone', '$dt', 630, $leftPos)");
				//throw new Exception("INSERT INTO ApplicationSignature(ApplicationNumber, SignatureID, EmployeeName, $dateColumn, TopPos, LeftPos) VALUES ('$applicationnumber', '$signatureid', '$ownerphone', '$dt', '20', '20')");
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
	
	public function createOwnerSignature($dbh, $ownername, $ownerphone) {
		require('./I18N/Arabic.php'); 
		$Arabic = new I18N_Arabic('Glyphs');
		//$font = 'C:\Windows\Fonts\arabtype.ttf';
		$font = '.\arial.ttf';
		
		$fontsize = 14;
		$text = $Arabic->utf8Glyphs($ownername);
		//$text = "Abdalla Al Salem";
		
		// First we create our bounding box for the first text
		$bbox = imagettfbbox($fontsize, 0, $font, $text);
		//throw new Exception(print_r($bbox, true));

		// This is our cordinates for X and Y - center of the image created by imagecreatetruecolor function
		//$x = $bbox[0] + (imagesx($img) / 2) - ($bbox[4] / 2) - 25;
		//$y = $bbox[1] + (imagesy($img) / 2) - ($bbox[5] / 2) - 5;
		
		$width = $bbox[4] - $bbox[0] + 10;
		$y = $bbox[1] - $bbox[5];
		//$height = $y + 40;
		$height = $y + 10;
		//throw new Exception($width . " ----- " . $height);
		
		//$width = 184;
		//$y = $height = 24;
		$img = imagecreatetruecolor($width, $height);
		imagesavealpha($img, true);

		$trans_colour = imagecolorallocatealpha($img, 0, 0, 0, 127);
		//$trans_colour = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $trans_colour);
		
		//$white = imagecolorallocate( $img, 255, 255, 255 );
		//$grey = imagecolorallocate( $img, 128, 128, 128 );
		//$black = imagecolorallocate( $img, 0, 0, 0 );
		$blue = imagecolorallocate( $img, 0, 0, 255 );

		// Add the text
		imagettftext($img, $fontsize, 0, 0, $y, $blue, $font, $text);

		//$img2 = imagecreatetruecolor($width, $height);
		//imagesavealpha($img2, true);
		//imagefill($img2, 0, 0, $trans_colour);

		//imagecopyresampled($img2, $img, 0, 0, 0, 0, $width, $height, $width * 8, $height * 8);
		
		imagepng( $img, "./my_image.png", 2 );
		imagejpeg( $img, "./my_image.jpg", 2 );
		//$size = getimagesize("./my_image.png", $info);
		//throw new Exception(print_r($size, true));
		
		ob_start();
		imagepng( $img, NULL, 2);
		$imgData = ob_get_contents();
		ob_end_clean();
		
		//$dbh = $this->connect();
		
		try {
			$ds = $dbh->query("SELECT EmployeeId FROM SignatureList WHERE EmployeeId = '$ownerphone'");
			if($ds->fetchColumn() == 0)
				$dbh->exec("INSERT INTO SignatureList(EmployeeId, Image, Width, Height, Resolution) VALUES ('$ownerphone', " . $dbh->quote($imgData) . ", $width, $height, 96)");
			else
				$dbh->exec("UPDATE SignatureList SET Image = " . $dbh->quote($imgData) . ", Width = $width, Height = $height WHERE EmployeeId = '$ownerphone'");
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
//		finally {
			imagecolordeallocate( $img, $trans_colour );
			//imagecolordeallocate( $img, $white );
			//imagecolordeallocate( $img, $grey );
			//imagecolordeallocate( $img, $black );
			imagecolordeallocate( $img, $blue );
			imagedestroy( $img );	
			imagedestroy( $img2 );	
//		}
	}
	
	public function getUserSignatureList($param) {
		$dbh = $this->connect();

		//throw new Exception($param['currentuser']);

		try {
			$st = "SELECT ID, Width, Height, Resolution FROM SignatureList WHERE EmployeeId='{$param['currentuser']}'";
			$this->rebuildDMLSelect($st);
			
			//$st = "SELECT ID, Image, Width, Height, Resolution FROM SignatureList WHERE EmployeeId='{$param['currentuser']}'";
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();
		while($r = $ds->fetch(PDO::FETCH_ASSOC)) {
			$this->result[] = (object)$r;
		}

		//throw new Exception((string)count($this->result[0]));
		//throw new Exception(print_r($this->result, false));
		
		return $this->result;

	}	

	public function getStampedSignatures($param) {
		$dbh = $this->connect();

		//throw new Exception($param['currentuser']);
		if ($param['schema'] == 'main-form')
			$tableName = "ApplicationSignature";
		else
			$tableName = "ApplicationLoadSignature";
		
		if ($this->driver == 'oci')
			$dt = "Date_";
		else
			$dt = "Date";
		
		try {
			$st = " SELECT SignatureID, EmployeeName, $dt AS \"Date\", TopPos, LeftPos, Width, Height, Resolution FROM {$tableName} INNER JOIN SignatureList ON SignatureID = ID WHERE 
					{$this->hyphensToCamel($param['data-key-field'])} = '{$param['data-key-field-val']}' ";
			
			$this->rebuildDMLSelect($st);

			//throw new Exception($st);
			
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();
		while($r = $ds->fetch(PDO::FETCH_ASSOC)) {
			$r2 = (object)$r;
			if ($this->driver == 'oci')
				$r2->Date = DateTime::createFromFormat('d-M-y', $r2->Date)->format('Y-m-d');
		
			$this->result[] = $r2;
		}

		return $this->result;

	}	
	
	public function saveSignature($param) {
		$dbh = $this->connect();
		
		try {
			$columnsToUpdate = "";
			$ar = array();
			$fields = "";
			$values = "";

			$param[$param['data-key-field']] = $param['data-key-field-val'];
			//throw new Exception(print_r($param));
			
			//throw new Exception($param['application-date']);
			
			foreach ($param as $key => $val) {
				//throw new Exception($key . " --- " . $val);
			
				if ($key == 'schema' || $key == 'data-key-field' || $key == 'data-key-field-val')
					continue;

				//if ($param[$key] != '') {
					$key = $this->hyphensToCamel($key);
					
					if (!($this->driver == 'oci' && ($key == $this->hyphensToCamel($param['data-key-field']) || $key == "SignatureId")))
						$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . $key . " = '{$val}'";
						
					$fields .= ($fields == "" ? '' : ',') . $key;
					$values .= ($values == "" ? ':' : ',:') . $key;
					$ar[$key] = $val;
				//}
			} 			

			if ($this->driver != 'oci') {
				$columnsToUpdate .= ", Date = '" . date('Y-m-d') . "'";
				$fields .= ",Date";
				$values .= ",:Date";
				$ar["Date"] = date('Y-m-d');
			} else {
				$columnsToUpdate .= ", Date_ = '" . date('d-M-y') . "'";
				$fields .= ",Date_";
				$values .= ",:Date_";
				$ar["Date_"] = date('d-M-y');
			}

			if ($param['schema'] == 'main-form')
				$tableName = "ApplicationSignature";
			else
				$tableName = "ApplicationLoadSignature";
			
			//error_log(print_r($columnsToUpdate, true));
			//throw new Exception(print_r($ar, true));
			
			//if ($param['schema'] == 'main-form') {
				if ($this->driver != 'oci')
					$st = "INSERT INTO {$tableName} (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
					//INSERT INTO ApplicationSignature (SignatureId,EmployeeName,TopPos,LeftPos,ApplicationNumber,Date)VALUES (:SignatureId,:EmployeeName,:TopPos,:LeftPos,:ApplicationNumber,:Date) ON DUPLICATE KEY UPDATE SignatureId = '6',EmployeeName = 'Roman Davidenko',TopPos = '712.066650390625',LeftPos = '227',ApplicationNumber = '2/12345', Date = '2015-02-02'				
				else
					$st = "MERGE INTO {$tableName} USING dual ON ({$this->hyphensToCamel($param['data-key-field'])} = '{$param['data-key-field-val']}' AND SignatureId = '{$ar['SignatureId']}') " .
						" WHEN MATCHED THEN UPDATE SET " . $columnsToUpdate .
						" WHEN NOT MATCHED THEN INSERT (" . $fields . ")" . "VALUES (" . $values . ")";
						//MERGE INTO ApplicationSignature USING dual ON (ApplicationNumber = '2/12345' AND SignatureId = '6') WHEN MATCHED THEN UPDATE SET EmployeeName = 'Roman Davidenko',TopPos = '40.06666564941406',LeftPos = '219', Date_ = '03-Feb-15' WHEN NOT MATCHED THEN INSERT (SignatureId,EmployeeName,TopPos,LeftPos,ApplicationNumber,Date_)VALUES (:SignatureId,:EmployeeName,:TopPos,:LeftPos,:ApplicationNumber,:Date_)
						//MERGE INTO ApplicationSignature using dual on (ApplicationNumber = '2/12345' AND SignatureId = '6') WHEN MATCHED THEN UPDATE SET EmployeeName = 'Roman Davidenko',TopPos = '105.06666564941406',LeftPos = '77', Date_ = '03-Feb-15' WHEN NOT MATCHED THEN INSERT (SignatureId,EmployeeName,TopPos,LeftPos,ApplicationNumber,Date_)VALUES ('6','Roman Davidenko','86.06666564941406','66','2/12345','03-Feb-15')
			//} else if ($param['schema'] == 'load-form')
			//	$st = "INSERT INTO ApplicationLoadSignature (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
			//else
			//	throw new Exception('Wrong form: ' . $param['schema']);;
				
			//throw new Exception($st);
				
			$ds = $dbh->prepare($st);
			$ds->execute($ar);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		//$this->result = null;
		
		return $this->result;
	}

	public function deleteSignature($param) {
		$dbh = $this->connect();

		if ($param['schema'] == 'main-form')
			$tableName = "ApplicationSignature";
		else
			$tableName = "ApplicationLoadSignature";
			
		try {
			//throw new Exception(" DELETE FROM {$tableName} WHERE 
			$dbh->exec(" DELETE FROM {$tableName} WHERE 
					{$this->hyphensToCamel($param['data-key-field'])}  =  '{$param['data-key-field-val']}'  AND
					SignatureID = '{$param['signature-id']}' ");
				
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}

		return $this->result;
	}
}

?>
