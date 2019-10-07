<?php
require_once realpath(__DIR__ . '/databaseClass.php');

class valid extends database{
	function isMobile($mobile){
		return preg_match('/^07\d{9}$/', $mobile);
	}

	function isPhone($phone){
		return preg_match('/^\d{7}$/', $phone);
	}

	function isUnique($name,$table,$field){
		try{
			// $table = $data['table'];
			// $field = $data['field'];
			// $name = $data['name'];
			$sql = "SELECT id FROM `$table` WHERE UPPER($field) LIKE UPPER('$name')";
			$result = $this->pdo->query($sql);
			$row = $result->fetch(PDO::FETCH_ASSOC);
			if($row)
				return false;
			else
				return true;
		}
		catch(PDOException $e){
			dsh($e);
		}
	}

	function maxLength200($str){
		if(strlen($str) < 200)
			return true;
		else
			return false;
	}

	function isName($str){
		// return preg_match('/^\pL+$/', $str);
		if(!$str)
			return false;
		else
			return true;
	}

	function inRange($num,$min,$max){
		if($num <= $max && $num >= $min)
			return true;
		else
			return false;
	}

	function isToday($date){
		$date = date('Y-m-d',strtotime($date));
		$now = date('Y-m-d',time());
		if (strpos($date, $now) !== FALSE)
			return true;
		else
			return false;
	}

	function isPerm($v){
		return preg_match('/^[0,1]{4}$/', $v);
	}

	function isEmail($v){
		return filter_var($v, FILTER_VALIDATE_EMAIL);
	}

	function check($v,$arrCheck,$table = null, $field = null,$min = null,$max = null){
		$v = htmlentities($v);
		$message = [];
		$result = true;
		if(in_array('isMobile', $arrCheck)){
			if(!$this->isMobile($v)){
				array_push($message, ['field'=>$field,'content'=>'Invalid_Mobile','extra'=>'']);
				$result = false;
			}
		}
		if(in_array('isPhone', $arrCheck)){
			if(!$this->isPhone($v)){
				array_push($message, ['field'=>$field,'content'=>'Invalid_Phone','extra'=>'']);
				$result = false;
			}
		}
		if(in_array('isUnique', $arrCheck)){
			if(!$this->isUnique($v,$table,$field)){
				array_push($message,['field'=>$field,'content'=>'Not_Unique','extra'=>'']);
				$result = false;
			}
		}

		if(in_array('maxLength200', $arrCheck)){
			if(!$this->maxLength200($v,$table,$field)){
				array_push($message,['field'=>$field,'content'=>'Number_Of_Charachter_Is_More_Than_200','extra'=>'']);
				$result = false;
			}
		}

		if(in_array('isName', $arrCheck)){
			if(!$this->isName($v)){
				array_push($message,['field'=>$field,'content'=>'Name_Not_Walid','extra'=>'']);
				$result = false;
			}
		}

		if(in_array('inRange', $arrCheck)){
			$v = floatval($v);
			if(!$this->inRange($v,$min,$max)){
				array_push($message, ['field'=>$field,'content'=>'Not_In_Range','extra'=>" $min:$max "]);
				$result = false;
			}
		}

		if(in_array('isEmail', $arrCheck)){
			if(!$this->isEmail($v)){
				array_push($message,['field'=>$field,'content'=>'Not_Valid_Email','extra'=>'']);
				$result = false;
			}
		}

		if(in_array('isToday', $arrCheck)){
			if(!$this->isToday($v)){
				array_push($message,['field'=>$field,'content'=>'It_is_not_today','extra'=>'']);
				$result = false;
			}
		}

		return ['result'=>$result,'message'=>$message];
	}


	function required($v,$arrData){
		$result = true;
		$message = [];
		foreach ($arrData as $key=>$value) {
			if(@$v[$key] == ''){
				array_push($message, ['field'=>$value,'content'=>'Not_Be_Empty','extra'=>'']);
				$result = false;
			}
		}
		return ['result'=>$result,'message'=>$message];
	}



}
$valid = new valid;

// dsh($valid->isToday('2015/12/18'));
// dsh($valid->isMobile('07505149171'));
// dsh($valid->isUnique('college','name','mAth'));
// dsh($valid->check('Math',['isMobile','isPhone','isUnique'],'college','name'));
// dsh($valid->inRange("0.4",0.0001,99999999999999));
?>