<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class depp extends database{
	private $table = 'depp';
	private $struct = ['self'=>['id','name','detail','id_branch','type','code','id_cash','id_store','address','phone'],
	'branch'=>['source'=>'id_branch','target'=>'id','column'=>'name','search'=>['name','detail']],
	'account'=>['source'=>'id_cash','target'=>'id','column'=>'name','search'=>['name']]
	];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_branch'=>'branch','type'=>'type','code'=>'code']);
		if(!$required['result'])
			return json_encode($required);

		$vCheck1 = $this->valid($v['name'],['maxLength200','isName'],$this->table,'name');
		$vCheck2 = $this->valid($v['code'],['isUnique','maxLength200','isName'],$this->table,'code');
		$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
		if(!$vCheckArr['result'])
			return json_encode($vCheckArr);

		$v['id_store'] = 11;

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_branch'=>'branch']);
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

	function deppList(){
		try{
			$sql = "SELECT * FROM depp";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function generateInvoice($part,$idDepp){//this is uncomplete
		try{
			
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$depp = new depp();
// for($i = 0; $i<50000; $i++)
// 	dsh($depp->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_branch'=>rand(100,7400),'capabranch'=>rand(50,900)]));


?>