<?php
@session_start();
error_reporting(E_ALL);
date_default_timezone_set("Asia/Baghdad");

// if((@!$_SESSION['id'] && @$_GET['action'] != 'login') || $_SESSION['idCustomer'])
// 	die();

require_once realpath(__DIR__ . '/../settingClass.php');
require_once realpath(__DIR__ . '/validClass.php');
require_once realpath(__DIR__ . '/logClass.php');
require_once realpath(__DIR__ . '/permClass.php');
require_once realpath(__DIR__ . '/dollar_rateClass.php');

function dsh(){
	$argList = func_get_args();
	// var_dump($argList);
	echo '<pre style="color:red">';
	foreach($argList as $key => $v){
		
		$backtrace = debug_backtrace();
		// var_dump($backtrace[0]);
		echo $backtrace[0]['file'].' ('.$backtrace[0]['line'].')   ';
		ob_start();
		var_dump($v);
		$result = ob_get_clean();
		$result = str_replace(">\n", '>', $result);
		echo $result;
		
	}
	echo '</pre><hr>';
}


class database{
	public $pdo;
	public $pdoLog;
	function __construct(){
		try {
			$this->pdo = new PDO('mysql:host=127.0.0.1;dbname='.setting::DATABASE, setting::USER_DATABASE, setting::MYSQL_PASSWORD);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->query('SET NAMES utf8');

			$this->pdoLog = new PDO('mysql:host=127.0.0.1;dbname='.setting::DATABASE_LOG, setting::USER_DATABASE, setting::MYSQL_PASSWORD);
			$this->pdoLog->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdoLog->query('SET NAMES utf8');
		} 
		catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}


	function dshConvertNumber($string) {
		$persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
		$arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');

		$num = range(0, 9);
		$string = str_replace($persian, $num, $string);
		return str_replace($arabic, $num, $string);
	}

	function bigIntval($value) {
		$value = trim($value);
		if (ctype_digit($value)) {
			return $value;
		}
		$value = preg_replace("/[^0-9](.*)$/", '', $value);
		if (ctype_digit($value)) {
			return $value;
		}
		return 0;
	}

