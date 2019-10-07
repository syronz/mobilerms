<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/validClass.php');

class cat extends database{
	private $table = 'cat';
	private $struct = ['self'=>['id','name','detail']];
	function add($v){
		$vCheck = @$this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		if(!$vCheck['result'])
			return json_encode($vCheck);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		$old = $this->getRow($this->table,$v['id']);
		$vCheck['result'] = true;
		if($v['name'] != $old['name'])
			$vCheck = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		if(!$vCheck['result'])
			return json_encode($vCheck);

		$this->editDefault($this->table,$v,$this->struct);

		$this->record($this->table,'edit','',$v['id'],['new'=>$v,'old'=>$old]);
		return json_encode(['result'=>true]);
	}

	function delete($v){
		try{
			$old = $this->getRow($this->table,$v['id']);

			$sql = "DELETE FROM {$this->table} WHERE id = '{$v['id']}'";
			$this->pdo->query($sql);

			$this->record($this->table,'delete','',$v['id'],['old'=>$old]);
			return json_encode(['result'=>true]);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function show($v){
		$idDepp = $_SESSION['idDepp'];
		$sql = "SELECT id_cat FROM depp_cat WHERE depp_cat.id_depp = '$idDepp'";
		$result = $this->pdo->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_COLUMN);
		$rows = implode($rows,',');

		$v['condition'] = "cat.id IN ($rows)";

		return $this->listDefault($this->table,$v,$this->struct);
	}

	function catList(){
		try{
			$table = $this->table;
			$fields = ['id','name'];

			$idDepp = $_SESSION['idDepp'];

			$sql = "SELECT id,name FROM $table WHERE id IN (SELECT id_cat FROM depp_cat WHERE id_depp = '$idDepp') ORDER BY name ASC";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			return json_encode($rows);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}
}
$cat = new cat();
// for($i = 0; $i<50000; $i++)
// 	dsh($cat->add(['name'=>'Math'.rand().rand(),'detail'=>'for test']));


?>