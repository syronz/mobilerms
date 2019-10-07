<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class dollar_rate extends database{
	private $table = 'dollar_rate';
	private $struct = ['self'=>['id','rate','id_city','date'],
	'city'=>['source'=>'id_city','target'=>'id','column'=>'name','search'=>['name','detail']]];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$v['date'] = date('Y-m-d H:i:s',time());

		$required = $this->isRequired($v,['rate'=>'rate','id_city'=>'city']);
		if(!$required['result'])
			return json_encode($required);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['rate'=>'rate','id_city'=>'city']);
		if(!$required['result'])
			return json_encode($required);

		$old = $this->getRow($this->table,$v['id']);

		$vCheck = $this->valid($old['date'],['isToday'],$this->table,'date');
		if(!$vCheck['result'])
			return json_encode($vCheck);

		$this->editDefault($this->table,$v,$this->struct);

		$this->record($this->table,'edit','',$v['id'],['new'=>$v,'old'=>$old]);
		return json_encode(['result'=>true]);
	}

	function delete($v){
		try{
			if(!$this->perm($this->table,'delete'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Delete','extra'=>'']]]);

			$old = $this->getRow($this->table,$v['id']);
			$vCheck = $this->valid($old['date'],['isToday'],$this->table,'date');
			if(!$vCheck['result'])
				return json_encode($vCheck);

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

	function get_dollar_rate($idDepp = null,$date = null){
		try{
			if(empty($idDepp))
				$idDepp = $_SESSION['idDepp'];
			$idDepp = $this->toSave($idDepp);
			$depp = $this->getRow('depp',$idDepp);
			$branch = $this->getRow('branch',$depp['id_branch']);
			$city = $this->getRow('city',$branch['id_city']);

			if(empty($date)){
				$sql = "SELECT rate FROM dollar_rate WHERE id_city = '{$city['id']}' ORDER BY id DESC LIMIT 1";
				$result = $this->pdo->query($sql);
				$row = $result->fetch(PDO::FETCH_ASSOC);
			}
			else{
				$sql = "SELECT rate FROM dollar_rate WHERE id_city = '{$city['id']}' and date like '$date%' ORDER BY id DESC LIMIT 1";
				$result = $this->pdo->query($sql);
				$row = $result->fetch(PDO::FETCH_ASSOC);
			}
			
			return $row['rate'];
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

}
$dollar_rate = new dollar_rate();

// dsh($dollar_rate->get_dollar_rate(1));
// for($i = 0; $i<50000; $i++)
// 	dsh($dollar_rate->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_city'=>rand(100,7400),'capacity'=>rand(50,900)]));


?>