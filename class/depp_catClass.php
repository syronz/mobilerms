<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class depp_cat extends database{
	private $table = 'depp_cat';
	private $struct = ['self'=>['id','id_cat','id_depp'],
	'cat'=>['source'=>'id_cat','target'=>'id','column'=>'name','search'=>['name','detail']],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name','detail']]
	];

	function add($v){
		$v = $this->toSave($v);
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['id_cat'=>'cat','id_depp'=>'depp']);
		if(!$required['result'])
			return json_encode($required);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['id_cat'=>'cat','id_depp'=>'depp']);
		if(!$required['result'])
			return json_encode($required);

		$old = $this->getRow($this->table,$v['id']);

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

	function depp_catList(){
		try{
			$sql = "SELECT * FROM depp_cat";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function deppCatAuth($idDepp){
		try{
			$idDepp = $this->toSave($idDepp);
			$sql = "SELECT id_cat FROM depp_cat WHERE id_depp = '$idDepp'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$depp_cat = new depp_cat();
// for($i = 0; $i<50000; $i++)
// 	dsh($depp_cat->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_cat'=>rand(100,7400),'capabrand'=>rand(50,900)]));


?>