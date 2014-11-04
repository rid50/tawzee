<?php
class DatabaseRepository {
	private $dsn;
	private $username;
	private $password;
	private $result;
	
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

		/* Ensure that we are operating with UTF-8 encoding.
		 * This command is for MySQL. Other databases may need different commands.
		 */
		$driver = explode(':', $this->dsn, 2);
		$driver = strtolower($driver[0]);

		//throw new Exception($driver);
		
		/* Driver specific initialization. */
		switch ($driver) {
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
	
	public function getUserAttributes($param) {
		$dbh = $this->connect();
		
		try {
		
			//$ds = $dbh->query("SELECT loginName FROM userRepository WHERE loginName = :loginName'); 
		
			$sth = $dbh->prepare('SELECT loginName, upn, displayName FROM UserRepository WHERE loginName = :loginName');
		} catch (PDOException $e) {
			throw new Exception('Failed to prepare query: ' . $e->getMessage());
		}

		//throw new Exception('SELECT loginName, upn, displayName FROM UserRepository WHERE loginName = :loginName');
		
		//if (isset($_POST['loginNames']))
		//	$assoc_ar = json_decode($_POST['loginNames'], true);
		//else
		//	$assoc_ar = json_decode($_GET['loginNames'], true);

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
				
				try {
					$res = $sth->execute(array('loginName' => $loginName));
				} catch (PDOException $e) {
					throw new Exception('Failed to execute query: ' . $e->getMessage());
				}

				try {
					$row = $sth->fetch(PDO::FETCH_ASSOC);
				} catch (PDOException $e) {
					throw new Exception('Failed to fetch result set: ' . $e->getMessage());
				}
				
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
			$st = "SELECT COUNT(*) AS count FROM Application";
			
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

			$st = "SELECT ApplicationNumber, ApplicationDate, OwnerName, ProjectName, ControlCenterId, ProjectType, AreaName AS Area, Block, Plot, ConstructionExpDate, FeedPoints
				FROM Application LEFT JOIN Area ON Application.AreaID = Area.ID ";

				if ($where == "")
				$st .= " ORDER BY $sidx $sord LIMIT $start, $limit";
			else
				$st .= " WHERE " . $where . " ORDER BY $sidx $sord LIMIT $start, $limit";
				
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
			//$r2 = (object)$r;
			//if ($i == 1)
				//throw new Exception($r2->OwnerName);
			$this->result->rows[$i]['cell'] = (object)$r;
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
				FROM Application AS r LEFT JOIN ApplicationDetail AS d ON r.ApplicationNumber = d.ApplicationNumber
				WHERE r.ApplicationNumber='{$param['applicationNumber']}'";

//								FROM ApplicationRequirements AS r LEFT JOIN ApplicationRequirementsDetail AS d ON r.ApplicationNumber = d.ApplicationNumber 

			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			//throw new Exception($st);
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();

		$index = -1;
		while($r = $ds->fetch(PDO::FETCH_ASSOC, $index++)) {
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
			$st = "SELECT l.FileNumber AS FileNumber, LoadDate, Description,
				PowerFactorSummer, PowerFactorWinter, MaximumLoadsSummer, MaximumLoadsWinter,
				ConnectorLoad, SummerLoad, WinterLoad, Remarks
				FROM ApplicationLoad AS l LEFT JOIN ApplicationLoadDetail AS d ON l.FileNumber = d.FileNumber 
				WHERE ApplicationNumber='{$param['applicationNumber']}'";
				
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
		while($r = $ds->fetch(PDO::FETCH_ASSOC, $index++)) {
			$r2 = (object)$r;
			//if ($first) {
			//	$this->result[] = array('FileNumber' => $r2 -> FileNumber, 'LoadDate' => $r2 -> LoadDate);
			//	$first = false;
			//}
			if ($index == 0) 
				$this->result[] = array('FileNumber' => $r2 -> FileNumber, 'LoadDate' => $r2 -> LoadDate,
										'PowerFactorSummer' => $r2 -> PowerFactorSummer, 'PowerFactorWinter' => $r2 -> PowerFactorWinter,
										'MaximumLoadsSummer' => $r2 -> MaximumLoadsSummer, 'MaximumLoadsWinter' => $r2 -> MaximumLoadsWinter
				);

			if (!($r2 -> ConnectorLoad == null && $r2 -> SummerLoad == null && $r2 -> WinterLoad == null))
				$this->result[] = array('Description' => $r2 -> Description, 'ConnectorLoad' => $r2 -> ConnectorLoad, 
									'SummerLoad' => $r2 -> SummerLoad, 'WinterLoad' => $r2 -> WinterLoad, 'Remarks' => $r2 -> Remarks);
		}
		return $this->result;
	}	
	
	public function getAreas() {
		$dbh = $this->connect();
		try {
			$st = "SELECT ID, AreaName FROM Area ORDER BY AreaName ASC";
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

	function insertUpdate($param, $op) {
		$dbh = $this->connect();

		if ($op == 'insert') {
			//$str = preg_replace( "/^[a-z]/", '$0', $param["schema"][2]["primary-key"] );
			//$str = $this->hyphensToCamel($param["schema"][2]["primary-key"]);
			//throw new Exception($str);
			
			//$que = $dbh->query("SELECT COUNT(*) FROM Application WHERE ApplicationNumber = {$this->hyphensToCamel($param['schema'][2]['primary-key'])}");
			//$que = $dbh->query("SELECT COUNT(*) FROM Application WHERE ApplicationNumber = {$param[$param['schema'][2]['primary-key']]}");
			
			if ($param['schema'] == 'main-form')
				$que = $dbh->query("SELECT COUNT(*) FROM Application WHERE ApplicationNumber = '{$param['application-number']}'");
			else if ($param['schema'] == 'load-form')
				$que = $dbh->query("SELECT COUNT(*) FROM ApplicationLoad WHERE FileNumber = '{$param['file-number']}'");
			else
				throw new Exception('Wrong form: ' . $param['schema']);;

			if($que->fetchColumn() != 0)
				throw new Exception("23000"); //"The document already exists"
		}
		
		$dbh->beginTransaction();

		try {
			if (isset($param['area-id'])) {
				$areaId = $param['area-id'];
				$areaName = $param['area'];
				if ($areaId == NULL && $areaName != NULL) {
					$stArea = $dbh->prepare("INSERT INTO Area(ID, AreaName) VALUES(NULL, '$areaName');");
					$stArea->execute();
					$areaId = $dbh->lastInsertId();
					$param['area-id'] = $areaId;
					$this->result[] = $areaId;
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

				if ($key == 'schema' || $key == 'table' || $key == 'area')
					continue;

				if ($key == 'application-date' || $key == 'load-date') {
					if ($param[$key] != '')
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
				//}

				//if ($key == 'applicationdate')
					//break;
			} 			

			if ($param['schema'] == 'main-form')
				$st = "INSERT INTO Application (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
			else if ($param['schema'] == 'load-form')
				$st = "INSERT INTO ApplicationLoad (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
			else
				throw new Exception('Wrong form: ' . $param['schema']);;
				
			//throw new Exception($st);				
			$ds = $dbh->prepare($st);
			$ds->execute($ar);

			if ($param['schema'] == 'main-form')
				$st = "DELETE FROM ApplicationDetail WHERE ApplicationNumber = '{$param['application-number']}'";
			else if ($param['schema'] == 'load-form')
				$st = "DELETE FROM ApplicationLoadDetail WHERE FileNumber = '{$param['file-number']}'";
				
			//throw new Exception($st);
			$ds = $dbh->prepare($st);
			$ds->execute($ar);
			
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

		$driver = explode(':', $this->dsn, 2);
		$driver = strtolower($driver[0]);
		$nullfirst = "";
		if ($driver == 'oci')
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
/*
	public function createUpdateOngoingCheckup($param) {
		$dbh = $this->connect();

		$columnsToUpdate = "";
		$ar = [];
		
		if ($param['date_ins'] != "")
			$param['date_ins'] = DateTime::createFromFormat('d/m/Y', $param['date_ins'])->format('Y-m-d');
		else
			$param['date_ins'] =  (new DateTime())->format('Y-m-d-H-i-s');
			//$param['date_ins'] = (new DateTime("now", new DateTimeZone('Asia/Kuwait')))->format('Y-m-d-H-i-s');
		
		//throw new Exception($param['date_ins']);
		//throw new Exception($param['elc_load_new'] == 0.0);
			
		//$param['date_ins'] = "";
		$submmit_time=strtotime($param['date_ins']);
		//throw new Exception(date("Y", $submmit_time));

		$ds = $dbh->query("SELECT 1 FROM ongoing_check 
							WHERE form_no = '$param[form_no]' AND year = " . date("Y", $submmit_time) . " AND file_no != '$param[file_no]'");
		if($ds->fetchColumn() == 1) {
			throw new Exception('23000');
		}
		
		if ($param['check_1_dt'] != "")
			$param['check_1_dt'] = DateTime::createFromFormat('d/m/Y', $param['check_1_dt'])->format('Y-m-d');
		if ($param['check_2_dt'] != "")
			$param['check_2_dt'] = DateTime::createFromFormat('d/m/Y', $param['check_2_dt'])->format('Y-m-d');
		if ($param['check_3_dt'] != "")
			$param['check_3_dt'] = DateTime::createFromFormat('d/m/Y', $param['check_3_dt'])->format('Y-m-d');
		
		$ds = $dbh->query("SELECT 1 FROM ongoing_check WHERE file_no = '$param[file_no]'");
		if($ds->fetchColumn() == 1) {
			if ($param['form_no'] != "") {
				$columnsToUpdate .= " form_no = '{$param['form_no']}'";
			}
			if ($param['date_ins'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "date_ins = '{$param['date_ins']}'";
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "year = " . date("Y", $submmit_time);
			}
			
			if (!isset($param['elc_load_new']) || $param['elc_load_new'] == "")
				$param['elc_load_new'] = 0.0;
			if (!isset($param['elc_load_old']) || $param['elc_load_old'] == "")
				$param['elc_load_old'] = 0.0;

			//if ($param['elc_load_new'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "elc_load_new = '{$param['elc_load_new']}'";
			//}			
			//if ($param['elc_load_old'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "elc_load_old = '{$param['elc_load_old']}'";
			//}
			if ($param['check_1_dt'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "check_1_dt = '{$param['check_1_dt']}'";
			}
			if ($param['checker_1'] != 0) {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "checker_1 = {$param['checker_1']}";
			}
			if ($param['result_1'] != 0) {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "result_1 = {$param['result_1']}";
			}
			if ($param['note_1'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "note_1 = '{$param['note_1']}'";
			}
			if ($param['check_2_dt'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "check_2_dt = '{$param['check_2_dt']}'";
			}
			if ($param['checker_2'] != 0) {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "checker_2 = {$param['checker_2']}";
			}
			if ($param['result_2'] != 0) {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "result_2 = {$param['result_2']}";
			}
			if ($param['note_2'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "note_2 = '{$param['note_2']}'";
			}
			if ($param['check_3_dt'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "check_3_dt = '{$param['check_3_dt']}'";
			}
			if ($param['checker_3'] != 0) {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "checker_3 = {$param['checker_3']}";
			}
			if ($param['result_3'] != 0) {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "result_3 = {$param['result_3']}";
			}
			if ($param['note_3'] != "") {
				$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . "note_3 = '{$param['note_3']}'";
			}
		} else {
			$columnsToUpdate .= " form_no = '{$param['form_no']}'";
		}

		$ar = array(
			'file_no' => $param['file_no'],
			'form_no' => $param['form_no'],
			'year' => date("Y", $submmit_time),
			'date_ins' => $param['date_ins'],
			'elc_load_new' => $param['elc_load_new'],
			'elc_load_old' => $param['elc_load_old'],
			'check_1_dt' => $param['check_1_dt'],
			'checker_1' => $param['checker_1'],
			'result_1' => $param['result_1'],
			'note_1' => $param['note_1'],
			'check_2_dt' => $param['check_2_dt'],
			'checker_2' => $param['checker_2'],
			'result_2' => $param['result_2'],
			'note_2' => $param['note_2'],
			'check_3_dt' => $param['check_3_dt'],
			'checker_3' => $param['checker_3'],
			'result_3' => $param['result_3'],
			'note_3' => $param['note_3'],
		);
		
		try {
			$st = "INSERT INTO ongoing_check (file_no, form_no, year, date_ins, elc_load_new, elc_load_old, check_1_dt, checker_1, result_1, note_1, check_2_dt, checker_2, result_2, note_2, check_3_dt, checker_3, result_3, note_3)
					VALUES (:file_no,  :form_no, :year, :date_ins, :elc_load_new, :elc_load_old, :check_1_dt, :checker_1, :result_1, :note_1, :check_2_dt, :checker_2, :result_2, :note_2, :check_3_dt, :checker_3, :result_3, :note_3)
					ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
					//ON DUPLICATE KEY UPDATE check_1_dt = '{check_1_dt}'"; // . $columnsToUpdate;

			//throw new Exception($st);

			$stp = $dbh->prepare($st);
			$stp->execute($ar);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
	
	public function insertIntoCheckups($param) {
		$dbh = $this->connect();

		if ($param['date_ins'] != "")
			$param['date_ins'] = DateTime::createFromFormat('d/m/Y', $param['date_ins'])->format('Y-m-d');
		else
			$param['date_ins'] =  (new DateTime())->format('Y-m-d-H-i-s');
			//$param['date_ins'] = (new DateTime("now", new DateTimeZone('Asia/Kuwait')))->format('Y-m-d-H-i-s');
		
		$submmit_time=strtotime($param['date_ins']);
		
		if ($param['check_1_dt'] != "")
			$param['check_1_dt'] = DateTime::createFromFormat('d/m/Y', $param['check_1_dt'])->format('Y-m-d');
		if ($param['check_2_dt'] != "")
			$param['check_2_dt'] = DateTime::createFromFormat('d/m/Y', $param['check_2_dt'])->format('Y-m-d');
		if ($param['check_3_dt'] != "")
			$param['check_3_dt'] = DateTime::createFromFormat('d/m/Y', $param['check_3_dt'])->format('Y-m-d');
		
		$ar = [];
		$ar = array(
			'file_no' => $param['file_no'],
			'form_no' => $param['form_no'],
			'year' => date("Y", $submmit_time),
			'date_ins' => $param['date_ins'],
			'elc_load_new' => $param['elc_load_new'],
			'elc_load_old' => $param['elc_load_old'],
			'area_id' => $param['area_id'],
			'sector_addrs' => $param['sector_addrs'],
			'qasimaa' => $param['qasimaa'],
			'usr_ins' => $param['user_name'],
			'check_1_dt' => $param['check_1_dt'],
			'checker' => $param['checker_1'],
			'result_1' => $param['result_1'],
			'notes_1' => $param['note_1'],
			'check_2_dt' => $param['check_2_dt'],
			'checker_2' => $param['checker_2'],
			'result_2' => $param['result_2'],
			'notes_2' => $param['note_2'],
			'check_3_dt' => $param['check_3_dt'],
			'checker_3' => $param['checker_3'],
			'result_3' => $param['result_3'],
			'notes_3' => $param['note_3'],
		);
		
		try {
			$dbh->beginTransaction();
		
			$st = "INSERT INTO check_form (file_no, form_no, year, date_ins, elc_load_new, elc_load_old, area_id, sector_addrs, qasimaa, usr_ins, check_1_dt, checker, result_1, notes_1, check_2_dt, checker_2, result_2, notes_2, check_3_dt, checker_3, result_3, notes_3)
					VALUES (:file_no, :form_no, :year, :date_ins, :elc_load_new, :elc_load_old, :area_id, :sector_addrs, :qasimaa, :usr_ins, :check_1_dt, :checker, :result_1, :notes_1, :check_2_dt, :checker_2, :result_2, :notes_2, :check_3_dt, :checker_3, :result_3, :notes_3)";

			//throw new Exception($st);

			$stp = $dbh->prepare($st);
			$stp->execute($ar);
			
			$st = "UPDATE doc SET sectionId = {$param['sectionId']} WHERE docFileNumber = {$param['file_no']}";
			$stp = $dbh->prepare($st);
			$stp->execute();

			$st = "DELETE FROM ongoing_check WHERE file_no = {$param['file_no']}";
			$stp = $dbh->prepare($st);
			$stp->execute();

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
	
	public function approve_reject($param) {
		$dbh = $this->connect();

		try {
			$dbh->beginTransaction();
			
			//throw new Exception ($dbh->quote($param[docComment]));
			if ($param[sectionId] != null) {
				$st = "UPDATE doc SET docComment = " . $dbh->quote($param[docComment]);
				//if ($param[sectionId])
				$st .= ", sectionId = " . $param[sectionId];
				$st .= ", employeeId = NULL";
				$st .= " WHERE docFileNumber = '$param[docFileNumber]'";
				//$st = "UPDATE doc SET docComment = " . $dbh->quote($param[docComment]) . ", employeeId = NULL WHERE docFileNumber = $dbh->quote($param[docFileNumber])";
				
				$stDoc = $dbh->exec($st);
				
				$dt = date_create_from_format('d/m/Y', $param[docHistory][docDate]);
				$dt = date_format($dt, 'Y-m-d');
				//$appr = $param[docHistory][docApprover];
				//$st = "INSERT INTO docHistory (docFileNumber, docDate, docApprover)	VALUES ($param[docFileNumber], '$dt', '$appr')";
				$st = "INSERT INTO docHistory (docFileNumber, docDate, docApprover)	VALUES ('$param[docFileNumber]', '$dt', '{$param[docHistory][docApprover]}')";
				$stDoc = $dbh->exec($st);
			} else {
				if ($param[employeeId] != null)
					$st = "UPDATE doc SET employeeId = " . $dbh->quote($param[employeeId]);
				else
					$st = "UPDATE doc SET employeeId = NULL";
				
				$st .= " WHERE docFileNumber = '$param[docFileNumber]'";
				//$stDoc = $dbh->prepare($st);
				//$stDoc->execute();
				$stDoc = $dbh->exec($st);
			}
			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			//throw new Exception('Failed to execute/prepare query: ' . $st);
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
	}
*/
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
			$st = "SELECT ID, Title FROM Attachments WHERE ApplicationNumber='{$param['applicationNumber']}'";
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
	
	public function getUserSignatureList($param) {
		$dbh = $this->connect();

		//throw new Exception($param['currentuser']);

		try {
			$st = "SELECT ID, Width, Height, Resolution FROM SignatureList WHERE EmployeeId='{$param['currentuser']}'";
			//$st = "SELECT ID, Image, Width, Height, Resolution FROM SignatureList WHERE EmployeeId='{$param['currentuser']}'";
			$ds = $dbh->query($st);
		} catch (PDOException $e) {
			throw new Exception('Failed to execute/prepare query: ' . $e->getMessage());
		}
		
		$this->result = array();
		while($r = $ds->fetch(PDO::FETCH_ASSOC)) {
			$this->result[] = (object)$r;
/*			
			$r2 = (object)$r;
			$image = $r2 -> Image;
			$im = imagecreatefromstring($image);
			$width = imagesx($im);
			$height = imagesy($im);
			error_log("id " . $r2 -> ID  . " ; width "  . $width . " ;  height "  . $height . " \n", 3, "error.log");
			//error_log("id " . $r2 -> ID . " ; width "  . $width . " ;  height "  . $height . " \n");
*/			
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
			
		try {
			$st = " SELECT SignatureID, EmployeeName, Date, TopPos, LeftPos, Width, Height, Resolution FROM {$tableName} INNER JOIN SignatureList ON SignatureID = ID WHERE 
					{$this->hyphensToCamel($param['data-key-field'])}  =  '{$param['data-key-field-val']}' ";
				
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
					$columnsToUpdate .= ($columnsToUpdate == "" ? '' : ',') . $key . " = '{$val}'";
					$fields .= ($fields == "" ? '' : ',') . $key;
					$values .= ($values == "" ? ':' : ',:') . $key;
					$ar[$key] = $val;
				//}
			} 			

			$columnsToUpdate .= ", Date = '" . date('Y-m-d') . "'";
			$fields .= ",Date";
			$values .= ",:Date";
			$ar["Date"] = date('Y-m-d');

			//error_log(print_r($columnsToUpdate, true));
			//throw new Exception(print_r($ar, true));
			
			if ($param['schema'] == 'main-form')
				$st = "INSERT INTO ApplicationSignature (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
			else if ($param['schema'] == 'load-form')
				$st = "INSERT INTO ApplicationLoadSignature (" . $fields . ")" . "VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $columnsToUpdate;
			else
				throw new Exception('Wrong form: ' . $param['schema']);;
				
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
