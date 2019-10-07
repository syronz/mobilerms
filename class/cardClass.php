<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/validClass.php');

class card extends database{
	private $table = 'card';
	private $struct = ['self'=>['id','card_number','change_pass','id_account','password','dollar','dinnar','date','id_user']];
	function add($v){
		try{
			//Validate Card
			$v = $this->toSave($v);
			$cardNumber = $this->toSave(str_replace('-', '', trim($v['card_number'])));
			$cardNumber = str_replace(' ', '', $cardNumber);

			if(!$this->validateCard($cardNumber)){
				return json_encode(['result'=>false,'message'=>[['field'=>'card_number','content'=>'Invalid_Card']]]);
			}

			$v['card_number'] = $cardNumber;

			$vCheck1 = $this->valid($v['card_number'],['inRange'],null,'card_number',100000000000,999999999999);
			$vCheck2 = $this->valid($v['idAccount'],['inRange'],null,'idAccount',1,99999999999999);
			$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
			if(!$vCheckArr['result'])
				return json_encode($vCheckArr);


		//Check exist account
			$account = $this->getRow('account',$v['idAccount']);
			if(empty($account))
				return json_encode(['result'=>false,'message'=>[['field'=>'idAccount','content'=>'This_account_not_exist']]]);


		//Hash Card
			$v['card_number'] = hash('sha512', SETTING::HASH_KEY.$v['card_number']);

		//encrypt idAccount
			$v['idAccount'] = $this->encryptNumber($v['idAccount']);

		//Hash Password
			$v['password'] = password_hash(setting::PASS_KEY.$v['password'], PASSWORD_BCRYPT);

		//Check for empty card
			$sql = "SELECT * FROM card WHERE card_number = '{$v['card_number']}'";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$old = $row;

			if(!empty($row['id_account'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'idAccount','content'=>'This_card_used_before']]]);
			}


		//Update Info in card row
			$idUser = $this->idUser();
			$date = date('Y-m-d H:i:s',time());
			$sql = "UPDATE card SET id_user = '$idUser', date='$date', id_account = '{$v['idAccount']}', change_pass = '1',password = '{$v['password']}' WHERE card_number = '{$v['card_number']}'";
			$this->pdo->query($sql);

			$this->record($this->table,'edit','first',$old['id'],['new'=>$v,'old'=>$old]);
			return json_encode(['result'=>true]);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function validateCard($card){
		if(strlen($card) != 12)
			return false;
		if( ($card[11] != (intval($card[1]) + intval($card[6])) % 10) || ($card[2] != abs(intval($card[4]) - intval($card[11]))) )
			return false;
		return true;
	}

	function cardList($idAcount){
		try{
			$idAcount = $this->toSave($idAcount);
			$idAcount = $this->encryptNumber($idAcount);
			$sql = "SELECT dollar,dinar,SUBSTR(date,1,10) AS date FROM card WHERE id_account = $idAcount";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($rows))
				return json_encode(['result'=>true,'rows'=>$rows]);
			return json_encode(['result'=>false]);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}


	function addCredit($v){
		try{
			//Validate Card
			$v = $this->toSave($v);
			$credit = @$this->toSave(str_replace('-', '', trim($v['credit'])));
			$credit = str_replace(' ', '', $credit);

			if(strlen($credit) != 12){
				return json_encode(['result'=>false,'message'=>[['field'=>'credit','content'=>'Invalid_Credit']]]);
			}

			$v['credit'] = $credit;

			$vCheck1 = $this->valid($v['credit'],['inRange'],null,'credit',100000000000,999999999999);
			$vCheck2 = $this->valid($v['idAccount'],['inRange'],null,'idAccount',1,99999999999999);
			$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
			if(!$vCheckArr['result'])
				return json_encode($vCheckArr);

		//Check exist account
			$account = $this->getRow('account',$v['idAccount']);
			if(empty($account))
				return json_encode(['result'=>false,'message'=>[['field'=>'idAccount','content'=>'This_account_not_exist']]]);

		//Hash Card
			$arrCredit[5000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('5000'));
			$arrCredit[25000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('25000'));
			$arrCredit[100000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('100000'));
			$arrCredit[500000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('500000'));

			foreach ($arrCredit as $key => $value) {
				$sql = "SELECT * FROM credit WHERE code = '$value'";
				$result = $this->pdo->query($sql);
				$rowCredit = $result->fetch(PDO::FETCH_ASSOC);
				if($rowCredit)
					break;
			}

			if(empty($rowCredit['id'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'credit','content'=>'Invalid_credit']]]);
			}
			if($rowCredit['state'] != 'open' || $rowCredit['id_card']){
				return json_encode(['result'=>false,'message'=>[['field'=>'credit','content'=>'This_credit_used_before']]]);
			}

