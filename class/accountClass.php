<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/validClass.php');
require_once realpath(__DIR__ . '/dollar_rateClass.php');

class account extends database{
	private $table = 'account';
	private $struct = ['self'=>['id','name','type','serial','mobile','phone','email','address','detail','date_register']];

	function add($v){
		if(!$this->perm($this->table,'add'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Add','extra'=>'']]]);
		
		$vCheck1 = @$this->valid($v['name'],['maxLength200','isName'],$this->table,'name');
		$vCheck2 = [];
		if(!empty($v['mobile']))
			$vCheck2 = @$this->valid($v['mobile'],['isUnique','isMobile'],$this->table,'mobile');

		$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
		if(!$vCheckArr['result'])
			return json_encode($vCheckArr);


		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		if(!$this->perm($this->table,'edit'))
			return json_encode(['result'=>false,'message'=>[['field'=>'','content'=>'You_Havent_Permission_To_Edit','extra'=>'']]]);
		
		$old = $this->getRow($this->table,$v['id']);
		$vCheck['result'] = true;

		if(!empty($v['mobile']))
			$vCheck2 = @$this->valid($v['mobile'],['isUnique','isMobile'],$this->table,'mobile');


		if($v['serial'] != $old['serial'])
			$vCheck = $this->valid($v['serial'],['isUnique','maxLength200','isName'],$this->table,'serial');
		if(!$vCheck['result'])
			return json_encode($vCheck);

		if($v['serial'] == '')
			$v['serial'] = null;

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

		$data = $this->listDefault($this->table,$v,$this->struct);
		$data = json_decode($data,true);
		foreach ($data['rows'] as $key => &$value) {
			$value['balance'] = floatval($this->lastBalance($value['id']));
		}
		// dsh($data);
		return json_encode($data);

	}

	function accountList($accountType = null){
		if(!$accountType)
			return $this->listAllDefault($this->table,['id','name']);
		else{
			$accountType = $this->toSave($accountType);
			$sql = "SELECT id,name FROM account WHERE type = '$accountType' ORDER BY name";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
	}

	
	function accountPayList($idDepp = null){
		try{
			$idDepp = $idDepp ? $idDepp : $_SESSION['idDepp'];

			$idDepp = $this->toSave($idDepp);
			$deppInfo = $this->getRow('depp',$idDepp);
			$sql = "SELECT id,name FROM account WHERE id = '{$deppInfo['id_cash']}'";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT id,name FROM account WHERE type in ('cash','drawing','store','noAccount') AND id != '{$deppInfo['id_cash']}'";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			array_unshift($rows, $row);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function accountTollList(){
		try{
			$sql = "SELECT id,name FROM account WHERE name = 'toll' and type = 'expense'";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT id,name FROM account WHERE type = 'expense' ";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			array_unshift($rows, $row);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


	function addAcc($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,$idOutdepp = 0, $idIndepp = 0){
		try{

			if(is_array($dollar)){
				$dollar1 = $dollar[0];
				$dollar2 = $dollar[1];
			}
			else{
				$dollar1 = $dollar;
				$dollar2 = -$dollar1;
			}

			if(is_array($dinar)){
				$dinar1 = $dinar[0];
				$dinar2 = $dinar[1];
			}
			else{
				$dinar1 = $dinar;
				$dinar2 = -$dinar1;
			}



			if(!$idDepp){
				$idDepp = $_SESSION['idDepp'];
			}
			if(is_null($inOut)){
				if(($dollar1 * $dinar1) == 0){
					if(($dollar1 + $dinar1) > 0)
						$inOut = 'in';
					else if(($dollar1 + $dinar1) < 0)
						$inOut = 'out';
					else
						$inOut = 'zero';
				}
				else if(($dollar1 * $dinar1) < 0){
					$inOut = 'exchange';
				}
				else{
					if($dollar1 > 0)
						$inOut = 'in';
					else
						$inOut = 'out';
				}
			}
			$dollarRate = $this->getDollarRate($idDepp);
			if(!$dollarRate)
				return false;
			$inDollar1 = $dollar1 + $dinar1 / $dollarRate;
			$idUser = $this->idUser();
			$date = date('Y-m-d H:i:s',time());
			$balanceDepp = $this->lastBalanceDepp($idAccount,$idDepp) + $inDollar1;
			$balance = $this->lastBalance($idAccount) + $inDollar1;

			$sql = "INSERT INTO acc(id_account,id_depp,id_user,id_account_other,in_out,date,dollar,dinar,type,dollar_rate,in_dollar,depp_balance,balance,detail,id_outdepp,id_indepp) VALUES('$idAccount','$idDepp','$idUser','$idAccountOther','$inOut','$date','$dollar1','$dinar1','$type','$dollarRate','$inDollar1','$balanceDepp','$balance','$detail',$idOutdepp,$idIndepp);";
			$this->pdo->query($sql);
			$lastAccId = $this->pdo->lastInsertId();

		//Add Alter
			if($inOut == 'in' || $inOut == 'out'){
				// if(!$dollar2)
				// 	$dollar2 = -$dollar1;
				// if(!$dinar2)
				// 	$dinar2 = -$dinar1;

				$inDollar2 = $dollar2 + $dinar2 / $dollarRate;
				if($inOut == 'in')
					$inOut = 'out';
				else
					$inOut = 'in';
				$balanceDepp = $this->lastBalanceDepp($idAccountOther,$idDepp) + $inDollar2;
				$balance = $this->lastBalance($idAccountOther) + $inDollar2;


				$sql = "INSERT INTO acc(id_account,id_depp,id_user,id_account_other,in_out,date,dollar,dinar,type,dollar_rate,in_dollar,depp_balance,balance,detail,pair,id_outdepp,id_indepp) VALUES('$idAccountOther','$idDepp','$idUser','$idAccount','$inOut','$date','$dollar2','$dinar2','$type','$dollarRate','$inDollar2','$balanceDepp','$balance','$detail','$lastAccId',$idOutdepp,$idIndepp);";
				$this->pdo->query($sql);
				$pairAccId = $this->pdo->lastInsertId();

				$sql = "UPDATE acc SET pair = '$pairAccId' WHERE id = '$lastAccId'";
				$this->pdo->query($sql);
			}

			return $lastAccId;

		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function newAcc($v){
		if($v['dollar'] > 0 || $v['dinar'] > 0){
			$p = 'payin';
		}
		else
			$p = 'payout';
		$r = @$this->addAcc($v['id_account'],0,$v['id_account_other']['id'],$this->bigIntval($v['dollar']),$this->bigIntval($v['dinar']),$p,$v['detail']);
		if($r > 0)
			return json_encode(['result'=>true]);
		return json_encode(['result'=>false]);
	}

	function saveTrn($v){
		try{
			$v = $this->toSave($v);
			$required = $this->isRequired($v,['to_id_account'=>'to_id_account','from_id_account'=>'from_id_account','toType'=>'toType']);
			if(!$required['result'])
				return json_encode($required);

			$dollarRate = new dollar_rate();
			$inDollar = $v['dinar'] / $dollarRate->get_dollar_rate() + $v['dollar'];

			$vCheck = $this->valid($inDollar,['inRange'],null,'amountSend',0.0001,99999999999999);
			if(!$vCheck['result'])
				return json_encode($vCheck);

			$lastBalance = $this->lastBalance($v['from_id_account']);

			if(in_array($v['fromType'], ['cash','store']) !== false){
				if($lastBalance < $inDollar)
					return json_encode(['result'=>false,'message'=>[['field'=>'amountSend','content'=>'This_Amount_Not_Exist_By_Cash']]]);
			}


			$accType = 'transfer';
			switch ($v['toType']) {
				case 'expense':
				$accType = 'expense';
				break;
				case 'equipment':
				$accType = 'expense';
				break;
				case 'drawing':
				$accType = 'drawing';
				break;
				case 'cash':
				$accType = 'payin';
				break;
				default:
				$accType = 'transfer';
				break;
			}

			@$this->addAcc($v['to_id_account'],$_SESSION['idDepp'],$v['from_id_account'],$v['dollar'],$v['dinar'],$accType,$v['detail']);
			return json_encode(['result'=>true]);


		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function lastBalanceDepp($idAccount,$idDepp = null){
		try{
			$idAccount = $this->toSave($idAccount);
			if($idDepp){
				$sql = "SELECT depp_balance FROM acc WHERE id_account = '$idAccount' AND id_depp = '$idDepp' ORDER BY id DESC LIMIT 1;";
				$result = $this->pdo->query($sql);
				if($result)
					return $result->fetch(PDO::FETCH_ASSOC)['depp_balance'];
				else
					return 0;
			}
			else{
				$sql = "SELECT depp_balance FROM acc WHERE id_account = '$idAccount' AND id_depp = '{$_SESSION['idDepp']}' ORDER BY id DESC LIMIT 1;";
				$result = $this->pdo->query($sql);
				if($result)
					return $result->fetch(PDO::FETCH_ASSOC)['depp_balance'];
				else
					return 0;
			}

		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function lastBalance($idAccount){
		try{
			$sql = "SELECT balance FROM acc WHERE id_account = '$idAccount' ORDER BY id DESC LIMIT 1";
			$result = $this->pdo->query($sql);
			if($result)
				return $result->fetch(PDO::FETCH_ASSOC)['balance'];
			else
				return 0;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


	function accIndepp($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,$idIndepp){
		try{
			$idIndepp = $this->toSave($idIndepp);
			$idAcc = $this->addAcc($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,0,$idIndepp);
			// $idAcc = $this->addAcc($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,$idOutdepp);
			$sql = "INSERT INTO acc_indepp(id_indepp,id_acc) VALUES($idIndepp,$idAcc)";
			$result = $this->pdo->query($sql);

			$sql = "SELECT total,discount FROM indepp WHERE id = $idIndepp";
			$result = $this->pdo->query($sql);
			$indeppInfo = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT sum(a.in_dollar) AS sumPay FROM acc_indepp ai INNER JOIN acc a ON ai.id_acc = a.id WHERE ai.id_indepp = $idIndepp ";
			$result = $this->pdo->query($sql);
			$sumPay = $result->fetch(PDO::FETCH_ASSOC)['sumPay'];

			if($sumPay >= ($indeppInfo['total'] - setting::DISCOUNT_EXCHANGE)){
				$sql = "UPDATE indepp SET status = 'done' WHERE id = $idIndepp";
				$this->pdo->query($sql);
			}

			return true;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function accOutdepp($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,$idOutdepp){
		try{
			$idOutdepp = $this->toSave($idOutdepp);
			$idAcc = $this->addAcc($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,$idOutdepp,0);
			$sql = "INSERT INTO acc_outdepp(id_outdepp,id_acc) VALUES($idOutdepp,$idAcc)";
			$result = $this->pdo->query($sql);

			$sql = "SELECT total,discount FROM outdepp WHERE id = $idOutdepp";
			$result = $this->pdo->query($sql);
			$outdeppInfo = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT sum(a.in_dollar) AS sumPay FROM acc_outdepp ai INNER JOIN acc a ON ai.id_acc = a.id WHERE ai.id_outdepp = $idOutdepp ";
			$result = $this->pdo->query($sql);
			$sumPay = $result->fetch(PDO::FETCH_ASSOC)['sumPay'];

			if($sumPay >= ($outdeppInfo['total'] - setting::DISCOUNT_EXCHANGE)){
				$sql = "UPDATE outdepp SET status = 'done' WHERE id = $idOutdepp";
				$this->pdo->query($sql);
			}

			return true;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function accOrder($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null,$idOrder){
		try{
			$idOrder = $this->toSave($idOrder);
			$idAcc = $this->addAcc($idAccount,$idDepp,$idAccountOther,$dollar,$dinar,$type,$detail,$inOut = null);
			$sql = "INSERT INTO acc_order(id_order,id_acc) VALUES($idOrder,$idAcc)";
			$result = $this->pdo->query($sql);

			$sql = "SELECT total,discount FROM ordered WHERE id = $idOrder";
			$result = $this->pdo->query($sql);
			$orderInfo = $result->fetch(PDO::FETCH_ASSOC);

			$sql = "SELECT sum(a.in_dollar) AS sumPay FROM acc_order ai INNER JOIN acc a ON ai.id_acc = a.id WHERE ai.id_order = $idOrder ";
			$result = $this->pdo->query($sql);
			$sumPay = $result->fetch(PDO::FETCH_ASSOC)['sumPay'];

			if($sumPay >= ($orderInfo['total'] - setting::DISCOUNT_EXCHANGE)){
				$sql = "UPDATE ordered SET status = 'done' WHERE id = $idOrder";
				$this->pdo->query($sql);
			}

			return true;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}



	function accountDeppList($v){
		try{
			$idDepp = $_SESSION['idDepp'];
			$v = $this->toSave($v);
			if(empty($v['search']))
				$sql = "SELECT a.id,a.name,a.`type`,a.serial,CONCAT(a.mobile,' ',a.phone) AS mobile,a.address,a.detail,substr(acc.date,1,10) as date,acc.depp_balance FROM (SELECT max(id) AS id FROM acc WHERE acc.id_depp = '$idDepp' GROUP BY acc.id_account ) AS tbl INNER JOIN acc ON acc.id = tbl.id INNER JOIN account a ON a.id = acc.id_account ORDER BY {$v['order']} LIMIT {$v['limit']}";
			else
				$sql = "SELECT a.id,a.name,a.`type`,a.serial,CONCAT(a.mobile,' ',a.phone) AS mobile,a.address,a.detail,substr(acc.date,1,10) as date,acc.depp_balance FROM (SELECT max(id) AS id FROM acc WHERE acc.id_depp = '$idDepp' GROUP BY acc.id_account ) AS tbl INNER JOIN acc ON acc.id = tbl.id INNER JOIN account a ON a.id = acc.id_account WHERE a.id like '{$v['search']}' OR a.name like '%{$v['search']}%' OR a.`type` like '%{$v['search']}%' OR a.serial like '%{$v['search']}%' OR a.mobile like '%{$v['search']}%' OR a.serial like '%{$v['search']}%' OR a.address like '%{$v['search']}%' OR a.detail like '%{$v['search']}%' OR acc.date like '%{$v['search']}%'  ORDER BY {$v['order']} LIMIT {$v['limit']}";

			$result = $this->pdo->query($sql);
			$r['rows'] = $result->fetchAll(PDO::FETCH_ASSOC);
			$r['rows'] = $this->entityDecode($r['rows']);

			if(empty($v['search']))
				$sql = "SELECT count(*) as count FROM (SELECT max(id) AS id FROM acc WHERE acc.id_depp = '$idDepp' GROUP BY acc.id_account ) AS tbl INNER JOIN acc ON acc.id = tbl.id INNER JOIN account a ON a.id = acc.id_account";
			else
				$sql = "SELECT count(*) as count FROM (SELECT max(id) AS id FROM acc WHERE acc.id_depp = '$idDepp' GROUP BY acc.id_account ) AS tbl INNER JOIN acc ON acc.id = tbl.id INNER JOIN account a ON a.id = acc.id_account WHERE a.id like '{$v['search']}' OR a.name like '%{$v['search']}%' OR a.`type` like '%{$v['search']}%' OR a.serial like '%{$v['search']}%' OR a.mobile like '%{$v['search']}%' OR a.serial like '%{$v['search']}%' OR a.address like '%{$v['search']}%' OR a.detail like '%{$v['search']}%' OR acc.date like '%{$v['search']}%'";
			$result = $this->pdo->query($sql);
			$r['count'] = $result->fetch(PDO::FETCH_ASSOC)['count'];

			$action = $v['search']?'search':'view';
			if(SETTING::USER_ACTIVITY_READ)
				$this->record($this->table,$action,$v['search'],0,null);
			return json_encode($r);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}



	function accountDeppViewList($v){
		try{
			$v = $this->toSave($v);

			$struct = ['self'=>['id','id_user','id_account_other','in_out','date','dollar','dinar','type','dollar_rate','in_dollar','depp_balance','balance','detail','id_outdepp','id_indepp'],
			'account'=>['source'=>'id_account_other','target'=>'id','column'=>'name','search'=>['name','mobile']],
			'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']]
			];
			$v['condition'] = " acc.id_account = {$v['id']} AND acc.id_depp = {$_SESSION['idDepp']} ";
			if(@$v['start'] && @$v['end']){
				$end = date('Y-m-d',strtotime($v['end'])+86400);
				$start = date('Y-m-d',strtotime($v['start']));
				$v['condition'] .= " AND acc.date >= '$start' AND acc.date <= '$end' ";
			}
			$data = json_decode($this->listDefault('acc',$v,$struct),true);

			for($i = 0; $i < count($data['rows']); $i++){
				if($data['rows'][$i]['id_outdepp']){
					$sql = "SELECT  if (length(oe.detail),concat(p.name,'\n :',oe.detail),p.name) as name,oe.qty,TRIM(TRAILING '.00' FROM oe.cost) as cost,TRIM(TRAILING '.00' FROM oe.qty*oe.cost) as total from outdepp_extra oe left join product p on p.id = oe.id_product
						where oe.id_outdepp = {$data['rows'][$i]['id_outdepp']}";
					$result = $this->pdo->query($sql);
					$data['rows'][$i]['mini']  = $result->fetchAll(PDO::FETCH_ASSOC);
				}
				//$data['rows'][$i]['detail'] = implode(",",$data['rows'][$i])
				if($data['rows'][$i]['in_dollar'] == 0 ){
					array_splice($data['rows'],$i,1);
				}
				if($data['rows'][$i]['id_indepp']){
					$sql = "SELECT  if (length(oe.detail),concat(p.name,'\n :',oe.detail),p.name) as name,oe.qty,TRIM(TRAILING '.00' FROM oe.cost) as cost,TRIM(TRAILING '.00' FROM oe.qty*oe.cost) as total from indepp_extra oe left join product p on p.id = oe.id_product
						where oe.id_indepp = {$data['rows'][$i]['id_indepp']}";
					$result = $this->pdo->query($sql);
					$data['rows'][$i]['mini']  = $result->fetchAll(PDO::FETCH_ASSOC);
				}
			}


			return json_encode($data);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function accountViewList($v){
		try{
			$struct = ['self'=>['id','id_user','id_account_other','in_out','date','dollar','dinar','type','dollar_rate','in_dollar','depp_balance','balance','detail'],
			'account'=>['source'=>'id_account_other','target'=>'id','column'=>'name','search'=>['name','mobile']],
			'depp'=>['source'=>'id_depp','target'=>'id','column'=>'name','search'=>['name']],
			'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']]
			];
			$v['condition'] = " acc.id_account = {$v['id']}";
			return $this->listDefault('acc',$v,$struct);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function deleteAcc($v){
		try{
			$id = is_array($v)? $v['id']:$v;
			$accInfo = $this->getRow('acc',$id);
			$today = date('Y-m-d',time());

			if(setting::LOCK_ACC_AFTER_24H){
				if($today != substr($accInfo['date'], 0,10))
					return json_encode(['result'=>false,'message'=>['You_Cant_DELETE_Different_Date']]);
			}



			$pair = $accInfo['pair']?$this->getRow('acc',$accInfo['pair']):$accInfo;

			if(!empty($accInfo['id']) && !empty($pair['id'])){
				$sql = "DELETE FROM acc_outdepp WHERE id_acc in ({$accInfo['id']},{$pair['id']})";
				$this->pdo->query($sql);
				$sql = "DELETE FROM acc_indepp WHERE id_acc in ({$accInfo['id']},{$pair['id']})";
				$this->pdo->query($sql);

				$sql = "DELETE FROM acc WHERE id in ({$accInfo['id']},{$pair['id']})";
				$this->pdo->query($sql);

				$sql = "UPDATE acc SET balance = balance - {$accInfo['in_dollar']} WHERE id > {$accInfo['id']} AND id_account = {$accInfo['id_account']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = depp_balance - {$accInfo['in_dollar']} WHERE id > {$accInfo['id']} AND id_account = {$accInfo['id_account']} and id_depp = {$accInfo['id_depp']}";
				$this->pdo->query($sql);

				$sql = "UPDATE acc SET balance = balance - {$pair['in_dollar']} WHERE id > {$pair['id']} AND id_account = {$pair['id_account']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = depp_balance - {$pair['in_dollar']} WHERE id > {$pair['id']} AND id_account = {$pair['id_account']} and id_depp = {$pair['id_depp']}";
				$this->pdo->query($sql);

				$this->record('acc','delete','',$accInfo['id'],['old'=>$accInfo]);
				$this->record('acc','delete','',$pair['id'],['old'=>$pair]);
				return json_encode(['result'=>true]);
			}
			

			

			

			
			return json_encode(['result'=>false]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function editAcc($v){
		try{
			$id = $v['id'];
			$accInfo = $this->getRow('acc',$id);
			$today = date('Y-m-d',time());

			if(setting::LOCK_ACC_AFTER_24H){
				if($today != substr($accInfo['date'], 0,10))
					return json_encode(['result'=>false,'message'=>['You_Cant_Edit_Different_Date']]);
			}


			$pair = $accInfo['pair']?$this->getRow('acc',$accInfo['pair']):$accInfo;

			$inDollar = floatval($v['dollar']) + floatval($v['dinar'] / $accInfo['dollar_rate']);

			$newBalanceDepp = $this->lastBalanceDepp($v['id_account_other'],$pair['id_depp']) - $inDollar;
			$newBalance = $this->lastBalance($v['id_account_other']) - $inDollar;

			$sql = "UPDATE acc SET id_account_other = '{$v['id_account_other']}', dollar = '{$v['dollar']}', dinar = '{$v['dinar']}', type = '{$v['type']}', detail = '{$v['detail']}', in_dollar = '$inDollar' WHERE id = '{$accInfo['id']}'";
			$this->pdo->query($sql);

			$sql = "UPDATE acc SET id_account = '{$v['id_account_other']}', dollar = -'{$v['dollar']}', dinar = -'{$v['dinar']}', type = '{$v['type']}', detail = '{$v['detail']}', in_dollar = -'$inDollar' WHERE id = '{$pair['id']}'";
			$this->pdo->query($sql);


			if($v['id_account_other'] == $accInfo['id_account_other']){
				$diff = $accInfo['in_dollar'] - $inDollar;

				$sql = "UPDATE acc SET balance = balance - $diff WHERE id >= {$accInfo['id']} AND id_account = {$accInfo['id_account']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = depp_balance - $diff WHERE id >= {$accInfo['id']} AND id_account = {$accInfo['id_account']} and id_depp = {$accInfo['id_depp']}";
				$this->pdo->query($sql);

				$sql = "UPDATE acc SET balance = balance + $diff WHERE id >= {$pair['id']} AND id_account = {$pair['id_account']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = depp_balance + $diff WHERE id >= {$pair['id']} AND id_account = {$pair['id_account']} and id_depp = {$pair['id_depp']}";
				$this->pdo->query($sql);
			}
			else{
				$diff = $accInfo['in_dollar'] - $inDollar;

				$sql = "UPDATE acc SET balance = balance - $diff WHERE id >= {$accInfo['id']} AND id_account = {$accInfo['id_account']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = depp_balance - $diff WHERE id >= {$accInfo['id']} AND id_account = {$accInfo['id_account']} and id_depp = {$accInfo['id_depp']}";
				$this->pdo->query($sql);



				$sql = "UPDATE acc SET balance = balance - {$pair['in_dollar']} WHERE id > {$pair['id']} AND id_account = {$pair['id_account']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = depp_balance - {$pair['in_dollar']} WHERE id > {$pair['id']} AND id_account = {$pair['id_account']} and id_depp = {$pair['id_depp']}";
				$this->pdo->query($sql);

				$sql = "UPDATE acc SET balance = '$newBalance' WHERE id >= {$pair['id']} AND id_account = {$v['id_account_other']}";
				$this->pdo->query($sql);
				$sql = "UPDATE acc SET depp_balance = '$newBalanceDepp' WHERE id >= {$pair['id']} AND id_account = {$v['id_account_other']} and id_depp = {$pair['id_depp']}";
				$this->pdo->query($sql);
			}

			$this->record('acc','edit','',$accInfo['id'],['new'=>$v,'old'=>$accInfo]);
			$this->record('acc','edit','',$pair['id'],['new'=>$v,'old'=>$pair]);
			return json_encode(['result'=>true]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function getAccountByMobile($mobile){
		$vCheck = $this->valid($mobile,['isMobile'],$this->table,'mobile');
		if(!$vCheck['result'])
			return json_encode($vCheck);

		$accountInfo = $this->getRowsByFiled('account','mobile',$mobile);
		if(empty($accountInfo)){
			return json_encode(['result'=>true,'customerType'=>'new','info'=>['mobile'=>$mobile]]);
		}
		else{
			return json_encode(['result'=>true,'customerType'=>'exist','info'=>$accountInfo[0]]);
		}
	}

	function addCustomer($v){
		$vCheck1 = @$this->valid($v['name'],['maxLength200','isName'],$this->table,'name');
		$vCheck2 = [];
		if(!empty($v['mobile']))
			$vCheck2 = @$this->valid($v['mobile'],['isUnique','isMobile'],$this->table,'mobile');

		$vCheckArr = $this->arrValid($vCheck1,$vCheck2);
		if(!$vCheckArr['result'])
			return json_encode($vCheckArr);

		$v['type'] = 'customer';
		$idCustomer = $this->addDefault($this->table,$v);
		return json_encode(['result'=>true,'idCustomer'=>$idCustomer]);
	}

	function accountInfo($v){
		try{
			$v = $this->toSave($v);
			$sql = "SELECT * FROM account WHERE id = '{$v['id']}'";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			if($row)
				return json_encode(['result'=>true,'data'=>$row]);
			else
				return json_encode(['result'=>false]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function cashDeppViewList($v){
		try{
			$deppInfo = $this->getRow('depp',$_SESSION['idDepp']);

			$struct = ['self'=>['id','id_user','id_account_other','in_out','date','dollar','dinar','type','dollar_rate','in_dollar','depp_balance','balance','detail'],
			'account'=>['source'=>'id_account_other','target'=>'id','column'=>'name','search'=>['name','mobile']],
			'user'=>['source'=>'id_user','target'=>'id','column'=>'name','search'=>['name','detail']]
			];
			$v['condition'] = " acc.id_account = {$deppInfo['id_cash']}";
			return $this->listDefault('acc',$v,$struct);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function idCashInDepp(){
		$deppInfo = $this->getRow('depp',$_SESSION['idDepp']);
		return json_encode(['idCash'=>$deppInfo['id_cash']]);
	}


	function transferMoney($v){
		try{
			$v = $this->toSave($v);
			$required = $this->isRequired($v,['amountSend'=>'amountSend','id_account'=>'account','id_account_other'=>'account']);
			if(!$required['result']){
				return json_encode($required);
			}

			$vCheck = $this->valid($v['amountSend'],['inRange'],null,'amountSend',0.0001,99999999999999);
			if(!$vCheck['result']){
				return json_encode($vCheck);
			}

			$lastBalance = $this->lastBalance($v['id_account']);

			if($lastBalance < $v['amountSend']){
				return json_encode(['result'=>false,'message'=>[['field'=>'amountSend','content'=>'This_Amount_Not_Exist_By_Cash']]]);
			}

			@$this->addAcc($v['id_account_other'],$_SESSION['idDepp'],$v['id_account'],$v['amountSend'],0,'transfer',$v['detail']);

			return json_encode(['result'=>true]);


		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function lastBalanceBeforeDate($idAccount,$idDepp,$date){
		try{
			$sql = "SELECT depp_balance,balance FROM acc WHERE id_account = '$idAccount' AND id_depp = '$idDepp' AND date < '$date' ORDER BY date DESC LIMIT 1";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			return $row;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}



}
$account = new account();

// dsh($account->deleteAcc(14373));


?>
