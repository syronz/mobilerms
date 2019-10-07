<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/depp_catClass.php');

class brand extends database{
	private $table = 'brand';
	private $struct = ['self'=>['id','name','detail','id_cat'],
	'cat'=>['source'=>'id_cat','target'=>'id','column'=>'name','search'=>['name','detail']]];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_cat'=>'cat']);
		if(!$required['result'])
			return json_encode($required);

		$vCheck1 = $this->valid($v['name'],['maxLength200','isName'],$this->table,'name');
		if(!$vCheck1['result'])
			return json_encode($vCheck1);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_cat'=>'cat']);
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
		$idDepp = $_SESSION['idDepp'];
		$sql = "SELECT id_cat FROM depp_cat WHERE depp_cat.id_depp = '$idDepp'";
		$result = $this->pdo->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_COLUMN);
		$rows = implode($rows,',');

		$v['condition'] = "cat.id IN ($rows)";
		
		if($this->perm($this->table,'view'))
			return $this->listDefault($this->table,$v,$this->struct);
	}

	function brandList(){
		try{
			$sql = "SELECT b.id,concat(c.name,' / ',b.name) as name FROM brand b inner join cat c on b.id_cat = c.id";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}



	/* start piece */
	function addDepp($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$v['id_cat'] = setting::ID_PIECE;

		$required = $this->isRequired($v,['name'=>'name','id_cat'=>'cat']);
		if(!$required['result'])
			return json_encode($required);

		$vCheck1 = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		if(!$vCheck1['result'])
			return json_encode($vCheck1);



		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function editDepp($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);

		$v['id_cat'] = setting::ID_PIECE;

		$required = $this->isRequired($v,['name'=>'name','id_cat'=>'cat']);
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

	function deleteDepp($v){
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

	function showDepp($v){
		$deppCat = new depp_cat();
		$catList = $deppCat->deppCatAuth($v['idDepp']);
		$arrCat = [];
		foreach ($catList as $key => $value) {
			array_push($arrCat, $value['id_cat']);
		}
		$strCat = join(',',$arrCat);
		$v['condition'] = " id_cat in ($strCat) ";
		return $this->listDefault($this->table,$v,$this->struct);
	}

	function brandListDepp($idDepp = null){
		if(empty($inDepp)){
			$idDepp = $_SESSION['idDepp'];
		}
		$deppCat = new depp_cat();
		$catList = $deppCat->deppCatAuth($idDepp);
		$arrCat = [];
		foreach ($catList as $key => $value) {
			array_push($arrCat, $value['id_cat']);
		}
		$strCat = join(',',$arrCat);

		$sql = "SELECT b.id,concat(b.name) as name FROM brand b inner join cat c on b.id_cat = c.id WHERE c.id in ($strCat)";
		$result = $this->pdo->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		return json_encode($rows);
	}

	function getBrandByCat($idCat){
		try{
			$idCat = $this->toSave($idCat);
			$sql = "SELECT b.id,b.name as name FROM brand b WHERE id_cat = '$idCat'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$brand = new brand();
// for($i = 0; $i<50000; $i++)
// 	dsh($brand->add(['name'=>'Dep'.rand().rand(),'detail'=>'for test','id_cat'=>rand(100,7400),'capacat'=>rand(50,900)]));


?>