	function addDefault($table,$data){
		try{
			$data = $this->toSave($data);
			$sql = "INSERT INTO `$table`";
			$midSql = '(';
				$endSql = ' VALUES(';

					foreach ($data as $key => $value) {
						$midSql .= $key.',';
						$endSql .= "'$value',";
					}
					$midSql = substr($midSql, 0,-1).')';
$endSql = substr($endSql, 0,-1).');';
$sql = $sql.$midSql.$endSql;
$this->pdo->query($sql);

$lastId = $this->pdo->lastInsertId();

$this->record($table,'add','',$lastId,['new'=>$data]);
return $lastId;
}
catch(PDOException $e){
	dsh($e);
}
}

function editDefault($table,$data,$struct){
	try{
		$data = $this->toSave($data);
		$sql = "UPDATE `$table` SET ";
		$midSql = '';
		$endSql = " WHERE id = '{$data['id']}'";

		foreach ($data as $key => $value) {
			if($key != 'id' && in_array($key, $struct['self'])){
				$midSql .= "$key = '$value',";
			}
		}
		$midSql = substr($midSql, 0,-1);
		$sql = $sql.$midSql.$endSql;
		$this->pdo->query($sql);

		return true;
	}
	catch(PDOException $e){
		dsh($e);
	}
}

protected function queryDesignerCount($table,$struct,$search = null,$condition=null){
	$sqlSelectors = 'SELECT COUNT(*) AS count';
	$sqlFrom = " FROM `$table`";
	if($search)
		$sqlWhere = ' WHERE (';
	else
		$sqlWhere = ' ';
	foreach ($struct as $key => $value) {
		if($key == 'self'){
			foreach ($value as $fields) {
				if($search){
					$sqlWhere .= " UPPER($table.$fields) LIKE UPPER('%$search%') OR ";
				}
			}
			if($search){
				$sqlWhere = substr($sqlWhere, 0,-3);
			}
		}
		else{
			// $sqlSelectors .= "$key.{$value['column']} AS $key,";
			$sqlFrom .= " LEFT JOIN $key ON $key.{$value['target']} = $table.{$value['source']} ";
			// if($search){
			// 	foreach ($value['search'] as $searchField) {
			// 		$sqlWhere .= " UPPER($key.$searchField) LIKE UPPER('%$search%') OR ";
			// 	}
			// }
		}
	}
	if($search){
		$sqlWhere .= ')';
		if($condition)
			$sqlWhere .= " AND $condition ";
	}
	else{
		if($condition)
			$sqlWhere .= " WHERE $condition ";
	}
	return "$sqlSelectors $sqlFrom $sqlWhere";
}

protected function queryDesignerSelect($table,$struct,$order,$limit,$search = null,$condition=null){
		// dsh($limit);
		// $sql = "SELECT ";
	$sqlSelectors = 'SELECT ';
	$sqlFrom = " FROM `$table`";
	if($search)
		$sqlWhere = ' WHERE (';
	else
		$sqlWhere = ' ';
	$sqlOrder = 'ORDER BY '.$order;
	foreach ($struct as $key => $value) {
		if($key == 'self'){
			foreach ($value as $fields) {
				$sqlSelectors .= "$table.$fields,";
				if($search){
					$sqlWhere .= " UPPER($table.$fields) LIKE UPPER('%$search%') OR ";
				}
			}
		}
		else{
			$sqlSelectors .= "$key.{$value['column']} AS $key,";
			$sqlFrom .= " LEFT JOIN $key ON $key.{$value['target']} = $table.{$value['source']} ";
			if($search){
				foreach ($value['search'] as $searchField) {
					$sqlWhere .= " UPPER($key.$searchField) LIKE UPPER('%$search%') OR ";
				}
			}
		}
	}
	$sqlSelectors = substr($sqlSelectors, 0,-1);
	if($search){
		$sqlWhere = substr($sqlWhere, 0,-3).')';
		if($condition)
			$sqlWhere .= ' AND '.$condition;
	}
	else{
		if($condition)
			$sqlWhere .= ' WHERE '.$condition;
	}

		// dsh("$sqlSelectors $sqlFrom $sqlWhere $sqlOrder LIMIT $limit");
	return "$sqlSelectors $sqlFrom $sqlWhere $sqlOrder LIMIT $limit";
}

function listDefault($table,$data,$struct){
	try{
		// $data = $this->toSave($data);

		$order = explode(' ', $data['order']);
		if(array_search($order[0],$struct['self']) === false)
			return json_encode(['result'=>false]);

		if(!(strtoupper($order[1]) == 'DESC' || strtoupper($order[1]) == 'ASC'))
			return json_encode(['result'=>false]);

		$limit = explode(',', $data['limit']);
		$data['limit'] = intval($limit[0]).','.intval(@$limit[1]);

		$sql = $this->queryDesignerSelect($table,$struct,$data['order'],$data['limit'],$data['search'],@$data['condition']);

		$sql2 = ' '.$sql;

		$result = $this->pdo->query($sql2);
		$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);
		$r['rows'] = $this->entityDecode($r['rows']);

		$sql = $this->queryDesignerCount($table,$struct,$data['search'],@$data['condition']);
		$result = $this->pdo->query($sql);
		$r['count'] = $result->fetch(PDO::FETCH_ASSOC)['count'];

		$action = $data['search']?'search':'view';

		if(SETTING::USER_ACTIVITY_READ)
			$this->record($table,$action,$data['search'],0,null);
		return json_encode($r);
	}
	catch(PDOException $e){
		dsh($e);
	}
}

function listAllDefault($table,$fields){
	try{
		$fields = $this->toSave($fields);
		$sql = "SELECT ";
		foreach ($fields as $value) {
			$sql .= $value.",";
		}
		$sql = substr($sql, 0,-1).' FROM `'.$table."` ORDER BY $fields[1] ASC";
		$result = $this->pdo->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			// $arrList = [];
			// foreach ($rows as $value) {
			// 	$arrList[$value[$fields[0]]] = $value[$fields[1]];
			// }
			// return json_encode($arrList);

		return json_encode($rows);
	}
	catch(PDOException $e){
		dsh($e);
	}
}

function valid($v,$arrCheck,$table = null, $field = null, $min = null, $max = null){
	$valid = new valid();
	return $valid->check($v,$arrCheck,$table,$field,$min,$max);
}

