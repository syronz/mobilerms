<?php
require_once realpath(__DIR__ . '/databaseClass.php');
ini_set('max_execution_time', 30000);
class extra extends database{

	function generateRandom(){
		$fp = fopen('please_delete/g.txt', 'w');
		for($i=0;$i<100000;$i++){
			$num = mt_rand(10000000,99999999);
			$strNum = substr(password_hash($num, PASSWORD_BCRYPT),7,-1).substr(password_hash($num+1, PASSWORD_BCRYPT),7,-1).$num;
			$finalNum = substr(preg_replace('/[^0-9]+/', '', $strNum),0,12);
			// dsh($finalNum);
			fwrite($fp, $finalNum."\n");
		}
	}

	function generateHash($file){
		// $fp = fopen("please_delete/$file.txt", 'r');
		try{
			$data = file_get_contents("please_delete/$file.txt");
			$data = explode("\n", $data);

			$valueKey = md5($file);


dsh(hash('sha512', SETTING::HASH_KEY.'465412234028'.$valueKey));
			return 0;

			foreach ($data as $key => $value) {
				$value = trim($value);
				if(strlen($value) == 12){
					$code = hash('sha512', SETTING::HASH_KEY.$value.$valueKey);
					$sql = "INSERT INTO credit(code,state) VALUES('$code','open');";
					$this->pdo->query($sql);
				// dsh($value,$code);
				}
			}
		}
		catch(PDOException $e){
			dsh($e);
		}

	}



	function generateCardNumber(){
		/*
		format:
		nABn-CnDn-nnnE
		E = A + D;
		B = C + E;

		*/
		try{
			$fp = fopen('please_delete/card.txt', 'w');
			for($i=0;$i<100000;$i++){
				$p1 = mt_rand(1000,9999);
				$p2 = substr('0000'.mt_rand(0,9999),-4);
				$p3 = substr('0000'.mt_rand(0,9999),-4);
				$num = $p1.$p2.$p3;
				$num[11] = (intval($num[1]) + intval($num[6])) % 10;
				$num[2] = abs(intval($num[4]) - intval($num[11]));
				$finalNum = substr($num, 0,4).'-'.substr($num, 4,4).'-'.substr($num, 8,4);
			// dsh($num,$finalNum);
				fwrite($fp, $finalNum."\n");
			}
		}
		catch(PDOException $e){
			dsh($e);
		}

	}


	function generateHashCrdNumber(){
		try{
			$data = file_get_contents("please_delete/cardSimple.txt");
			$data = explode("\n", $data);


			foreach ($data as $key => $value) {

				$str = trim($value);
				if(strlen($str) == 12){
					// dsh($str);
					$code = hash('sha512', SETTING::HASH_KEY.$str);
					$sql = "INSERT INTO card(card_number) VALUES('$code');";
					$this->pdo->query($sql);
				// dsh($value,$code);
				}
			}
		}
		catch(PDOException $e){
			dsh($e);
		}

	}

	





}


$ex = new extra();
// $ex->generateHashCrdNumber();
//$ex->generateHash('5000');


?>