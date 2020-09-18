<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/storeClass.php');
require_once realpath(__DIR__ . '/accountClass.php');
require_once realpath(__DIR__ . '/productClass.php');

class indepp extends database{
	private $table = 'indepp';
	private $struct = ['self'=>['id','id_user','id_depp','id_account','invoice','date','total','discount','dollar_rate','type','status','detail'],
	'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']],
	'account'=>['source'=>'id_account','target'=>'id','column'=>'name','search'=>['name','detail']],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name']]
	];

	function add($v){
		try{
			// dsh($v);
			$store = new store();
			$acc = new account();
			$product = new product();
			if(!$this->perm($this->table,'add'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

			$required = $this->isRequired($v,['id_account'=>'account','type'=>'type']);
			if(!$required['result'])
				return json_encode($required);

			$v['id_user'] = $this->idUser();
			$v['date'] = date('Y-m-d H:i:s',time());
			$v['total'] = 0;
			$v['totalToll'] = 0;
			$totalCompany = 0;
			foreach ($v['items'] as $value) {
				$v['total'] += $value['qty'] * $value['cost'] + $value['qty'] * $value['toll'];
				$v['totalToll'] += $value['qty'] * $value['toll'];
				$totalCompany += $value['qty'] * $value['cost'];
				if(@$value['id_product'] == null)
					return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Product_must_be_selected','extra'=>'']]]);
			}
			$v['dollar_rate'] = $this->getDollarRate($v['id_depp']);

			if($v['total'] <= 0)
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Error','extra'=>'42972324']]]);


			$sql = @"INSERT INTO indepp(id_user,id_depp,id_account,invoice,date,total,discount,dollar_rate,type,detail,total_toll,id_toll) VALUES('{$v['id_user']}','{$v['id_depp']}','{$v['id_account']}','{$v['invoice']}','{$v['date']}','{$v['total']}','{$v['discount']}','{$v['dollar_rate']}','{$v['type']}','{$v['detail']}','{$v['totalToll']}','{$v['pay']['id_toll']['id']}');";
			$this->pdo->query($sql);

			$lastIndeppId = $this->pdo->lastInsertId();

			foreach ($v['items'] as $key => $value) {
				if($value['id_product']['id']){
          $codeArr = explode("\n", $value['code']);
          /* dsh($codeArr); */
          $qty = $value['qty'];
          
          
          if (count($codeArr) > 1) {
            $qty = 1;
            /* if (count($codeArr) !=  $qty) { */
            /*   return false; */
            /* } else { */
            /*   $qty = 1; */
            /* } */
          }

          foreach($codeArr as $el) {
              $sql = "INSERT INTO indepp_extra(id_indepp,id_product,qty,cost,detail,toll,serial) 
                VALUES('$lastIndeppId','{$value['id_product']['id']}','{$qty}','{$value['cost']}','{$value['description']}','{$value['toll']}','{$el}');";
              $this->pdo->query($sql);
          }
				}
				if($v['type'] == 'active' || $v['type'] == 'freeze'){
					$product->updateProductPriceBuy($value['id_product']['id'],$value['qty'],$value['cost']+$value['toll']);
					$store->add($value['id_product']['id'],$v['id_depp'],$value['qty'], $value['code']);
				}
			}

			$totalAfterDiscount = ((100 - $v['discount']) * $v['total'])/100;

			if($v['type'] == 'active' || $v['type'] == 'freeze'){
				$dollar = @floatval($v['pay']['payDollar']);
				$dinar = @floatval($v['pay']['payDinar']);
				$acc->addAcc(setting::ID_STORE_ACCOUNT,$v['id_depp'],$v['id_account'],$totalCompany,0,'buy','',null,0,$lastIndeppId);

				$acc->addAcc(setting::ID_STORE_ACCOUNT,$v['id_depp'],$v['pay']['id_toll']['id'],$totalAfterDiscount-$totalCompany,0,'expense','',null,0,$lastIndeppId);

				// $acc->addAcc($v['id_account'],$v['id_depp'],$v['pay']['id_account']['id'],$dollar,$dinar,'payout','');
				$acc->accIndepp($v['id_account'],$v['id_depp'],$v['pay']['id_account']['id'],$dollar,$dinar,'payout','',null,$lastIndeppId);
			}

			return json_encode(['result'=>true,'id'=>$lastIndeppId]);
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function edit($v){
		try{
		$store = new store();
		$acc = new account();
		$product = new product();
		$v = $this->toSave($v);
		$idIndepp = $v['base']['id'];
		$indeppInfo = $this->getRow($this->table,$idIndepp);
		
		//check permission
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);

		//check requirment and validation for all products
		$required = $this->isRequired($v,['id_account'=>'account','type'=>'type']);
		if(!$required['result'])
			return json_encode($required);

		$v['id_user'] = $this->idUser();
		$v['date'] = date('Y-m-d H:i:s',time());
		$v['total'] = 0;
		$v['totalToll'] = 0;
		$totalNewCompany = 0;
		foreach ($v['items'] as $value) {
			$v['total'] += $value['qty'] * $value['cost'] + $value['qty'] * $value['toll'];
			$v['totalToll'] += $value['qty'] * $value['toll'];
			$totalNewCompany += $value['qty'] * $value['cost'];
			if(@$value['id_product'] == null)
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Product_must_be_selected','extra'=>'']]]);
		}

		//get dollar rate for calculation
		$v['dollar_rate'] = $this->getDollarRate($v['id_depp']);

		//check total of invoice
		if($v['total'] <= 0)
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Error','extra'=>'479632']]]);

		//get old invoice base info
		$sql = "SELECT * FROM indepp WHERE id = $idIndepp";
		$result = $this->pdo->query($sql);
		$oldBase = $result->fetch(PDO::FETCH_ASSOC);

		//update indepp with new data
		$sql = @"UPDATE indepp SET id_depp = '{$v['id_depp']}', id_account = '{$v['id_account']}', invoice = '{$v['invoice']}', total = '{$v['total']}', discount = '{$v['discount']}', dollar_rate = '{$v['dollar_rate']}', type = '{$v['type']}', detail = '{$v['detail']}',total_toll = '{$v['totalToll']}' WHERE id = $idIndepp";
		$this->pdo->query($sql);

		// prepare oldItems
		$sql = "SELECT * FROM indepp_extra WHERE id_indepp = $idIndepp"	;
		$result = $this->pdo->query($sql);
		$oldItems = $result->fetchAll(PDO::FETCH_ASSOC);

		// reset priceBuy for old items
		$totalOldCompany = 0;
		foreach ($oldItems as $key => $value) {
      $sql = "DELETE FROM store WHERE serial = '{$value['serial']}'"	;
      $result = $this->pdo->query($sql);
			$totalOldCompany += $value['qty'] * $value['cost'];
			$product->resetProductPriceBuy($value['id_product'],$value['qty'],$value['cost']+$value['toll']);
		}

		// clean indepp_extra from old items, delete all of them
		$sql = "DELETE FROM indepp_extra WHERE id_indepp = $idIndepp"	;
		$result = $this->pdo->query($sql);

		// decrese product from store
		if($oldBase['type'] == 'active' || $oldBase['type'] == 'freeze'){
			foreach ($oldItems as $value) {
				$store->delete($value['id_product'],$oldBase['id_depp'],$value['qty']);
			}
		}

		// insert new item to invoice,update price_buy and add products to store
		foreach ($v['items'] as $key => $value) {

			if($value['id_product']['id']){
        $sql = "INSERT INTO indepp_extra(id_indepp,id_product,qty,cost,detail,toll, serial) 
          VALUES('$idIndepp','{$value['id_product']['id']}','{$value['qty']}','{$value['cost']}','{$value['description']}','{$value['toll']}','{$value['code']}');";
				$this->pdo->query($sql);
			}
			if($v['type'] == 'active' || $v['type'] == 'freeze'){
				$product->updateProductPriceBuy($value['id_product']['id'],$value['qty'],$value['cost']+$value['toll']);
				$store->add($value['id_product']['id'],$v['id_depp'],$value['qty'], $value['code']);
			}
		}

		// in case of change account, reset amount 
		/*
		$oldTotalAfterDiscount = ((100 - $oldBase['discount']) * $oldBase['total'])/100;
		if(($oldBase['type'] == 'active' || $oldBase['type'] == 'freeze') && $oldBase['id_account'] != $v['id_account']){
			$acc->addAcc($oldBase['id_account'],$oldBase['id_depp'],setting::ID_STORE_ACCOUNT,$oldTotalAfterDiscount,0,'edit_buy','');
		}

		$totalAfterDiscount = ((100 - $v['discount']) * $v['total'])/100;
		if(($v['type'] == 'active' || $v['type'] == 'freeze') && $oldBase['id_account'] != $v['id_account']){
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$v['id_depp'],$v['id_account'],$totalAfterDiscount,0,'buy','');
		}
		
		if(($oldBase['id_account'] == $v['id_account']) && $oldTotalAfterDiscount != $totalAfterDiscount){
			$acc->addAcc($v['id_depp'],setting::ID_STORE_ACCOUNT,$v['id_account'],$totalAfterDiscount - $oldTotalAfterDiscount,0,'edit_buy','');
		}

		*/



		// new way for calculate changes
		$totalO = ((100 - $oldBase['discount']) * $oldBase['total'])/100;
		$totalN = ((100 - $v['discount']) * $v['total'])/100;

		$totalO = $oldBase['type'] == 'active'? $totalO : 0;
		$totalN = $v['type'] == 'active' ? $totalN : 0;

		$aO = $oldBase['id_account'];
		$aN = $v['id_account'];


		if($totalO != 0 && $aO != $aN){
			$acc->addAcc($aO,$oldBase['id_depp'],setting::ID_STORE_ACCOUNT,$totalOldCompany,0,'edit_buy','');
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$oldBase['id_depp'],$aN,$totalOldCompany,0,'buy','');


		}

		if($totalO != $totalN){
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$oldBase['id_depp'],$aN,$totalNewCompany - $totalOldCompany,0,'edit_buy','');

			$acc->addAcc(setting::ID_STORE_ACCOUNT,$v['id_depp'],$oldBase['id_toll'],$v['totalToll'] - $oldBase['total_toll'],0,'expense','');
		}

		return json_encode(['result'=>true,'id'=>$idIndepp]);
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function delete($v){
		try{
			$store = new store();
			$product = new product();
			if(!$this->perm($this->table,'delete'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Delete','extra'=>'']]]);
			$idIndepp = $v['id'];
			$sql = "SELECT id FROM acc WHERE id_indepp = '$idIndepp'";
			$result = $this->pdo->query($sql);
			$accMustDelete = $result->fetchAll(PDO::FETCH_ASSOC);
			$account = new account();
			foreach ($accMustDelete as $key => $value) {
				$account->deleteAcc($value['id']);
			}

			$old = $this->getRow($this->table,$v['id']);

			

			//get old invoice base info
			$sql = "SELECT * FROM indepp WHERE id = $idIndepp";
			$result = $this->pdo->query($sql);
			$oldBase = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT * FROM indepp_extra WHERE id_indepp = $idIndepp"	;
			$result = $this->pdo->query($sql);
			$oldItems = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "DELETE FROM indepp_extra WHERE id_indepp = $idIndepp"	;
			$result = $this->pdo->query($sql);

			foreach ($oldItems as $key => $value) {
				$product->resetProductPriceBuy($value['id_product'],$value['qty'],$value['cost']+$value['toll']);
			}

			foreach ($oldItems as $value) {
				$store->delete($value['id_product'],$oldBase['id_depp'],$value['qty']);
			}

			

			$sql = "SELECT id FROM acc WHERE id_indepp = '$idIndepp'";
			$result = $this->pdo->query($sql);
			$accMustDelete = $result->fetchAll(PDO::FETCH_ASSOC);

			foreach ($accMustDelete as $key => $value) {
				$account->deleteAcc($value['id']);
			}






			$sql = "DELETE FROM {$this->table} WHERE id = '{$v['id']}'";
			$this->pdo->query($sql);

			$this->record($this->table,'delete','',$v['id'],['old'=>$old]);
			return json_encode(['result'=>true]);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Delete_Not_Available','extra'=>'You_Must_Delete_Transaction']]]);
		}
	}

	function show($v){
		$v['condition'] = ' indepp.id_depp = '.$_SESSION['idDepp'];
		if($this->perm($this->table,'view'))
			return $this->listDefault($this->table,$v,$this->struct);
	}

	function indeppList(){
		try{
			$sql = "SELECT * FROM indepp";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function indeppPrint($id){
		try{
			$id = $this->toSave($id);
			$sql = "SELECT a.name as account,i.date,i.detail,i.discount,i.dollar_rate,i.id,i.invoice,i.status,i.total,i.type as indeppType,u.name as user,d.name as depp,d.code as deppCode,b.name as branch,d.phone,d.address,d.id as id_depp,a.id as id_account,a.mobile as accountMobile,a.phone as accountPhone, a.address as accountAddress,a.serial as accountSerial FROM indepp i INNER JOIN account a ON i.id_account = a.id inner join user u on u.id = i.id_user inner join depp d on d.id = i.id_depp inner join branch b on b.id = d.id_branch  WHERE i.id = '$id'";
			$result = $this->pdo->query($sql);
			$row['base'] = $result->fetch(PDO::FETCH_ASSOC);
			// $row['base']['date'] = 'بەروار: 2015-12-29 11:25:01'

			$sql = "SELECT p.name as product,m.name as model,i.qty,i.cost,i.detail as description,i.serial as code,b.name as brand, i.toll FROM indepp_extra i inner join product p on p.id = i.id_product left join model m on m.id = p.id_model inner join brand b on b.id = p.id_brand WHERE i.id_indepp = '$id'";
			$result = $this->pdo->query($sql);
			$row['items'] = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "SELECT a.id AS id_acc,a.id_account,a.id_account_other,ac.name,a.dollar,a.dinar,a.dollar_rate,a.in_dollar,a.depp_balance,a.date FROM acc_indepp ai INNER JOIN acc a ON ai.id_acc = a.id INNER JOIN account ac ON ac.id = a.id_account_other WHERE ai.id_indepp = '$id' ";
			$result = $this->pdo->query($sql);
			$row['pays'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$total = 0;
			foreach ($row['pays'] as &$value) {
				$total += $value['in_dollar'];
				@$value['total'] += $total;
			}

			$row['base']['totalPays'] = 0;
			foreach ($row['pays'] as $key => $value) {
				$row['base']['totalPays'] += $value['in_dollar'];
			}
// dsh($sql,$row['pays']);
			
			return json_encode($row);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function indeppPayins($idIndepp){
		try{
			$idIndepp = $this->toSave($idIndepp);
			$sql = "SELECT a.id AS id_acc,a.id_account,a.id_account_other,ac.name,a.dollar,a.dinar,a.dollar_rate,a.in_dollar,a.depp_balance,a.date FROM acc_indepp ai INNER JOIN acc a ON ai.id_acc = a.id INNER JOIN account ac ON ac.id = a.id_account_other WHERE ai.id_indepp = '$idIndepp' ";
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

	function savePayout($v){
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
			@$acount->accIndepp($v['id_account'],$v['id_depp'],$v['id_account_other'],$v['payDollar'],$v['payDinar'],'payout',$v['detail'],null,$v['id_indepp']);

			return json_encode(['result'=>true]);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	
	function indeppEdit($id){
		try{
			$id = $this->toSave($id);
			$sql = "SELECT a.name as account,i.date,i.detail,i.discount,i.dollar_rate,i.id,i.invoice,i.status,i.total,i.type as indeppType,u.name as user,d.name as depp,d.code as deppCode,b.name as branch,d.phone,d.address,d.id as id_depp,a.id as id_account,a.mobile as accountMobile,a.phone as accountPhone, a.address as accountAddress,a.serial as accountSerial,a.type as accountType FROM indepp i INNER JOIN account a ON i.id_account = a.id inner join user u on u.id = i.id_user inner join depp d on d.id = i.id_depp inner join branch b on b.id = d.id_branch  WHERE i.id = '$id'";
			$result = $this->pdo->query($sql);
			$row['base'] = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT p.name as product,m.name as model,i.qty,i.cost,i.detail as description,i.serial as code,b.name as brand,b.id as id_brand,m.id as id_model,p.id as id_product,i.toll FROM indepp_extra i inner join product p on p.id = i.id_product left join model m on m.id = p.id_model inner join brand b on b.id = p.id_brand WHERE i.id_indepp = '$id'";
			$result = $this->pdo->query($sql);
			$row['items'] = $result->fetchAll(PDO::FETCH_ASSOC);

			foreach ($row['items'] as &$value) {
				$value['id_brand'] = ["id"=>intval($value['id_brand']),"name"=>$value['brand']];

				$value['modelList'] = [["id"=>intval($value['id_model']),"name"=>$value['model']]];
				$value['id_model'] = ["id"=>intval($value['id_model']),"name"=>$value['model']];

				$value['productList'] = [["id"=>intval($value['id_product']),"name"=>$value['product']]];
				$value['id_product'] = ["id"=>intval($value['id_product']),"name"=>$value['product']];
			}

			// $row['items'][0]['modelList'] = [["id"=>1,"name"=>"One M7"]];
			// $row['items'][0]['id_model'] = ["id"=>1,"name"=>"One M7"];

// dsh($row);
			
			return json_encode($row);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$indepp = new indepp();

// $indepp->indeppEdit(81);


// $sampeArr = [
// 'accountType' => 'company',
// 'type' => 'just_show',
// 'pay' => ['id_account' => ['id' => '126364','name' => 'باوەر دەستی']],
// 'id_account' => '3','discount' => 0,
// 'items' => [0 => ['qty' => 100,'description' => '','cost' => '1.00','id_brand' => ['id' => '3','name' => 'Piece / HTC',],'id_model' => ['id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,],'id_product' => ['id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,],'modelList' => [0 => ['id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,],],'productList' => [0 => ['id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,],],'code' => '985',],],
// 'id_depp' => '1','invoice' => '852','dollarRate' => 1227.5,
// ];

// $sampeArr =  array ('accountType' => 'company','type' => 'active','pay' =>array ('id_account' =>array ('id' => '10','name' => 'سندوقی های',),'payDollar' => '1',  ),  'id_account' => '1',  'discount' => 0,  'items' =>   array (0 =>array ('qty' => 100,'description' => '','cost' => '1.00','id_brand' =>array ('id' => '3','name' => 'Piece / HTC',),'id_model' =>array ('id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,),'id_product' =>array ('id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,),'modelList' =>array (0 =>array ('id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,),),'productList' =>array (0 =>array ('id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,),),'code' => '985',    ),  ),  'id_depp' => '1',  'invoice' => '30',  'dollarRate' => 1227.5,);
// dsh($indepp->add($sampeArr));



?>