function record($table,$action,$detail,$idtable = null,$data = null){
	try{
		$log = new log();
		$log->recordActivity($table,$action,$detail,$idtable,$data,$_SESSION['id'],$_SERVER['REMOTE_ADDR'],date('Y-m-d H:i:s',time()));
	}
	catch(exception $e){
		dsh($e);
		die();
	}
}

function recordCustomer($table,$action,$detail,$idtable = null,$data = null){
	try{
		$log = new log();
		$log->recordActivity($table,$action,$detail,$idtable,$data,$_SESSION['idCustomer'],$_SERVER['REMOTE_ADDR'],date('Y-m-d H:i:s',time()));
	}
	catch(exception $e){
		dsh($e);
		die();
	}
}

function idUser(){
	try{
		if(!empty($_SESSION['id']))
			return $_SESSION['id'];
		else
			return 0;
	}
	catch(exception $e){
		dsh($e);
		die();
	}
}


function getDollarRate($idDepp){
	try{
		$dollar = new dollar_rate();
		$rate = $dollar->get_dollar_rate($idDepp);
		return $rate;
	}
	catch(exception $e){
		dsh($e);
		die();
	}
}

function getRow($table,$id){
	try{
		$sql = "SELECT * FROM `$table` WHERE id = '$id'";
		$result = $this->pdo->query($sql);
		return $result->fetch(PDO::FETCH_ASSOC);
	}
	catch(exception $e){
		dsh($e);
		die();
	}
}

