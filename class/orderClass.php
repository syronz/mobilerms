<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/accountClass.php');

class order extends database{
	private $table = 'ordered';
	private $struct = ['self'=>['id','id_user','id_depp','id_account','invoice','date','date_back','total','discount','dollar_rate','type','status','detail'],
	'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']],
	'account'=>['source'=>'id_account','target'=>'id','column'=>'name','search'=>['name','detail']],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name']]
	];

	function addMobile($v){
		try{
			// dsh($v);
			// return 0;
			$acc = new account();
			if(!$this->perm($this->table,'add'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

			$required = $this->isRequired($v,['id_account'=>'account','type'=>'type']);
			if(!$required['result'])
				return json_encode($required);

			$v['id_user'] = $this->idUser();
			$v['date'] = date('Y-m-d H:i:s',time());
			$v['total'] = 0;
			foreach ($v['items'] as $value) {
				$v['total'] += $value['cost'];
			}
			$v['dollar_rate'] = $this->getDollarRate($v['id_depp']);


			if($v['total'] < 0)
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Error','extra'=>'63298532']]]);


			$v['invoice'] = json_decode($this->invoiceGenerate(),TRUE)['invoice'];

			$sql = @"INSERT INTO ordered(id_user,id_depp,id_account,invoice,date,total,discount,dollar_rate,type,detail) VALUES('{$v['id_user']}','{$v['id_depp']}','{$v['id_account']}','{$v['invoice']}','{$v['date']}','{$v['total']}','{$v['discount']}','{$v['dollar_rate']}','{$v['type']}','{$v['detail']}');";
			$this->pdo->query($sql);

			$lastorderId = $this->pdo->lastInsertId();

			foreach ($v['items'] as $key => $value) {
				if($value['id_model']['id']){
					$sql = @"INSERT INTO order_extra(id_order,id_model,id_repairman,iemi,cost,detail) VALUES('$lastorderId','{$value['id_model']['id']}','{$value['id_repairman']['id']}','{$value['iemi']}','{$value['cost']}','{$value['description']}');";
					$this->pdo->query($sql);
				}
			}

			$totalAfterDiscount = ((100 - $v['discount']) * $v['total'])/100;

			if($v['type'] == 'active' || $v['type'] == 'freeze'){
				$dollar = @floatval($v['pay']['payDollar']);
				$dinar = @floatval($v['pay']['payDinar']);
				$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,$totalAfterDiscount,0,'sell','');

				$acc->accOrder($v['pay']['id_account']['id'],$v['id_depp'],$v['id_account'],$dollar,$dinar,'payin','',null,$lastorderId);
			}

			return json_encode(['result'=>true,'id'=>$lastorderId]);
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function edit($v){
		$store = new store();
		$acc = new account();
		$v = $this->toSave($v);
		$idorder = $v['base']['id'];

		
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		$required = $this->isRequired($v,['id_account'=>'account','type'=>'type']);
		if(!$required['result'])
			return json_encode($required);

		$v['id_user'] = $this->idUser();
		$v['date'] = date('Y-m-d H:i:s',time());
		$v['total'] = 0;
		foreach ($v['items'] as $value) {
			$v['total'] += $value['qty'] * $value['cost'];
			if(@$value['id_product'] == null)
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Product_must_be_selected','extra'=>'']]]);
		}
		$v['dollar_rate'] = $this->getDollarRate($v['id_depp']);


		if($v['total'] <= 0)
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Error','extra'=>'4274452']]]);

		$sql = "SELECT * FROM order WHERE id = $idorder";
		$result = $this->pdo->query($sql);
		$oldBase = $result->fetch(PDO::FETCH_ASSOC);

		$sql = @"UPDATE order SET id_depp = '{$v['id_depp']}', id_account = '{$v['id_account']}', total = '{$v['total']}', discount = '{$v['discount']}', dollar_rate = '{$v['dollar_rate']}', type = '{$v['type']}', detail = '{$v['detail']}' WHERE id = $idorder";
		$this->pdo->query($sql);


		$sql = "SELECT * FROM order_extra WHERE id_order = $idorder"	;
		$result = $this->pdo->query($sql);
		$oldItems = $result->fetchAll(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM order_extra WHERE id_order = $idorder"	;
		$result = $this->pdo->query($sql);

		if($oldBase['type'] == 'active' || $oldBase['type'] == 'freeze'){
			foreach ($oldItems as $value) {
				$store->add($value['id_product'],$oldBase['id_depp'],$value['qty']);
			}
		}

		foreach ($v['items'] as $key => $value) {
			if($value['id_product']['id']){
				$sql = "INSERT INTO order_extra(id_order,id_product,qty,cost,detail) VALUES('$idorder','{$value['id_product']['id']}','{$value['qty']}','{$value['cost']}','{$value['description']}');";
				$this->pdo->query($sql);
			}
			if($v['type'] == 'active' || $v['type'] == 'freeze'){
				$store->delete($value['id_product']['id'],$v['id_depp'],$value['qty']);
			}
		}

		$oldTotalAfterDiscount = ((100 - $oldBase['discount']) * $oldBase['total'])/100;
		if(($oldBase['type'] == 'active' || $oldBase['type'] == 'freeze') && $oldBase['id_account'] != $v['id_account']){
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$oldBase['id_depp'],$oldBase['id_account'],$oldTotalAfterDiscount,0,'edit_sell','');
		}

		$totalAfterDiscount = ((100 - $v['discount']) * $v['total'])/100;
		if(($v['type'] == 'active' || $v['type'] == 'freeze') && $oldBase['id_account'] != $v['id_account']){
			$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,$totalAfterDiscount,0,'buy','');
		}

		if($totalAfterDiscount > $oldTotalAfterDiscount){
			$sql = "UPDATE order SET status = 'progress' WHERE id = '$idorder'";
			$this->pdo->query($sql);
		}

		if(($oldBase['id_account'] == $v['id_account']) && $oldTotalAfterDiscount != $totalAfterDiscount){
			$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,$totalAfterDiscount - $oldTotalAfterDiscount,0,'edit_sell','');
		}

		return json_encode(['result'=>true,'id'=>$idorder]);
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

	function orderList(){
		try{
			$sql = "SELECT * FROM order";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function orderPrint($id){
		try{
			$id = $this->toSave($id);
			$sql = "SELECT a.name as account,i.date,i.detail,i.discount,i.dollar_rate,i.id,i.invoice,i.status,i.total,i.type as orderType,u.name as user,d.name as depp,d.code as deppCode,b.name as branch,d.phone,d.address,d.id as id_depp,a.id as id_account,a.mobile as accountMobile,a.phone as accountPhone, a.address as accountAddress,a.serial as accountSerial FROM ordered i INNER JOIN account a ON i.id_account = a.id inner join user u on u.id = i.id_user inner join depp d on d.id = i.id_depp inner join branch b on b.id = d.id_branch  WHERE i.id = '$id'";
			$result = $this->pdo->query($sql);
			$row['base'] = $result->fetch(PDO::FETCH_ASSOC);
			

			$sql = "SELECT m.name as model,i.cost,i.detail as description,b.name as brand,i.iemi,r.name FROM order_extra i inner join model m on m.id = i.id_model inner join brand b on b.id = m.id_brand inner join repairman r on r.id = i.id_repairman WHERE i.id_order = '$id'";
			$result = $this->pdo->query($sql);
			$row['items'] = $result->fetchAll(PDO::FETCH_ASSOC);
			

			$sql = "SELECT a.id AS id_acc,a.id_account,a.id_account_other,ac.name,a.dollar,a.dinar,a.dollar_rate,a.in_dollar,a.depp_balance,a.date FROM acc_order ai INNER JOIN acc a ON ai.id_acc = a.id INNER JOIN account ac ON ac.id = a.id_account_other WHERE ai.id_order = '$id' ";
			$result = $this->pdo->query($sql);
			$row['pays'] = $result->fetchAll(PDO::FETCH_ASSOC);
	// dsh($sql,$row['pays']);		
			$total = 0;
			foreach ($row['pays'] as &$value) {
				$total += $value['in_dollar'];
				// @$value['total'] += $total;
			}

			$row['base']['totalPays'] = $total;

// dsh($sql,$row['pays']);
			
			return json_encode($row);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function orderPayins($idorder){
		try{
			$idorder = $this->toSave($idorder);
			$sql = "SELECT a.id AS id_acc,a.id_account,a.id_account_other,ac.name,a.dollar,a.dinar,a.dollar_rate,a.in_dollar,a.depp_balance,a.date FROM acc_order ai INNER JOIN acc a ON ai.id_acc = a.id INNER JOIN account ac ON ac.id = a.id_account_other WHERE ai.id_order = '$idorder' ";
			$result = $this->pdo->query($sql);
			$row['pays'] = $result->fetchAll(PDO::FETCH_ASSOC);

			$row['base']['totalPays'] = 0;
			foreach ($row['pays'] as $key => $value) {
				$row['base']['totalPays'] += $value['in_dollar'];
			}
			
			return json_encode($row);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function savePayin($v){
		try{
			$v = $this->toSave($v);
			if(!$this->perm($this->table,'add'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

			@$v['payDollar'] = floatval($v['payDollar']);
			@$v['payDinar'] = floatval($v['payDinar']);

			if($v['payDollar'] + $v['payDinar'] == 0)
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Must_Fill_Dollar_or_Dinar','extra'=>'']]]);

			$required = $this->isRequired($v,['id_account'=>'account','id_account_other'=>'less_from_account']);
			if(!$required['result'])
				return json_encode($required);

			$acount = new account();
			@$acount->accorder($v['id_account_other'],$v['id_depp'],$v['id_account'],$v['payDollar'],$v['payDinar'],'payin',$v['detail'],null,$v['id_order']);

			return json_encode(['result'=>true]);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	
	function orderEdit($id){
		try{
			$id = $this->toSave($id);
			$sql = "SELECT a.name as account,i.date,i.detail,i.discount,i.dollar_rate,i.id,i.invoice,i.status,i.total,i.type as orderType,u.name as user,d.name as depp,d.code as deppCode,b.name as branch,d.phone,d.address,d.id as id_depp,a.id as id_account,a.mobile as accountMobile,a.phone as accountPhone, a.address as accountAddress,a.serial as accountSerial,a.type as accountType FROM order i INNER JOIN account a ON i.id_account = a.id inner join user u on u.id = i.id_user inner join depp d on d.id = i.id_depp inner join branch b on b.id = d.id_branch  WHERE i.id = '$id'";
			$result = $this->pdo->query($sql);
			$row['base'] = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT p.name as product,m.name as model,i.qty,i.cost,i.detail as description,p.code as code,b.name as brand,b.id as id_brand,m.id as id_model,p.id as id_product FROM order_extra i inner join product p on p.id = i.id_product inner join model m on m.id = p.id_model inner join brand b on b.id = p.id_brand WHERE i.id_order = '$id'";
			$result = $this->pdo->query($sql);
			$row['items'] = $result->fetchAll(PDO::FETCH_ASSOC);

			foreach ($row['items'] as &$value) {
				$value['id_brand'] = ["id"=>intval($value['id_brand']),"name"=>$value['brand']];

				$value['modelList'] = [["id"=>intval($value['id_model']),"name"=>$value['model']]];
				$value['id_model'] = ["id"=>intval($value['id_model']),"name"=>$value['model']];

				$value['productList'] = [["id"=>intval($value['id_product']),"name"=>$value['product']]];
				$value['id_product'] = ["id"=>intval($value['id_product']),"name"=>$value['product']];
			}
			return json_encode($row);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function invoiceGenerate(){
		try{
			$idDepp = $_SESSION['idDepp'];
			$sql = "SELECT invoice FROM ordered WHERE id_depp = '$idDepp' ORDER BY id DESC LIMIT 1";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC)['invoice'];

			$sql = "SELECT code FROM depp WHERE id = '$idDepp'";
			$result = $this->pdo->query($sql);
			$codeDepp = $result->fetch(PDO::FETCH_ASSOC)['code'];

			if(!empty($row)){
				$expl = explode('-', $row);
				$series = @intval($expl[1]) + 1;
				$series = substr("0000000$series", -7);
				$invoice = "$codeDepp-$series";
			}
			else{
				$invoice = "$codeDepp-0000001";
			}

			return json_encode(['invoice'=>$invoice]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$order = new order();

// $sampeArr =  array ('accountType' => 'company','type' => 'active','pay' =>array ('id_account' =>array ('id' => '10','name' => 'سندوقی های',),'payDollar' => '1',  ),  'id_account' => '1',  'discount' => 0,  'items' =>   array (0 =>array ('qty' => 100,'description' => '','cost' => '1.00','id_brand' =>array ('id' => '3','name' => 'Piece / HTC',),'id_model' =>array ('id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,),'id_product' =>array ('id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,),'modelList' =>array (0 =>array ('id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,),),'productList' =>array (0 =>array ('id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,),),'code' => '985',    ),  ),  'id_depp' => '1',  'invoice' => '30',  'dollarRate' => 1227.5,);
// dsh($order->add($sampeArr));

// echo $order->invoiceGenerate();
?>