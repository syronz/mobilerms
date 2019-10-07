<?php
require_once realpath(__DIR__ . '/databaseClass.php');


/*------------------------------------------------------
recordActivity Explain:
INPUT:
	$table : name of table that transaction affect on it
	$action: enum [add,edit,delete,search,view]
	$detail: detail about transaction mostly null and in search type show searchStr
	$idTable: temp field completly null in this program use 0
	$data: array of data, it have 2 basic key and several data for example
		for update ['new'=>['name'=>'diako','mark'=>100],'old'=>['name'=>'Ako','mark'=>60]]
		for insert ['new'=>['name'=>'diako','mark'=>100]]
		for delete ['old'=>['name'=>'Ako','mark'=>60]]
	$idUser: get from $_SESSION
	$ip: get from $_SERVER['REMOTE_ADDR']
	$dateTime: best way is date('Y-m-d H:i:s',time())

TABLE STRUCTURE:
	CREATE TABLE `log` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_user` INT(10) UNSIGNED NOT NULL,
	`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`table` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`action` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`detail` TEXT NULL COLLATE 'utf8_bin',
	`ip` VARCHAR(50) NOT NULL COLLATE 'utf8_bin',
	`id_table` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB
AUTO_INCREMENT=3;
-------------------------------------------------------*/

class log extends database{
	private $table = 'log';
	private $struct = ['self'=>['id','id_user','date','table','action','detail','ip','id_table']];




	function recordActivity($table,$action,$detail,$idtable,$data,$idUser,$ip,$dateTime){
		try{
			$this->pdoLog->query("INSERT INTO `log` (`id_user`, `date`, `table`, `action`, `detail`, `ip`, `id_table`) VALUES ('$idUser','$dateTime','$table','$action','$detail','$ip','$idtable')");

			if($data){
				$idLog = $this->pdoLog->lastInsertId();
				if($action == 'edit'){
					foreach ($data['old'] as $key => $value){
						if($value != @$data['new'][$key])
							@$this->pdoLog->query("INSERT INTO `data`(id_log,old,new,name) VALUES('$idLog','$value','{$data['new'][$key]}','$key');");
					}
				}
				else if($action == 'delete'){
					foreach ($data['old'] as $key => $value){
						$this->pdoLog->query("INSERT INTO `data`(id_log,old,name) VALUES('$idLog','$value','$key');");
					}
				}
				else if($action == 'add'){
					foreach ($data['new'] as $key => $value){
						$this->pdoLog->query("INSERT INTO `data`(id_log,new,name) VALUES('$idLog','$value','$key');");
					}
				}
				else
					return false;
			}
			return true;
		}
		catch(PDOException $e){
			dsh($e);
			die();
		}
	}


	function show($v){
		try{
			if(!$this->perm($this->table,'view'))
				return false;
			$table = $this->table;
			$data = $v;
			$struct = $this->struct;

			$data = $this->toSave($data);

			$order = explode(' ', $data['order']);
			if(array_search($order[0],$struct['self']) === false)
				return json_encode(['result'=>false]);

			if(!(strtoupper($order[1]) == 'DESC' || strtoupper($order[1]) == 'ASC'))
				return json_encode(['result'=>false]);

			$limit = explode(',', $data['limit']);
			$data['limit'] = intval($limit[0]).','.intval(@$limit[1]);

			$sql = $this->queryDesignerSelect($table,$struct,$data['order'],$data['limit'],$data['search'],@$data['condition']);

			$result = $this->pdoLog->query($sql);
			$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$r['rows'] = $this->entityDecode($r['rows']);

			$sql = $this->queryDesignerCount($table,$struct,$data['search'],@$data['condition']);
			$result = $this->pdoLog->query($sql);
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

	function showExtra($idLog){
		try{
			$idLog = $this->toSave($idLog);
			
			$sql = "SELECT * FROM data WHERE id_log = '$idLog'";
			$result = $this->pdoLog->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			return json_encode(['result'=>true, 'rows'=>$rows]);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function logPrint($v){
		try{
			// dsh($v);
			$date = date('Y-m-d',time());
			$time = date('H,i,s',time());
			$fileName = "../logPrint/".$v['part'].'&id='.$v['id'].'&idDEpp='.$_SESSION['idDepp'].'&idUser='.$this->idUser().'&date='.$date.'&time='.$time.'.html';

			file_put_contents($fileName, $v['data']);
		}
		catch(Exception $e){
			dsh($e);
		}
	}



}
$log = new log();
// $log->recordActivity('test','add','new test info',5,['new'=>['diako','ako','bafrin']],6,'192.168.1.1','2015-06-17 00:00:00');

// NULL, '5', '2015-06-17 00:00:00', 'test', 'add', 'no add', '192.168.1.1', NULL


?>