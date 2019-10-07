<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class model extends database{
	private $table = 'model';
	private $struct = ['self'=>['id','name','detail','id_brand'],
	'brand'=>['source'=>'id_brand','target'=>'id','column'=>'name','search'=>['name','detail']]
	];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_brand'=>'brand']);
		if(!$required['result'])
			return json_encode($required);

		// $vCheck1 = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		// if(!$vCheck1['result'])
		// 	return json_encode($vCheck1);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_brand'=>'brand']);
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
		if(!$this->perm($this->table,'view'))
			return false;

		$sql = "SELECT b.id,c.name FROM brand b inner join cat c on b.id_cat = c.id";
		$result = $this->pdo->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		$arrCats = [];
		foreach ($rows as $key => $value) {
			$arrCats[$value['id']] = $value['name'];
		}

		$idDepp = $_SESSION['idDepp'];
		$sql = "SELECT b.id FROM depp_cat dc inner join brand b on b.id_cat = dc.id_cat WHERE dc.id_depp = '$idDepp'";
		$result = $this->pdo->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_COLUMN);
		$rows = implode($rows,',');

		$v['condition'] = "brand.id IN ($rows)";

		$data = json_decode($this->listDefault($this->table,$v,$this->struct),true);
		foreach ($data['rows'] as $key => &$value) {
			$value['cat'] = $arrCats[$value['id_brand']];
		}

		return json_encode($data);
	}

	function modelList(){
		try{
			$sql = "SELECT * FROM model";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	
	function modelListByBrand($idBrand){
		try{
			$sql = "SELECT * FROM model WHERE id_brand = '$idBrand'";
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
$model = new model();
// for($i = 0; $i<50000; $i++)
// 	dsh($model->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_cat'=>rand(100,7400),'capabrand'=>rand(50,900)]));


?>