<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class user_depp extends database{
	private $table = 'user_depp';
	private $struct = ['self'=>['id','id_user','id_depp'],
	'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name','detail']]
	];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['id_user'=>'user','id_depp'=>'depp']);
		if(!$required['result'])
			return json_encode($required);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['id_user'=>'user','id_depp'=>'depp']);
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

	function user_deppList(){
		try{
			$sql = "SELECT * FROM user_depp";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function userDeppList(){
		try{
			$idUser = $this->idUser();
			$sql = "SELECT d.* FROM user_depp ud inner join depp d on d.id = ud.id_depp WHERE ud.id_user = '$idUser'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

}
$user_depp = new user_depp();
// for($i = 0; $i<50000; $i++)
// 	dsh($user_depp->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_cat'=>rand(100,7400),'capabrand'=>rand(50,900)]));


?>