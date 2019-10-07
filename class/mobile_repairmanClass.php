<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/validClass.php');

class mobile_repairman extends database{
	private $table = 'repairman';
	private $struct = ['self'=>['id','name','detail','state','mobile','id_depp'],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name','detail']]
	];
	function add($v){
		$vCheck1 = @$this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		$vCheck2 = [];
		if(!empty($v['mobile']))
			$vCheck2 = @$this->valid($v['mobile'],['isUnique','isMobile'],$this->table,'mobile');
		$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
		if(!$vCheckArr['result'])
			return json_encode($vCheckArr);

		$v['id_depp'] = $_SESSION['idDepp'];

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

	function mobile_repairmanList(){
		return $this->listAllDefault($this->table,['id','name']);
	}
}
$mobile_repairman = new mobile_repairman();
// for($i = 0; $i<50000; $i++)
// 	dsh($mobile_repairman->add(['name'=>'Math'.rand().rand(),'detail'=>'for test']));


?>