		//encrypt idAccount
			$v['idAccount'] = $this->encryptNumber($v['idAccount']);

			$idUser = $this->idUser();
			$date = date('Y-m-d H:i:s',time());
			$ip = $this->getIp();

		//get card info
			$sql = "SELECT id,id_user,dinar FROM card WHERE id_account = '{$v['idAccount']}'";
			$result = $this->pdo->query($sql);
			$rowCard = $result->fetch(PDO::FETCH_ASSOC);

			if(empty($rowCard['id'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'idAccount','content'=>'This_account_have_not_any_card']]]);
			}
			else{
				//update credit
				$sql = "UPDATE credit SET id_card = '{$rowCard['id']}', state = 'close',date = '$date', ip = '$ip' WHERE id = '{$rowCredit['id']}'";
				$this->pdo->query($sql);

				//update card value
				if($key > 2000){
					$sql = "UPDATE card SET dinar = dinar + $key WHERE id = '{$rowCard['id']}'";
					$this->pdo->query($sql);
				}
				else{
					$sql = "UPDATE card SET dollar = dollar + $key WHERE id = '{$rowCard['id']}'";
					$this->pdo->query($sql);
				}

				//select last trans box
				$sql = "SELECT dollar_box,dinar_box FROM tran WHERE id_card = '{$rowCard['id']}' ORDER BY id DESC LIMIT 1;";
				$result = $this->pdo->query($sql);
				$rowTran = $result->fetch(PDO::FETCH_ASSOC);
				@$rowTran['dollar_box'] += $key;
				@$rowTran['dinar_box'] += $key;

				//insert tran
				if($key > 2000){
					$sql = "INSERT INTO tran(id_card,id_credit,id_user,type,date,ip,dinar,dinar_box) VALUES('{$rowCard['id']}','{$rowCredit['id']}','$idUser','in','$date','$ip','$key','{$rowTran['dinar_box']}');";
					$this->pdo->query($sql);
				}
				else{
					$sql = "INSERT INTO tran(id_card,id_credit,id_user,type,date,ip,dollar,dollar_box) VALUES('{$rowCard['id']}','{$rowCredit['id']}','$idUser','in','$date','$ip','$key','{$rowTran['dollar_box']}');";
					$this->pdo->query($sql);
				}
				$lastId = $this->pdo->lastInsertId();
			}

			$this->record($this->table,'edit','addCreditByUser',$rowCard['id'],['new'=>['dinar'=>$key],'old'=>$rowCard]);
			$this->record('credit','edit','addCreditByUser',$rowCredit['id'],['new'=>['state'=>'close','date'=>$date,'ip'=>$ip],'old'=>$rowCredit]);
			$this->record('tran','add','addCreditByUser',$lastId,['new'=>['id_card'=>$rowCard['id'],'id_credit'=>$rowCredit['id'],'id_user'=>$idUser,'type'=>'in','date'=>$date,'ip'=>$ip,'dinar'=>$key]]);
			return json_encode(['result'=>true,'message'=>$key.' IQD']);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function addCreditCrm($v){
		try{
			//Validate Card
			$v = $this->toSave($v);
			$credit = @$this->toSave(str_replace('-', '', trim($v['credit'])));
			$credit = str_replace(' ', '', $credit);

			if(strlen($credit) != 12){
				return json_encode(['result'=>false,'message'=>[['field'=>'credit','content'=>'Invalid_Credit']]]);
			}

			$v['credit'] = $credit;
			$v['idAccount'] = $this->decryptNumber($_SESSION['idCustomer']);

			$vCheck1 = $this->valid($v['credit'],['inRange'],null,'credit',100000000000,999999999999);
			$vCheck2 = $this->valid($v['idAccount'],['inRange'],null,'idAccount',1,99999999999999);
			$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
			if(!$vCheckArr['result'])
				return json_encode($vCheckArr);

		//Check exist account
			$account = $this->getRow('account',$v['idAccount']);
			if(empty($account))
				return json_encode(['result'=>false,'message'=>[['field'=>'idAccount','content'=>'This_account_not_exist']]]);

		//Hash Card
			$arrCredit[5000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('5000'));
			$arrCredit[25000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('25000'));
			$arrCredit[100000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('100000'));
			$arrCredit[500000] = hash('sha512', SETTING::HASH_KEY.$v['credit'].md5('500000'));

			foreach ($arrCredit as $key => $value) {
				$sql = "SELECT * FROM credit WHERE code = '$value'";
				$result = $this->pdo->query($sql);
				$rowCredit = $result->fetch(PDO::FETCH_ASSOC);
				if($rowCredit)
					break;
			}

			if(empty($rowCredit['id'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'credit','content'=>'Invalid_credit']]]);
			}
			if($rowCredit['state'] != 'open' || $rowCredit['id_card']){
				return json_encode(['result'=>false,'message'=>[['field'=>'credit','content'=>'This_credit_used_before']]]);
			}

		//encrypt idAccount
			$v['idAccount'] = $this->encryptNumber($v['idAccount']);

			$idUser = 0;
			$date = date('Y-m-d H:i:s',time());
			$ip = $this->getIp();

		//get card info
			$sql = "SELECT id,id_user,dinar FROM card WHERE id_account = '{$v['idAccount']}'";
			$result = $this->pdo->query($sql);
			$rowCard = $result->fetch(PDO::FETCH_ASSOC);

			if(empty($rowCard['id'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'idAccount','content'=>'This_account_have_not_any_card']]]);
			}
			else{
				//update credit
				$sql = "UPDATE credit SET id_card = '{$rowCard['id']}', state = 'close',date = '$date', ip = '$ip' WHERE id = '{$rowCredit['id']}'";
				$this->pdo->query($sql);

				//update card value
				if($key > 2000){
					$sql = "UPDATE card SET dinar = dinar + $key WHERE id = '{$rowCard['id']}'";
					$this->pdo->query($sql);
				}
				else{
					$sql = "UPDATE card SET dollar = dollar + $key WHERE id = '{$rowCard['id']}'";
					$this->pdo->query($sql);
				}

				//select last trans box
				$sql = "SELECT dollar_box,dinar_box FROM tran WHERE id_card = '{$rowCard['id']}' ORDER BY id DESC LIMIT 1;";
				$result = $this->pdo->query($sql);
				$rowTran = $result->fetch(PDO::FETCH_ASSOC);
				@$rowTran['dollar_box'] += $key;
				@$rowTran['dinar_box'] += $key;



				//insert tran
				if($key > 2000){
					$sql = "INSERT INTO tran(id_card,id_credit,type,date,ip,dinar,dinar_box) VALUES('{$rowCard['id']}','{$rowCredit['id']}','in','$date','$ip','$key','{$rowTran['dinar_box']}');";
					$this->pdo->query($sql);
				}
				else{
					$sql = "INSERT INTO tran(id_card,id_credit,type,date,ip,dollar,dollar_box) VALUES('{$rowCard['id']}','{$rowCredit['id']}','in','$date','$ip','$key','{$rowTran['dollar_box']}');";
					$this->pdo->query($sql);
				}
				$lastId = $this->pdo->lastInsertId();
			}

			$this->recordCustomer($this->table,'edit','addCreditOnline',$rowCard['id'],['new'=>['dinar'=>$key],'old'=>$rowCard]);
			$this->recordCustomer('credit','edit','addCreditOnline',$rowCredit['id'],['new'=>['state'=>'close','date'=>$date,'ip'=>$ip],'old'=>$rowCredit]);
			$this->recordCustomer('tran','add','addCreditOnline',$lastId,['new'=>['id_card'=>$rowCard['id'],'id_credit'=>$rowCredit['id'],'id_user'=>$idUser,'type'=>'in','date'=>$date,'ip'=>$ip,'dinar'=>$key]]);
			return json_encode(['result'=>true,'message'=>$key.' IQD']);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function checkCardNumber($v){
		try{
			$cardNumberTmp = $v['cardNumber'];
			$cardNumber = @$this->toSave($v['cardNumber']);
			$cardNumber = $this->toSave(str_replace(' ', '',(str_replace('-', '', trim($cardNumber)))));

			if(!$this->validateCard($cardNumber)){
				return json_encode(['result'=>false,'message'=>[['field'=>'card_number','content'=>'Invalid_Card']]]);
			}

			$v['card_number'] = $cardNumber;

			$vCheck1 = $this->valid($v['card_number'],['inRange'],null,'card_number',100000000000,999999999999);
			if(!$vCheck1['result'])
				return json_encode($vCheck1);

			$v['card_number'] = hash('sha512', SETTING::HASH_KEY.$v['card_number']);

			$sql = "SELECT id_account FROM card WHERE card_number = '{$v['card_number']}'";
			$result = $this->pdo->query($sql);
			$rowCard = $result->fetch(PDO::FETCH_ASSOC);
			// dsh($rowCard);
			if(empty($rowCard['id_account'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Invalid_Card']]]);
			}
			return json_encode(['result'=>true,'data'=>$cardNumberTmp]);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function checkCardPass($v){
		try{
			$cardNumberTmp = $v['cardNumber'];
			$cardNumber = @$this->toSave($v['cardNumber']);
			$cardNumber = $this->toSave(str_replace(' ', '',(str_replace('-', '', trim($cardNumber)))));

			if(!$this->validateCard($cardNumber)){
				return json_encode(['result'=>false,'message'=>[['field'=>'card_number','content'=>'Invalid_Card']]]);
			}

			$v['card_number'] = $cardNumber;

			$vCheck1 = $this->valid($v['card_number'],['inRange'],null,'card_number',100000000000,999999999999);
			if(!$vCheck1['result'])
				return json_encode($vCheck1);

			$v['card_number'] = hash('sha512', SETTING::HASH_KEY.$v['card_number']);

			$sql = "SELECT id,id_account,password FROM card WHERE card_number = '{$v['card_number']}'";
			$result = $this->pdo->query($sql);
			$rowCard = $result->fetch(PDO::FETCH_ASSOC);

			if(empty($rowCard['id_account'])){
				return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Invalid_Card']]]);
			}

			if(password_verify(setting::PASS_KEY.$v['password'], $rowCard['password'])){
				$_SESSION['idCustomer'] = $rowCard['id_account'];
				return json_encode(['result'=>true,'data'=>setting::ERP_PATH]);
			}
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'Wrong_Password']]]);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function checkCardLogin(){
		if(@$_SESSION['idCustomer'])
			return 'true';
		return 'false';
	}


	function getCustomerInfo(){
		try{
			$idCustomer = $this->decryptNumber($_SESSION['idCustomer']);

			$customerInfo['account'] = $this->getRow('account',$idCustomer);
			$customerInfo['card'] = $this->getRowsByFiled('card','id_account',$_SESSION['idCustomer'])[0];

			return json_encode($customerInfo);

		}
		catch(PDOException $e){
			dsh($e);
		}
	}


	public function customerLogout(){
		$_SESSION = [];
	}


	

	function tranListCrm($data){
	try{
		// dsh($data);
		$data = $this->toSave($data);
		$cardInfo = $this->getRowsByFiled('card','id_account',$_SESSION['idCustomer'])[0];

		$sql = "SELECT count(*) as count FROM tran WHERE id_card = '{$cardInfo['id']}'";
		$result = $this->pdo->query($sql);
		$r['count'] = $result->fetch(PDO::FETCH_ASSOC)['count'];

		$sql = "SELECT id,type,date,dollar,dinar,dollar_box,dinar_box FROM tran WHERE id_card = '{$cardInfo['id']}' ORDER BY {$data['order']} LIMIT {$data['limit']}";
		$result = $this->pdo->query($sql);
		$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);



		if(SETTING::USER_ACTIVITY_READ)
			$this->recordCustomer('tran','view',$data['search'],0,null);
		return json_encode($r);
	}
	catch(PDOException $e){
		dsh($e);
	}
}




}
$card = new card();
// for($i = 0; $i<50000; $i++)



?>