<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class store extends database{
	private $table = 'store';
	private $struct = ['self'=>['id','id_product','qty','id_depp'],
	'product'=>['source'=>'id_product','target'=>'id','column'=>'name','search'=>['name','code']],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name']]
	];

	function add($idProduct,$idDepp,$qty){
		try{
			$sql = "SELECT id FROM store WHERE id_depp = '$idDepp' AND id_product = '$idProduct'";
			$result = $this->pdo->query($sql);
			$idStore = $result->fetch(PDO::FETCH_ASSOC)['id'];

			if($idStore){
				$sql = "UPDATE store SET qty = qty + $qty WHERE id = $idStore;";
				$this->pdo->query($sql);
			}
			else{
				$sql = "INSERT INTO store(id_depp,id_product,qty) VALUES($idDepp,$idProduct,$qty)";
				$this->pdo->query($sql);
			}	
			return true;
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function remove($idProduct,$idDepp,$qty){
		try{
			$sql = "SELECT id FROM store WHERE id_depp = '$idDepp' AND id_product = '$idProduct'";
			$result = $this->pdo->query($sql);
			$idStore = $result->fetch(PDO::FETCH_ASSOC)['id'];

			if($idStore){
				$sql = "UPDATE store SET qty = qty - $qty WHERE id = $idStore;";
				$this->pdo->query($sql);
			}
			else{
				return false;
			}	
			return true;
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
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

	function delete($idProduct,$idDepp,$qty){
		try{
			$sql = "SELECT id FROM store WHERE id_depp = '$idDepp' AND id_product = '$idProduct'";
			$result = $this->pdo->query($sql);
			$idStore = $result->fetch(PDO::FETCH_ASSOC)['id'];

			if($idStore){
				$sql = "UPDATE store SET qty = qty - $qty WHERE id = $idStore;";
				$this->pdo->query($sql);
			}
			return true;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function show($v){
		try{
			if(!$this->perm($this->table,'view'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_View','extra'=>'']]]);

			$data = $this->toSave($v);

			$whereSql = '';
			$str = @$data['search'];
			if($str)
				$whereSql = " WHERE s.id_depp = '{$_SESSION['idDepp']}' AND (UPPER(c.name) LIKE UPPER('%$str%') OR UPPER(b.name) LIKE UPPER('%$str%')  OR UPPER(cat.name) LIKE UPPER('%$str%') OR UPPER(br.name) LIKE UPPER('%$str%') OR UPPER(p.name) LIKE UPPER('%$str%') OR UPPER(p.code) LIKE UPPER('%$str%') OR UPPER(d.name) LIKE UPPER('%$str%')) ";
			else
				$whereSql = " WHERE s.id_depp = '{$_SESSION['idDepp']}'";

			$sqlCount = "SELECT count(*) AS count FROM store s INNER JOIN depp d ON d.id = s.id_depp INNER JOIN product p ON p.id = s.id_product INNER JOIN branch b ON b.id = d.id_branch INNER JOIN city c ON c.id = b.id_city INNER JOIN brand br ON br.id = p.id_brand INNER JOIN cat ON cat.id = br.id_cat LEFT JOIN model m on m.id = p.id_model $whereSql ";
			$result = $this->pdo->query($sqlCount);
			$r['count'] = $result->fetch(PDO::FETCH_ASSOC)['count'];

			$sql = "SELECT s.id,s.id_depp,s.id_product,c.name AS city,b.name AS branch, concat(d.name,' ',d.code) AS depp, cat.name AS cat,br.name AS brand,m.name AS model,p.name AS product,p.code,s.qty FROM store s INNER JOIN depp d ON d.id = s.id_depp INNER JOIN product p ON p.id = s.id_product INNER JOIN branch b ON b.id = d.id_branch INNER JOIN city c ON c.id = b.id_city INNER JOIN brand br ON br.id = p.id_brand INNER JOIN cat ON cat.id = br.id_cat LEFT JOIN model m on m.id = p.id_model $whereSql ORDER BY {$data['order']} LIMIT {$data['limit']}";
			$result = $this->pdo->query($sql);
			$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$r['rows'] = $this->entityDecode($r['rows']);

			$action = $data['search']?'search':'view';

			if(SETTING::USER_ACTIVITY_READ)
				$this->record($this->table,$action,$data['search'],0,null);
			return json_encode($r);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}



		
	}

	function storeList(){
		try{
			$sql = "SELECT * FROM store";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	
	function storeListByModel($idModel){
		try{
			$idModel = $this->toSave($idModel);
			$sql = "SELECT * FROM store WHERE id_model = '$idModel'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function productQtyAll($idProduct){
		try{
			$sql = "SELECT sum(qty) as total FROM store WHERE id_product = '$idProduct'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetch(PDO::FETCH_ASSOC);
			return $rows['total'];
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	public function jardList(){
		try{
			if(!$this->perm($this->table,'view'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_View','extra'=>'']]]);

			// $idDepp = $_SESSION['idDepp'];


			$sql = "SELECT s.id,s.id_depp,s.id_product, concat(d.name,' ',d.code) AS depp, cat.name AS cat,br.name AS brand,m.name AS model,p.name AS product,p.code,s.qty,p.price_buy,s.qty*p.price_buy as total FROM store s INNER JOIN depp d ON d.id = s.id_depp INNER JOIN product p ON p.id = s.id_product  INNER JOIN brand br ON br.id = p.id_brand INNER JOIN cat ON cat.id = br.id_cat LEFT JOIN model m on m.id = p.id_model WHERE s.qty <> 0  ORDER BY total desc";
			$result = $this->pdo->query($sql);
			$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$r['rows'] = $this->entityDecode($r['rows']);

			$r['total'] = 0;
			$r['totalQty'] = 0;
			foreach ($r['rows'] as $value) {
				$r['total'] += $value['total'];
				$r['totalQty'] += $value['qty'];
			}

			return json_encode($r);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Jard_Not_Available']]);
		}
	}

	public function jardListDate($date){
		try{
			$date = $this->toSave($date);
			if(!$this->perm($this->table,'view'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_View','extra'=>'']]]);

			$sql = "SELECT * FROM jard_log WHERE date like '$date'";
			$result = $this->pdo->query($sql);
			$rowJardLog = $result->fetch(PDO::FETCH_ASSOC);


			$sql = "SELECT * FROM jard_log_detail WHERE id_jard_log = '{$rowJardLog['id']}'";
			$result = $this->pdo->query($sql);
			$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$r['rows'] = $this->entityDecode($r['rows']);


			$r['total'] = 0;
			$r['totalQty'] = 0;
			foreach ($r['rows'] as $value) {
				$r['total'] += $value['total'];
				$r['totalQty'] += $value['qty'];
			}

			return json_encode($r);
		}
		catch(PDOEXCEPTION $e){
			return json_encode(['result'=>false,'message'=>['Jard_Not_Available']]);
		}
	}


}
$store = new store();

// dsh($store->jardListDate('2017-03-03'));


?>