function getRowsByFiled($table,$field,$v){
	try{
		$sql = "SELECT * FROM `$table` WHERE $field = '$v'";
		$result = $this->pdo->query($sql);
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(exception $e){
		dsh($e);
		die();
	}
}

function toSave($data){
	if(is_array($data))
		foreach ($data as $key => &$value) {
			if(is_array($value))
				$value = $this->toSave($value);
			else
				$value = htmlentities($value,ENT_QUOTES);
		}
		else
			$data = htmlentities($data,ENT_QUOTES);
		return $data;
	}

	function entityDecode($data){
		foreach ($data as $key => &$value) {
			if(is_array($value))
				$value = $this->entityDecode($value);
			else
				$value = html_entity_decode($value,ENT_QUOTES);
		}
		return $data;
	}

	function arrValid(){
		$argList = func_get_args();
		$result = true;
		$messages = [];
		foreach($argList as $vCheck){
			if($vCheck){
				$result = $result && $vCheck['result'];
				foreach ($vCheck['message'] as $value) {
					array_push($messages, $value);
				}	
			}
			
		}
		return ['result'=>$result,'message'=>$messages];
	}

	function isRequired($v,$arr){
		$valid = new valid();
		return $valid->required($v,$arr);
	}

	function perm($table,$action){
		$perm = new perm();
		return $perm->checkPerm($table,$action,$_SESSION['id']);
	}

	function encryptNumber($num){
		try{
			$arrEncrypt = ['0'=>'2','1'=>'7','2'=>'5','3'=>'0','4'=>'8','5'=>'3','6'=>'9','7'=>'6','8'=>'1','9'=>'4','00'=>'09','01'=>'03','02'=>'04','03'=>'08','04'=>'01','05'=>'05','06'=>'02','07'=>'06','08'=>'00','09'=>'07','10'=>'17','11'=>'55','12'=>'89','13'=>'43','14'=>'61','15'=>'37','16'=>'77','17'=>'94','18'=>'51','19'=>'82','20'=>'13','21'=>'66','22'=>'36','23'=>'20','24'=>'28','25'=>'93','26'=>'70','27'=>'18','28'=>'60','29'=>'75','30'=>'50','31'=>'41','32'=>'46','33'=>'90','34'=>'92','35'=>'86','36'=>'69','37'=>'96','38'=>'72','39'=>'49','40'=>'56','41'=>'16','42'=>'11','43'=>'34','44'=>'63','45'=>'27','46'=>'57','47'=>'68','48'=>'24','49'=>'47','50'=>'21','51'=>'84','52'=>'23','53'=>'79','54'=>'39','55'=>'67','56'=>'78','57'=>'26','58'=>'83','59'=>'73','60'=>'33','61'=>'31','62'=>'59','63'=>'64','64'=>'98','65'=>'12','66'=>'29','67'=>'14','68'=>'97','69'=>'30','70'=>'62','71'=>'19','72'=>'42','73'=>'85','74'=>'48','75'=>'40','76'=>'25','77'=>'35','78'=>'52','79'=>'45','80'=>'15','81'=>'32','82'=>'53','83'=>'80','84'=>'95','85'=>'76','86'=>'87','87'=>'38','88'=>'54','89'=>'58','90'=>'71','91'=>'81','92'=>'88','93'=>'91','94'=>'99','95'=>'74','96'=>'65','97'=>'22','98'=>'44','99'=>'10'];
			$newNum = '';
			while ($num !== false) {
				$l = substr($num, 0,2);
				// dsh($num,$l,$arrEncrypt[$l]);
				$newNum .= $arrEncrypt[$l];
				$num = substr($num, 2,strlen($num));
			}
			return $newNum;
			
		}
		catch(EXCEPTION $e){
			dsh($e);
		}
	}

	function decryptNumber($num){
		try{
			$arrEncrypt = ['2'=>'0','7'=>'1','5'=>'2','0'=>'3','8'=>'4','3'=>'5','9'=>'6','6'=>'7','1'=>'8','4'=>'9','09'=>'00','03'=>'01','04'=>'02','08'=>'03','01'=>'04','05'=>'05','02'=>'06','06'=>'07','00'=>'08','07'=>'09','17'=>'10','55'=>'11','89'=>'12','43'=>'13','61'=>'14','37'=>'15','77'=>'16','94'=>'17','51'=>'18','82'=>'19','13'=>'20','66'=>'21','36'=>'22','20'=>'23','28'=>'24','93'=>'25','70'=>'26','18'=>'27','60'=>'28','75'=>'29','50'=>'30','41'=>'31','46'=>'32','90'=>'33','92'=>'34','86'=>'35','69'=>'36','96'=>'37','72'=>'38','49'=>'39','56'=>'40','16'=>'41','11'=>'42','34'=>'43','63'=>'44','27'=>'45','57'=>'46','68'=>'47','24'=>'48','47'=>'49','21'=>'50','84'=>'51','23'=>'52','79'=>'53','39'=>'54','67'=>'55','78'=>'56','26'=>'57','83'=>'58','73'=>'59','33'=>'60','31'=>'61','59'=>'62','64'=>'63','98'=>'64','12'=>'65','29'=>'66','14'=>'67','97'=>'68','30'=>'69','62'=>'70','19'=>'71','42'=>'72','85'=>'73','48'=>'74','40'=>'75','25'=>'76','35'=>'77','52'=>'78','45'=>'79','15'=>'80','32'=>'81','53'=>'82','80'=>'83','95'=>'84','76'=>'85','87'=>'86','38'=>'87','54'=>'88','58'=>'89','71'=>'90','81'=>'91','88'=>'92','91'=>'93','99'=>'94','74'=>'95','65'=>'96','22'=>'97','44'=>'98','10'=>'99'];
			$newNum = '';
			while ($num !== false) {
				$l = substr($num, 0,2);
				// dsh($num,$l,$arrEncrypt[$l]);
				$newNum .= $arrEncrypt[$l];
				$num = substr($num, 2,strlen($num));
			}
			return $newNum;
			
		}
		catch(EXCEPTION $e){
			dsh($e);
		}
	}


	function getIp() {
		$ipaddress = '';
		if (@$_SERVER['HTTP_CLIENT_IP'])
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(@$_SERVER['HTTP_X_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(@$_SERVER['HTTP_X_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(@$_SERVER['HTTP_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(@$_SERVER['HTTP_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(@$_SERVER['REMOTE_ADDR'])
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
}

$db = new database();

// for($i=100000;$i<1000000;$i++){
// 	$s = $db->encryptNumber($i);
// 	if($i != $db->decryptNumber($s))
// 		dsh($i);
// }

// $data = ['name'=>'Math'.rand(),'detail'=>'for test'];
// dsh($db->addDefault('college',$data));
// $db->record('test','add','new test info',50,['new'=>['diako','ako','bafrin']],6,'192.168.1.1','2015-06-17');
// dsh($db->toSave(["دحاکۆ ' ؛ <script>!!!!test</script>",['mark'=>'دیاکۆ"""<'],'name>diako']));







?>
