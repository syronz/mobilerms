<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class branch extends database{
	private $table = 'branch';
	private $struct = ['self'=>['id','name','detail','id_city'],
	'city'=>['source'=>'id_city','target'=>'id','column'=>'name','search'=>['name','detail']]];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_city'=>'city']);
		if(!$required['result'])
			return json_encode($required);

		$vCheck1 = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		if(!$vCheck1['result'])
			return json_encode($vCheck1);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_city'=>'city']);
		if(!$required['result'])
			return json_encode($required);

		$old = $this->getRow($this->table,$v['id']);

		if($v['name'] != $old['name'])
			$vCheck1 = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		else
			$vCheck1 = $this->valid($v['name'],['maxLength200','isName'],$this->table,'name');

		if(!$vCheck1['result'])
			return json_encode($vCheck1);

		$this->editDefault($this->table,$v,$this->struct);

		$this->record($this->table,'edit','',$v['id'],['new'=>$v,'old'=>$old]);
		return json_encode(['result'=>true]);
	}

	function delete($v){
		try{
			if(!$this->perm($this->table,'delete'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Delete','extra'=>'']]]);

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
		if($this->perm($this->table,'view'))
			return $this->listDefault($this->table,$v,$this->struct);
	}

	function branchList(){
		try{
			$sql = "SELECT * FROM branch";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}
}
$branch = new branch();
// for($i = 0; $i<50000; $i++)
// 	dsh($branch->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_city'=>rand(100,7400),'capacity'=>rand(50,900)]));


?>