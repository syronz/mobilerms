<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/storeClass.php');

class product extends database{
	private $table = 'product';
	private $struct = ['self'=>['id','name','detail','id_brand','code','price','price_buy','id_model'],
	'brand'=>['source'=>'id_brand','target'=>'id','column'=>'name','search'=>['name','detail']],
	'model'=>['source'=>'id_model','target'=>'id','column'=>'name','search'=>['name']]
	];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['name'=>'name','id_brand'=>'brand']);
		if(!$required['result'])
			return json_encode($required);

		// $vCheck1 = @$this->valid($v['code'],['isUnique','maxLength200','isName'],$this->table,'code');
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
		// if($this->perm($this->table,'view'))
		// 	return $this->listDefault($this->table,$v,$this->struct);

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

	function productList(){
		try{
			$sql = "SELECT * FROM product";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	
	function productListByModel($idModel){
		try{
			$modelInfo = $this->getRow('model',$idModel);
			$idModel = $this->toSave($idModel);
			$sql = "SELECT * FROM product WHERE id_model = '$idModel' and id_brand = '{$modelInfo['id_brand']}'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function productBycode($code){
		try{
			$code = $this->toSave($code);
			$sql = "SELECT p.id as id_product,p.name as product,p.price as cost,m.name as model,b.name as brand,b.id as id_brand,m.id as id_model,p.price_buy  FROM product p left join model m on m.id = p.id_model inner join brand b on b.id = p.id_brand WHERE p.code = '$code'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetch(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function productListByBrand($idBrand){
		try{
			$idBrand = $this->toSave($idBrand);
			$sql = "SELECT * FROM product WHERE id_brand = '$idBrand'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function updateProductPriceBuy($idProduct,$newQty,$cost){
		try{
			$store = new store();
			$oldQty = $store->productQtyAll($idProduct);
			$productInfo = $this->getRow('product',$idProduct);


			if($oldQty + $newQty)
				$newPriceBuy = ($oldQty * $productInfo['price_buy'] + $newQty * $cost) / ($oldQty + $newQty);
			else
				$newPriceBuy = $productInfo['price_buy'];

			// dsh($idProduct,$newQty,$cost,$newPriceBuy);

			$sql = "UPDATE product SET price_buy = '$newPriceBuy' WHERE id = '$idProduct'";
			$result = $this->pdo->query($sql);

			return true;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function resetProductPriceBuy($idProduct,$newQty,$cost){
		try{
			$store = new store();
			$oldQty = $store->productQtyAll($idProduct);
			$productInfo = $this->getRow('product',$idProduct);

			if($oldQty - $newQty)
				$resetPriceBuy = ($oldQty * $productInfo['price_buy'] - $newQty * $cost) / ($oldQty - $newQty);
			else
				$resetPriceBuy = $productInfo['price_buy'];

			// dsh($oldQty,$productInfo['price_buy'],$resetPriceBuy);

			$sql = "UPDATE product SET price_buy = '$resetPriceBuy' WHERE id = '$idProduct'";
			$result = $this->pdo->query($sql);

			return true;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$product = new product();

// $product->updateProductPriceBuy(15,10,200);


?>