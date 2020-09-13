<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/storeClass.php');
require_once realpath(__DIR__ . '/accountClass.php');

class outdepp extends database{
	private $table = 'outdepp';
	private $struct = ['self'=>['id','id_user','id_depp','id_account','invoice','date','total','discount','dollar_rate','type','status','detail'],
	'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']],
	'account'=>['source'=>'id_account','target'=>'id','column'=>'name','search'=>['name','detail']],
	'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name']]
	];

	function add($v){
		try{
			$store = new store();
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
				$v['total'] += $value['qty'] * $value['cost'];
				if(@$value['id_product'] == null)
					return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Product_must_be_selected','extra'=>'']]]);
			}
			$v['dollar_rate'] = $this->getDollarRate($v['id_depp']);


			if($v['total'] <= 0)
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Error','extra'=>'TotalIsZero']]]);


			$v['invoice'] = json_decode($this->invoiceGenerate(),TRUE)['invoice'];
			// dsh($v['invoice']);


			$sql = @"INSERT INTO outdepp(id_user,id_depp,id_account,invoice,date,total,discount,dollar_rate,type,detail) VALUES('{$v['id_user']}','{$v['id_depp']}','{$v['id_account']}','{$v['invoice']}','{$v['date']}','{$v['total']}','{$v['discount']}','{$v['dollar_rate']}','{$v['type']}','{$v['detail']}');";
			$this->pdo->query($sql);

			$lastOutdeppId = $this->pdo->lastInsertId();
			$totalLessStore = 0;

			foreach ($v['items'] as $key => $value) {
				$productInfo = $this->getRow('product',$value['id_product']['id']);
				$totalLessStore += $productInfo['price_buy'] * $value['qty'];

				

				if($value['id_product']['id']){
					$profitOne = $value['cost'] - $productInfo['price_buy'];
          $sql = "INSERT INTO outdepp_extra(id_outdepp,id_product,qty,cost,detail,profit_one, serial) 
            VALUES('$lastOutdeppId','{$value['id_product']['id']}','{$value['qty']}','{$value['cost']}','{$value['description']}','$profitOne', '{$value['code']}');";
					$this->pdo->query($sql);
				}
				if($v['type'] == 'active' || $v['type'] == 'freeze'){
					if(!$store->remove($value['id_product']['id'],$v['id_depp'],$value['qty'], $value['code']))
						return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Error','extra'=>'This Item not exist in store']]]);
				}
			}

			$totalAfterDiscount = ((100 - $v['discount']) * $v['total'])/100;

			if($v['type'] == 'active' || $v['type'] == 'freeze'){
				$dollar = @floatval($v['pay']['payDollar']);
				$dinar = @floatval($v['pay']['payDinar']);
				$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,[$totalAfterDiscount,-$totalLessStore],0,'sell','',null,$lastOutdeppId,0);



				// $acc->addAcc($v['id_account'],$v['id_depp'],$v['pay']['id_account']['id'],$dollar,$dinar,'payout','');
				$acc->accOutdepp($v['pay']['id_account']['id'],$v['id_depp'],$v['id_account'],$dollar,$dinar,'payout','',null,$lastOutdeppId);
			}
			return json_encode(['result'=>true,'id'=>$lastOutdeppId]);
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function edit($v){
		$store = new store();
		$acc = new account();
		$v = $this->toSave($v);
		$idoutdepp = $v['base']['id'];

		
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

		$sql = "SELECT * FROM outdepp WHERE id = $idoutdepp";
		$result = $this->pdo->query($sql);
		$oldBase = $result->fetch(PDO::FETCH_ASSOC);

		$sql = @"UPDATE outdepp SET id_depp = '{$v['id_depp']}', id_account = '{$v['id_account']}', total = '{$v['total']}', discount = '{$v['discount']}', dollar_rate = '{$v['dollar_rate']}', type = '{$v['type']}', detail = '{$v['detail']}' WHERE id = $idoutdepp";
		$this->pdo->query($sql);


		$sql = "SELECT * FROM outdepp_extra WHERE id_outdepp = $idoutdepp"	;
		$result = $this->pdo->query($sql);
		$oldItems = $result->fetchAll(PDO::FETCH_ASSOC);

		$sql = "DELETE FROM outdepp_extra WHERE id_outdepp = $idoutdepp"	;
		$result = $this->pdo->query($sql);

		
		if($oldBase['type'] == 'active' || $oldBase['type'] == 'freeze'){
			foreach ($oldItems as $value) {
				$store->add($value['id_product'],$oldBase['id_depp'],$value['qty']);
			}
		}

		$oldTotalLessStore = 0;
		foreach ($oldItems as $value) {
			$productInfo = $this->getRow('product',$value['id_product']);
			$oldTotalLessStore += $productInfo['price_buy'] * $value['qty'];
			// dsh()
		}
		
		$totalLessStore = 0;
		foreach ($v['items'] as $key => $value) {
			$productInfo = $this->getRow('product',$value['id_product']['id']);
			$totalLessStore += $productInfo['price_buy'] * $value['qty'];

			if($value['id_product']['id']){
				$sql = "INSERT INTO outdepp_extra(id_outdepp,id_product,qty,cost,detail) VALUES('$idoutdepp','{$value['id_product']['id']}','{$value['qty']}','{$value['cost']}','{$value['description']}');";
				$this->pdo->query($sql);
			}
			if($v['type'] == 'active' || $v['type'] == 'freeze'){
				$store->delete($value['id_product']['id'],$v['id_depp'],$value['qty']);
			}
		}


		/*
		$oldTotalAfterDiscount = ((100 - $oldBase['discount']) * $oldBase['total'])/100;
		if(($oldBase['type'] == 'active' || $oldBase['type'] == 'freeze') && $oldBase['id_account'] != $v['id_account']){
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$oldBase['id_depp'],$oldBase['id_account'],[$oldTotalLessStore,-$oldTotalAfterDiscount],0,'edit_sell','');
		}

		$totalAfterDiscount = ((100 - $v['discount']) * $v['total'])/100;
		if(($v['type'] == 'active' || $v['type'] == 'freeze') && $oldBase['id_account'] != $v['id_account']){
			$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,[$totalAfterDiscount,-$totalLessStore],0,'sell','');
		}

		if($totalAfterDiscount > $oldTotalAfterDiscount){
			$sql = "UPDATE outdepp SET status = 'progress' WHERE id = '$idoutdepp'";
			$this->pdo->query($sql);
		}

		if(($oldBase['id_account'] == $v['id_account']) && $oldTotalAfterDiscount != $totalAfterDiscount){
			$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,[$totalAfterDiscount - $oldTotalAfterDiscount,$oldTotalLessStore - $totalLessStore],0,'edit_sell','');
		}

		if($oldBase['type'] == 'active' && in_array($v['type'],['cancel','just_show','freeze'])){
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$oldBase['id_depp'],$oldBase['id_account'],[$oldTotalLessStore,-$oldTotalAfterDiscount],0,'edit_sell','');
		}
		if($v['type'] == 'active' && in_array($oldBase['type'],['cancel','just_show','freeze'])){
			$acc->addAcc($v['id_account'],$v['id_depp'],setting::ID_STORE_ACCOUNT,[$oldTotalAfterDiscount,-$oldTotalLessStore],0,'edit_sell','');
		}
		*/

		// new way for calculate changes
		$totalO = ((100 - $oldBase['discount']) * $oldBase['total'])/100;
		$totalN = ((100 - $v['discount']) * $v['total'])/100;

		$totalO = $oldBase['type'] == 'active'? $totalO : 0;
		$totalN = $v['type'] == 'active' ? $totalN : 0;
		$oldTotalLessStore = $oldBase['type'] == 'active'? $oldTotalLessStore : 0;
		$totalLessStore = $v['type'] == 'active' ? $totalLessStore : 0;

		$aO = $oldBase['id_account'];
		$aN = $v['id_account'];


		if($totalO != 0 && $aO != $aN){
			$acc->addAcc($aO,$oldBase['id_depp'],setting::ID_STORE_ACCOUNT,[-$totalO,$oldTotalLessStore],0,'edit_sell','');
			$acc->addAcc(setting::ID_STORE_ACCOUNT,$oldBase['id_depp'],$aN,[-$oldTotalLessStore,$totalO],0,'sell','');
		}

		if($totalO != $totalN){
			$acc->addAcc($aN,$oldBase['id_depp'],setting::ID_STORE_ACCOUNT,[$totalN - $totalO,$oldTotalLessStore - $totalLessStore],0,'edit_sell','');
			// dsh($totalO,$totalN,$totalLessStore,$oldTotalLessStore);
		}

		

		return json_encode(['result'=>true,'id'=>$idoutdepp]);
	}

	function delete($v){
		try{
			$store = new store();
			if(!$this->perm($this->table,'delete'))
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Delete','extra'=>'']]]);
			$idoutdepp = $v['id'];
			
			$sql = "SELECT id FROM acc WHERE id_outdepp = '$idoutdepp'";
			$result = $this->pdo->query($sql);
			$accMustDelete = $result->fetchAll(PDO::FETCH_ASSOC);
			$account = new account();
			foreach ($accMustDelete as $key => $value) {
				$account->deleteAcc($value['id']);
			}

			$old = $this->getRow($this->table,$v['id']);

			

			$sql = "SELECT * FROM outdepp WHERE id = $idoutdepp";
			$result = $this->pdo->query($sql);
			$oldBase = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT * FROM outdepp_extra WHERE id_outdepp = $idoutdepp"	;
			$result = $this->pdo->query($sql);
			$oldItems = $result->fetchAll(PDO::FETCH_ASSOC);

			$sql = "DELETE FROM outdepp_extra WHERE id_outdepp = $idoutdepp"	;
			$result = $this->pdo->query($sql);

		
			foreach ($oldItems as $value) {
				$store->add($value['id_product'],$oldBase['id_depp'],$value['qty']);
			}

			





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
		$v['condition'] = ' outdepp.id_depp = '.$_SESSION['idDepp'];
		if($this->perm($this->table,'view'))
			return $this->listDefault($this->table,$v,$this->struct);
	}

	function outdeppList(){
		try{
			$sql = "SELECT * FROM outdepp";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	function outdeppPrint($id){
		try{
			$account = new account();
			$id = $this->toSave($id);
			$sql = "SELECT a.name as account,i.date,i.detail,i.discount,i.dollar_rate,i.id,i.invoice,i.status,i.total,i.type as outdeppType,u.name as user,d.name as depp,d.code as deppCode,b.name as branch,d.phone,d.address,d.id as id_depp,a.id as id_account,a.mobile as accountMobile,a.phone as accountPhone, a.address as accountAddress,a.serial as accountSerial FROM outdepp i INNER JOIN account a ON i.id_account = a.id inner join user u on u.id = i.id_user inner join depp d on d.id = i.id_depp inner join branch b on b.id = d.id_branch  WHERE i.id = '$id'";
			$result = $this->pdo->query($sql);
			$row['base'] = $result->fetch(PDO::FETCH_ASSOC);
			// $row['base']['date'] = 'بەروار: 2015-12-29 11:25:01'

			$row['base']['pre_balance'] = floatval($account->lastBalanceBeforeDate($row['base']['id_account'],$row['base']['id_depp'],$row['base']['date'])['depp_balance']);

			$sql = "SELECT p.name as product,m.name as model,i.qty,i.cost,i.detail as description,i.serial as code,b.name as brand FROM outdepp_extra i inner join product p on p.id = i.id_product left join model m on m.id = p.id_model inner join brand b on b.id = p.id_brand WHERE i.id_outdepp = '$id'";


      /* $sql = "SELECT p.name as product,m.name as model,i.qty,i.cost,i.detail as description, */
      /*   s.serial as code,b.name as brand FROM outdepp_extra i */ 
      /*   inner join product p on p.id = i.id_product */ 
      /*   left join model m on m.id = p.id_model */ 
      /*   inner join brand b on b.id = p.id_brand */ 
      /*   inner join store s on s.id_product = p.id */
      /*   WHERE i.id_outdepp = '$id'"; */

			$result = $this->pdo->query($sql);
			$row['items'] = $result->fetchAll(PDO::FETCH_ASSOC);

			$row['base']['totalQTY'] = 0;
			foreach ($row['items'] as $key => $value) {
				$row['base']['totalQTY'] += $value['qty'];
			}

			$sql = "SELECT a.id AS id_acc,a.id_account,a.id_account_other,ac.name,a.dollar,a.dinar,a.dollar_rate,a.in_dollar,a.depp_balance,a.date FROM acc_outdepp ai INNER JOIN acc a ON ai.id_acc = a.id INNER JOIN account ac ON ac.id = a.id_account_other WHERE ai.id_outdepp = '$id' ";
			$result = $this->pdo->query($sql);
			$row['pays'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$total = 0;
			foreach ($row['pays'] as &$value2) {
				$total += $value2['in_dollar'];
				@$value2['total'] += $total;
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

	function outdeppPayins($idoutdepp){
		try{
			$idoutdepp = $this->toSave($idoutdepp);
			$sql = "SELECT a.id AS id_acc,a.id_account,a.id_account_other,ac.name,a.dollar,a.dinar,a.dollar_rate,a.in_dollar,a.depp_balance,a.date FROM acc_outdepp ai INNER JOIN acc a ON ai.id_acc = a.id INNER JOIN account ac ON ac.id = a.id_account_other WHERE ai.id_outdepp = '$idoutdepp' ";
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
			@$acount->accoutdepp($v['id_account_other'],$v['id_depp'],$v['id_account'],$v['payDollar'],$v['payDinar'],'payin',$v['detail'],null,$v['id_outdepp']);

			return json_encode(['result'=>true]);
		}
		catch(PDOEXCEPTION $e){
			// dsh($e);
			return json_encode(['result'=>false,'message'=>['Delete_Not_Available']]);
		}
	}

	
	function outdeppEdit($id){
		try{
			$id = $this->toSave($id);
			$sql = "SELECT a.name as account,i.date,i.detail,i.discount,i.dollar_rate,i.id,i.invoice,i.status,i.total,i.type as outdeppType,u.name as user,d.name as depp,d.code as deppCode,b.name as branch,d.phone,d.address,d.id as id_depp,a.id as id_account,a.mobile as accountMobile,a.phone as accountPhone, a.address as accountAddress,a.serial as accountSerial,a.type as accountType FROM outdepp i INNER JOIN account a ON i.id_account = a.id inner join user u on u.id = i.id_user inner join depp d on d.id = i.id_depp inner join branch b on b.id = d.id_branch  WHERE i.id = '$id'";
			$result = $this->pdo->query($sql);
			$row['base'] = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT p.name as product,m.name as model,i.qty,i.cost,i.detail as description,p.code as code,b.name as brand,b.id as id_brand,m.id as id_model,p.id as id_product FROM outdepp_extra i inner join product p on p.id = i.id_product left join model m on m.id = p.id_model inner join brand b on b.id = p.id_brand WHERE i.id_outdepp = '$id'";
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
			$sql = "SELECT invoice FROM outdepp WHERE id_depp = '$idDepp' ORDER BY id DESC LIMIT 1";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC)['invoice'];

			$sql = "SELECT code FROM depp WHERE id = '$idDepp'";
			$result = $this->pdo->query($sql);
			$codeDepp = $result->fetch(PDO::FETCH_ASSOC)['code'];

			if(!empty($row)){
				$expl = explode('-', $row);
				$series = intval($expl[1]) + 1;
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

	function toggleStatus($id){
		try{
			$id = $this->toSave($id);

			$outdeppInfo = $this->getRow('outdepp',$id);
			if($outdeppInfo['status'] == 'progress'){
				$sql = "UPDATE outdepp SET status = 'done' WHERE id = '$id'";
				$status = 'done';
			}
			else{
				$sql = "UPDATE outdepp SET status = 'progress' WHERE id = '$id'";
				$status = 'progress';
			}

			$this->pdo->query($sql);

			return json_encode(['result'=>true, 'status'=>$status]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}
}
$outdepp = new outdepp();

// $sampeArr =  array ('accountType' => 'company','type' => 'active','pay' =>array ('id_account' =>array ('id' => '10','name' => 'سندوقی های',),'payDollar' => '1',  ),  'id_account' => '1',  'discount' => 0,  'items' =>   array (0 =>array ('qty' => 100,'description' => '','cost' => '1.00','id_brand' =>array ('id' => '3','name' => 'Piece / HTC',),'id_model' =>array ('id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,),'id_product' =>array ('id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,),'modelList' =>array (0 =>array ('id' => '2','id_brand' => '3','name' => 'One M7','detail' => NULL,),),'productList' =>array (0 =>array ('id' => '6','id_brand' => '3','id_model' => '2','name' => 'Volum key','code' => '985','price' => '1.00','detail' => NULL,),),'code' => '985',    ),  ),  'id_depp' => '1',  'invoice' => '30',  'dollarRate' => 1227.5,);
// dsh($outdepp->add($sampeArr));

// echo $outdepp->invoiceGenerate();

// dsh($outdepp->delete(['id'=>21]));

// $outdepp->outdeppPrint(24);
?>
