<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/validClass.php');

class city extends database{
	private $table = 'city';
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
		return $this->listDefault($this->table,$v,$this->struct);
	}

	function cityList(){
		return $this->listAllDefault($this->table,['id','name']);
	}
}
$city = new city();
// for($i = 0; $i<50000; $i++)
// 	dsh($city->add(['name'=>'Math'.rand().rand(),'detail'=>'for test']));


?>