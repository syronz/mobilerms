<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class payin extends database{
	private $table = 'payin';
	private $struct = ['self'=>['id','id_account','detail','id_user','id_outdepp','dollar','dinar','dollar_rate','type','date'],
	'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']],
	'account'=>['source'=>'id_account','target'=>'id','column'=>'name','search'=>['name','detail']],
	'outdepp'=>['source'=>'id_outdepp','target'=>'id','column'=>'invoice','search'=>['invoice']]
	];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['id_account'=>'account','type'=>'type']);
		if(!$required['result'])
			return json_encode($required);
		$v['id_user'] = $_SESSION['id'];

		if($v['type'] != 'old'){
			$v['date'] = date('Y-m-d H:i:s',time());
			$required = $this->isRequired($v,['id_depp'=>'depp']);
			if(!$required['result'])
				return json_encode($required);
			$v['dollar_rate'] = $this->getDollarRate($v['id_depp']);
		}
		else{
			$required = $this->isRequired($v,['date'=>'date']);
			if(!$required['result'])
				return json_encode($required);
			$v['date'] = date('Y-m-d',strtotime($v['date']));
		}

		// $vCheck1 = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		// if(!$vCheck1['result'])
		// 	return json_encode($vCheck1);

		unset($v['addToCash']);


		$required = $this->isRequired($v,['dollar_rate'=>'dollar_rate']);
		if(!$required['result'])
			return json_encode($required);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_user'=>'user']);
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

	function payinList(){
		try{
			$sql = "SELECT * FROM payin";
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
$payin = new payin();
// for($i = 0; $i<50000; $i++)
// 	dsh($payin->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_user'=>rand(100,7400),'capauser'=>rand(50,900)]));


?>