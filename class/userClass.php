<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/storeClass.php');

class user extends database{
	private $table = 'user';
	private $struct = ['self'=>['id','name','username','password','change_password','lang','mobile','email','detail','id_perm','state'],
	'perm'=>['source'=>'id_perm','target'=>'id','column'=>'name','search'=>['name']]];

	function add($v){
		$required = $this->isRequired($v,['id_perm'=>'perm','name'=>'name','username'=>'username','password'=>'password']);
		if(!$required['result'])
			return json_encode($required);

		$vCheck1 = $this->valid($v['username'],['isUnique','maxLength200','isName'],$this->table,'username');
		$vCheck2 = $vCheck3 = ['result'=>true,'message'=>[]];
		if(@$v['email'])
			$vCheck2 = $this->valid($v['email'],['isEmail'],null,'email');
		if(@$v['mobile'])
			$vCheck3 = $this->valid($v['mobile'],['isMobile'],null,'mobile');
		$vCheck4 = $this->valid($v['id_perm'],['inRange'],null,'perm',1,10000000);
		$vCheckArr = $this->arrValid($vCheck1,$vCheck2,$vCheck3,$vCheck4);
		if(!$vCheckArr['result'])
			return json_encode($vCheckArr);

		$v['password'] = password_hash(setting::PASS_KEY.$v['password'], PASSWORD_BCRYPT);

		$this->addDefault($this->table,$v);
		return json_encode(['result'=>true]);
	}

	function edit($v){
		$required = $this->isRequired($v,['id_perm'=>'perm','name'=>'name','username'=>'username']);
		if(!$required['result'])
			return json_encode($required);

		$old = $this->getRow($this->table,$v['id']);

		if($v['name'] != $old['name'])
			$vCheck1 = $this->valid($v['name'],['isUnique','maxLength200','isName'],$this->table,'name');
		else
			$vCheck1 = $this->valid($v['name'],['maxLength200','isName'],$this->table,'name');
		$vCheck2 = $vCheck3 = ['result'=>true,'message'=>[]];
		if(@$v['email'])
			$vCheck2 = $this->valid($v['email'],['isEmail'],null,'email');
		if(@$v['mobile'])
			$vCheck3 = $this->valid($v['mobile'],['isMobile'],null,'mobile');
		$vCheck4 = $this->valid($v['id_perm'],['inRange'],null,'perm',1,10000000);

		$vCheckArr = $this->arrValid($vCheck1,$vCheck2,$vCheck3,$vCheck4);
		if(!$vCheckArr['result'])
			return json_encode($vCheckArr);

		if($v['password'])
			$v['password'] = password_hash(setting::PASS_KEY.$v['password'], PASSWORD_BCRYPT);
		else
			$v['password'] = $old['password'];

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

	function login($v){
		try{
			$username = $v['username'];
			$password = setting::PASS_KEY.$v['password'];

			$sql = "SELECT id,password,lang FROM user WHERE username = '$username'";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			if ($row) {
				if (password_verify($password, $row['password'])) {
					$_SESSION['id'] = $row['id'];
					
					$yesterday = strtotime("yesterday", time());
					$formattedYesterday = date('Y-m-d', $yesterday);
					$sql = "SELECT * FROM jard_log WHERE date like '$formattedYesterday'";
					$result = $this->pdo->query($sql);
					$rowJard = $result->fetch(PDO::FETCH_ASSOC);
					if(empty($rowJard)){
						$sql = "INSERT INTO jard_log(date) VALUES('$formattedYesterday')";
						$this->pdo->query($sql);

						$lastJardLogId = $this->pdo->lastInsertId();
						$store = new store();

						$jardList = json_decode($store->jardList(),true);
						foreach ($jardList['rows'] as $key => $value) {
							if($value['total'] != 0){
								$sql = "INSERT INTO jard_log_detail(id_jard_log,id_store,depp,cat,brand,product,qty,price_buy,total) VALUES('$lastJardLogId','{$value['id']}','{$value['depp']}','{$value['cat']}','{$value['brand']}','{$value['product']}','{$value['qty']}','{$value['price_buy']}','{$value['total']}')";
								$this->pdo->query($sql);
							}
							
						}
						// dsh($jardList);
					}


					
				}else{
					return json_encode(['result'=>false,'message'=>['field'=>'password','content'=>'Incorrect_Password','extra'=>':(']]);
				}
			}else{
				return json_encode(['result'=>false,'message'=>['field'=>'username','content'=>'Username_Not_Exist','extra'=>$v['username']]]);
			}
			return json_encode(['result'=>true,'lang'=>$row['lang']]);
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function checkLogin(){
		if(@$_SESSION['id'])
			return 'true';
		return 'false';
	}

	function getLang(){
		try{
			$sql = @"SELECT lang FROM user WHERE id = '{$_SESSION['id']}'";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			if(@$_SESSION['id'])
				return $row['lang'];
			else
				return setting::DEFAULT_LANG;
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	public function logout(){
		$_SESSION = [];
	}

	function userList(){
		try{
			$sql = "SELECT * FROM user";
			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function authUserPart(){
		try{
			$idUser = $this->idUser();

			$sql = "SELECT d.id,d.type,b.name AS branch,d.name AS depp,d.code FROM user_depp ud INNER JOIN depp_cat dc ON ud.id_depp = dc.id_depp INNER JOIN depp d ON d.id = dc.id_depp INNER JOIN branch b ON b.id = d.id_branch WHERE ud.id_user = '$idUser' GROUP BY d.code ORDER BY id ASC";
			// dsh($sql);
			$result = $this->pdo->query($sql);
			$rows['departments'] = $result->fetchAll(PDO::FETCH_ASSOC);

			if(empty($_SESSION['idDepp'])){
				$_SESSION['idDepp'] = @$rows['departments'][0]['id'];
			}

			$i = 0;
			foreach ($rows['departments'] as $key => $value) {
				if($value['id'] == $_SESSION['idDepp'])
					$rows['idDepp'] = $i;
				$i++;
			}

			// $_SESSION['idDepp'] = $rows[0]['id'];
			return json_encode($rows);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function updateAuthUserPart($idDEpp){
		try{
			$_SESSION['idDepp'] = $idDEpp;
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


}
$user = new user();

// dsh($user->login(['username'=>'a','password'=>'a']));


?>