<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/storeClass.php');
require_once realpath(__DIR__ . '/accountClass.php');
require_once realpath(__DIR__ . '/validClass.php');
require_once realpath(__DIR__ . '/dollar_rateClass.php');
require_once realpath(__DIR__ . '/accountClass.php');

class report extends database{
	private $table = 'report';
	private $struct = [];

	function snapshot($v = null){
		try{
			// dsh($v);
			$idDepp = $_SESSION['idDepp'];
			if(!$v['firstDate']){
				$firstDate = (new DateTime(Date('Y-m-d',time())))->modify('first day of this month')->format('Y-m-d');
				$lastDate = Date('Y-m-d',time());
				$range = 'depp'; //can be also all
				
			}
			else{
				$v = $this->toSave($v);
				// $firstDate = $v['firstDate'];
				// $lastDate = $v['lastDate'];
				$firstDate = (new DateTime($v['firstDate']))->format('Y-m-d');
				$lastDate = (new DateTime($v['lastDate']))->format('Y-m-d');
				$range = $v['range'];
			}

			$pointerDate = $firstDate;

			$i = 0;
			while ($pointerDate <= $lastDate) {
				$rows[$i]['date'] = $pointerDate;
				$pointerDate = (new DateTime($pointerDate))->modify('next day')->format('Y-m-d');

				

				if($range == 'depp'){
					//for resid, drawing
					$sql = "SELECT SUM(depp_balance) as deppResid FROM acc WHERE acc.id IN( SELECT   max(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account  WHERE a.type = 'drawing' AND ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id) ";
					$result = $this->pdo->query($sql);
					$rows[$i]['resid'] = $result->fetch(PDO::FETCH_ASSOC)['deppResid'];

					//for payable
					$sql = "SELECT SUM(depp_balance) as deppPayable FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('partner','customer','company') and ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id)  and acc.depp_balance <= 0 ";
					$result = $this->pdo->query($sql);
					$rows[$i]['payable'] = $result->fetch(PDO::FETCH_ASSOC)['deppPayable'];

					//for receivable
					$sql = "SELECT SUM(depp_balance) as deppReceivable FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('partner','customer','company') and ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id)  and acc.depp_balance >= 0 ";
					$result = $this->pdo->query($sql);
					$rows[$i]['receivable'] = $result->fetch(PDO::FETCH_ASSOC)['deppReceivable'];

					// dsh($rows[$i]['receivable'],$sql);

					//for cash
					$sql = "SELECT SUM(depp_balance) AS deppCash FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('cash') and ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id)";
					$result = $this->pdo->query($sql);
					$rows[$i]['cash'] = $result->fetch(PDO::FETCH_ASSOC)['deppCash'];

					//for store
					$sql = "SELECT SUM(depp_balance) AS deppStore FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('store') and ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id)";
					$result = $this->pdo->query($sql);
					$rows[$i]['store'] = $result->fetch(PDO::FETCH_ASSOC)['deppStore'];

					//for reExpense
					$sql = "SELECT SUM(depp_balance) as deppReExpense FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('expense') and ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id) and depp_balance <= 0 ";
					$result = $this->pdo->query($sql);
					$rows[$i]['reExpense'] = $result->fetch(PDO::FETCH_ASSOC)['deppReExpense'];

					//for dailyExpense
					$sql = "SELECT SUM(in_dollar) AS dailyExpense FROM acc INNER JOIN account a ON a.id = acc.id_account WHERE a.type = 'expense' AND acc.in_dollar > 0 AND date LIKE '{$rows[$i]['date']}%' AND acc.id_depp = '$idDepp'";
					$result = $this->pdo->query($sql);
					$rows[$i]['dailyExpense'] = $result->fetch(PDO::FETCH_ASSOC)['dailyExpense'];

					//for transactionCount
					$sql = "SELECT COUNT(id) as transactionCount FROM acc WHERE date LIKE '{$rows[$i]['date']}%' AND acc.id_depp = '$idDepp'";
					$result = $this->pdo->query($sql);
					$rows[$i]['transactionCount'] = $result->fetch(PDO::FETCH_ASSOC)['transactionCount'];

					//for equipment
					$sql = "SELECT SUM(depp_balance) as deppEquipment FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('equipment') and ac1.date <= '$pointerDate' and ac1.id_depp = '$idDepp' GROUP BY a.id)  ";
					$result = $this->pdo->query($sql);
					$rows[$i]['equipment'] = $result->fetch(PDO::FETCH_ASSOC)['deppEquipment'];

					$rows[$i]['balanceNegative'] = $rows[$i]['resid'] + $rows[$i]['payable'] + $rows[$i]['reExpense'];
					$rows[$i]['balancePosotive'] = $rows[$i]['receivable'] + $rows[$i]['cash'] + $rows[$i]['store'] + $rows[$i]['equipment'];
					$rows[$i]['balance'] = $rows[$i]['balanceNegative'] + $rows[$i]['balancePosotive'];
				}
				else{
					//for resid, drawing
					$sql = "SELECT SUM(balance) as allResid FROM acc WHERE acc.id IN( SELECT   max(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account  WHERE a.type = 'drawing' AND ac1.date <= '$pointerDate' GROUP BY a.id) ";
					$result = $this->pdo->query($sql);
					$rows[$i]['resid'] = $result->fetch(PDO::FETCH_ASSOC)['allResid'];

					//for payable
					$sql = "SELECT SUM(balance) as allPayable FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('partner','customer','company') and ac1.date <= '$pointerDate'  GROUP BY a.id) AND  acc.balance <= 0";
					$result = $this->pdo->query($sql);
					$rows[$i]['payable'] = $result->fetch(PDO::FETCH_ASSOC)['allPayable'];

					//for receivable
					$sql = "SELECT SUM(balance) as allReceivable FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('partner','customer','company') and ac1.date <= '$pointerDate'  GROUP BY a.id) AND acc.balance >= 0 ";
					$result = $this->pdo->query($sql);
					$rows[$i]['receivable'] = $result->fetch(PDO::FETCH_ASSOC)['allReceivable'];

					//for cash
					$sql = "SELECT SUM(balance) AS allCash FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('cash') and ac1.date <= '$pointerDate' GROUP BY a.id)";
					$result = $this->pdo->query($sql);
					$rows[$i]['cash'] = $result->fetch(PDO::FETCH_ASSOC)['allCash'];

					//for store
					$sql = "SELECT SUM(balance) AS allStore FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('store') and ac1.date <= '$pointerDate' GROUP BY a.id)";
					$result = $this->pdo->query($sql);
					$rows[$i]['store'] = $result->fetch(PDO::FETCH_ASSOC)['allStore'];

					//for reExpense
					$sql = "SELECT SUM(balance) as allReExpense FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('expense') and ac1.date <= '$pointerDate' GROUP BY a.id)  and balance <= 0 ";
					$result = $this->pdo->query($sql);
					$rows[$i]['reExpense'] = $result->fetch(PDO::FETCH_ASSOC)['allReExpense'];

					//for dailyExpense
					$sql = "SELECT SUM(in_dollar) AS dailyExpense FROM acc INNER JOIN account a ON a.id = acc.id_account WHERE a.type = 'expense' AND acc.in_dollar > 0 AND date LIKE '{$rows[$i]['date']}%'";
					$result = $this->pdo->query($sql);
					$rows[$i]['dailyExpense'] = $result->fetch(PDO::FETCH_ASSOC)['dailyExpense'];

					//for transactionCount
					$sql = "SELECT COUNT(id) as transactionCount FROM acc WHERE date LIKE '{$rows[$i]['date']}%'";
					$result = $this->pdo->query($sql);
					$rows[$i]['transactionCount'] = $result->fetch(PDO::FETCH_ASSOC)['transactionCount'];

					//for equipment
					$sql = "SELECT SUM(balance) as allEquipment FROM acc WHERE acc.id IN(SELECT   MAX(ac1.id) FROM account a INNER JOIN acc ac1 ON a.id = ac1.id_account WHERE a.type IN ('equipment') and ac1.date <= '$pointerDate' GROUP BY a.id)  ";
					$result = $this->pdo->query($sql);
					$rows[$i]['equipment'] = $result->fetch(PDO::FETCH_ASSOC)['allEquipment'];

					$rows[$i]['balanceNegative'] = $rows[$i]['resid'] + $rows[$i]['payable'] + $rows[$i]['reExpense'];
					$rows[$i]['balancePosotive'] = $rows[$i]['receivable'] + $rows[$i]['cash'] + $rows[$i]['store'] + $rows[$i]['equipment'];
					$rows[$i]['balance'] = $rows[$i]['balanceNegative'] + $rows[$i]['balancePosotive'];
				}
				$i++;
			}

// dsh($rows);
			return json_encode(['result'=>true,'rows'=>$rows]);
		}catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


	function dashboardBasicInfo($v = null){
		try{
			$year = @$v['year'];
			$month = @$v['month'];
			$idDepp = @$v['idDepp'];

			$year = $year ? $year : Date('Y');
			$month = $month ? $month : Date('m');

			$vCheck1 = $this->valid($year,['inRange'],null,'year',2000,2500);
			$vCheck2 = $this->valid($month,['inRange'],null,'month',1,12);
			$vCheck3 = $idDepp ? $this->valid($idDepp,['inRange'],null,'department',1,10000000) : [];
			$vCheckArr = $this->arrValid($vCheck1,$vCheck2,$vCheck3);
			if(!$vCheckArr['result'])
				return json_encode($vCheckArr);


			$where = "WHERE date LIKE '$year-$month%'";
			$where .= $idDepp ? " AND id_depp = $idDepp" : '';

			$sql = "SELECT count(*) as totalSellCount FROM outdepp $where AND type = 'active'";
			$result = $this->pdo->query($sql);
			$row['totalSellCount'] = $result->fetch(PDO::FETCH_ASSOC)['totalSellCount'];

			$sql = "SELECT sum(total * (100 - discount) / 100) as totalSellAmount FROM outdepp $where ";
			$result = $this->pdo->query($sql);
			$row['totalSellAmount'] = $result->fetch(PDO::FETCH_ASSOC)['totalSellAmount'];

			$sql = "SELECT count(*) as newCustomer FROM account WHERE date_register LIKE '$year-$month%' AND type IN ('customer','partner')";
			$result = $this->pdo->query($sql);
			$row['newCustomer'] = $result->fetch(PDO::FETCH_ASSOC)['newCustomer'];

			$sql = "SELECT sum(total) as totalBuyAmount FROM indepp $where AND type = 'active'";
			$result = $this->pdo->query($sql);
			$row['totalBuyAmount'] = $result->fetch(PDO::FETCH_ASSOC)['totalBuyAmount'];

			$sql = "SELECT sum(p.price_buy * s.qty) as totalJard from store s inner join product p on p.id = s.id_product";
			$result = $this->pdo->query($sql);
			$row['totalJard'] = $result->fetch(PDO::FETCH_ASSOC)['totalJard'];

			$account = new account();
			$row['storeAccount'] = $account->lastBalance(11);

			// dsh($sql,$row);
			return json_encode(['result'=>true,'rows'=>$row]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


	function dailySell($v = null){
		try{
			$v = $this->toSave($v);
			$year = @$v['year'];
			$month = @$v['month'];
			$idDepp = @$v['idDepp'];

			$year = $year ? $year : Date('Y');
			$month = $month ? $month : Date('m');

			$vCheck1 = $this->valid($year,['inRange'],null,'year',2000,2500);
			$vCheck2 = $this->valid($month,['inRange'],null,'month',1,12);
			$vCheck3 = $idDepp ? $this->valid($idDepp,['inRange'],null,'department',1,10000000) : [];
			$vCheckArr = $this->arrValid($vCheck1,$vCheck2,$vCheck3);
			if(!$vCheckArr['result'])
				return json_encode($vCheckArr);


			$where = "WHERE o.date LIKE '$year-$month%'";
			$where .= $idDepp ? " AND o.id_depp = $idDepp" : '';


			$sql = "SELECT substr(o.date,9,2) as date,SUM(o.total * (100 - o.discount) / 100) as sumTotal FROM outdepp o $where GROUP BY substr(o.date, 1,10)";
			$result = $this->pdo->query($sql);
			$row['thisMonth'] = $result->fetchAll(PDO::FETCH_ASSOC);

			$arrThisMonth = [];
			$arrPreMonth = [];
			for ($i=0; $i < 31; $i++) { 
				$arrThisMonth[$i] = $arrPreMonth[$i] = 0;
			}

			foreach ($row['thisMonth'] as $key => $value) {
				$arrThisMonth[intval($value['date'])] = $value['sumTotal'];
			}


			if($month == 1){
				$month = 12;
				$year--;
			}
			else
				$month--;

			$month = substr('0'.$month,-2);

			$where = "WHERE o.date LIKE '$year-$month%'";
			$where .= $idDepp ? " AND o.id_depp = $idDepp" : '';

			$sql = "SELECT substr(o.date,9,2) as date,SUM(o.total * (100 - o.discount) / 100) as sumTotal FROM outdepp o $where GROUP BY substr(o.date, 1,10)";
			$result = $this->pdo->query($sql);
			$row['preMonth'] = $result->fetchAll(PDO::FETCH_ASSOC);

			// dsh($sql);

			foreach ($row['preMonth'] as $key => $value) {
				$arrPreMonth[intval($value['date'])] = $value['sumTotal'];
			}

			return json_encode(['result'=>true,'rows'=>[$arrThisMonth,$arrPreMonth]]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


	function dollarRateChart(){
		try{
			$idDepp = $_SESSION['idDepp'];

			$sql = "SELECT b.id_city as idCity FROM depp d inner join branch b on b.id = d.id_branch WHERE d.id = '$idDepp'";
			$result = $this->pdo->query($sql);
			$idCity = $result->fetch(PDO::FETCH_ASSOC)['idCity'];

			$dRate = new dollar_rate();

			
			$dollarRate = $dRate->get_dollar_rate();

			$firstDate = (new DateTime(Date('Y-m-d',time())))->format('Y-m-d');

			$pointerDate = $firstDate;
			$pointerDate = (new DateTime($pointerDate))->modify('-9 day')->format('Y-m-d');
			$arr = [];
			for($i=0; $i<10; $i++){
				$arr['labels'][$i] = substr($pointerDate, 8,2);

				$dollarRate = $arr['dollarRate'][$i] = $dRate->get_dollar_rate($idDepp,$pointerDate) ? $dRate->get_dollar_rate($idDepp,$pointerDate) : $dollarRate;
				$pointerDate = (new DateTime($pointerDate))->modify('next day')->format('Y-m-d');
			}

			// $arr['labels'] = array_reverse($arr['labels']); 
			// $arr['dollarRate'] = array_reverse($arr['dollarRate']);



			return json_encode(['result'=>true,'rows'=>$arr]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function balanceReport($v){
		try{
			$v = $this->toSave($v);
			$date = @$v['date'];
			$idDepp = @$v['idDepp'];

			$date = $date ? $date : Date('Y-m-d');

			$date = (new DateTime($date))->modify('next day')->format('Y-m-d');


			$vCheck = $idDepp ? $this->valid($idDepp,['inRange'],null,'department',1,10000000) : ['result'=>true];
			if(!$vCheck['result'])
				return json_encode($vCheck);

			$where = $idDepp ? " AND id_depp = '$idDepp' " : '';

			if($idDepp){
				$sql = "SELECT a.name,a.`type`,acc.depp_balance as balance FROM acc INNER JOIN account a ON a.id = acc.id_account  WHERE acc.id IN(SELECT max(id) FROM acc WHERE date < '$date' AND id_depp = '$idDepp'  GROUP BY id_account) AND a.type <> 'noAccount' AND depp_balance < 0  ORDER BY depp_balance";
				$result = $this->pdo->query($sql);
				$rows['creditor'] = $result->fetchAll(PDO::FETCH_ASSOC);

				// dsh($sql);

				$sql = "SELECT a.name,a.`type`,acc.depp_balance as balance FROM acc INNER JOIN account a ON a.id = acc.id_account WHERE acc.id IN(SELECT max(id) FROM acc WHERE date < '$date' AND id_depp = '$idDepp'  GROUP BY id_account)  AND a.type not in('noAccount','expense') AND depp_balance > 0  ORDER BY depp_balance DESC";
				$result = $this->pdo->query($sql);
				$rows['debtor'] = $result->fetchAll(PDO::FETCH_ASSOC);
			}
			else{
				$sql = "SELECT a.name,a.`type`,acc.balance as balance FROM acc INNER JOIN account a ON a.id = acc.id_account  WHERE acc.id IN(SELECT max(id) FROM acc WHERE date < '$date'  GROUP BY id_account) AND a.type <> 'noAccount' AND balance < 0  ORDER BY balance";
				$result = $this->pdo->query($sql);
				$rows['creditor'] = $result->fetchAll(PDO::FETCH_ASSOC);

				$sql = "SELECT a.name,a.`type`,acc.balance as balance FROM acc INNER JOIN account a ON a.id = acc.id_account WHERE acc.id IN(SELECT max(id) FROM acc WHERE date < '$date'  GROUP BY id_account)  AND a.type not in('noAccount','expense') AND balance > 0  ORDER BY balance DESC";
				$result = $this->pdo->query($sql);
				$rows['debtor'] = $result->fetchAll(PDO::FETCH_ASSOC);
			}

			$maxCount = max(count($rows['creditor']),count($rows['debtor']));
			$arr = [];
			$arr['total']['debtor'] = 0;
			$arr['total']['creditor'] = 0;
			for($i=0; $i < $maxCount; $i++){
				$arr['total']['debtor'] += @$rows['debtor'][$i]['balance'];
				$arr['total']['creditor'] += @$rows['creditor'][$i]['balance'];
				array_push($arr, @['debtorName' => $rows['debtor'][$i]['name'], 'debtorType'=>$rows['debtor'][$i]['type'], 'debtorBalance'=>$rows['debtor'][$i]['balance'], 'creditorName'=>$rows['creditor'][$i]['name'], 'creditorType'=>$rows['creditor'][$i]['type'], 'creditorBalance'=>$rows['creditor'][$i]['balance'] ]);
			}

			return json_encode(['result'=>true,'rows'=>$arr]);

		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}


	function productSellReport($v = null){
		try{
			$v = $this->toSave($v);
			$start = @$v['start'] ? $v['start'] : (new DateTime(Date('Y-m-d',time())))->modify('first day of this month')->format('Y-m-d');;
			$end = @$v['end'] ? $v['end'] : Date('Y-m-d',time());
			$cat = @$v['cat'] ? $v['cat'] : 'all';
			$brand = @$v['brand'] ? $v['brand'] : 'all';
			$model = @$v['model'] ? $v['model'] : 'all';
			$product = @$v['product'] ? $v['product'] : 'all' ;
			$idDepp = @$v['idDepp'] ? $v['idDepp'] : $_SESSION['idDepp'] ;
			$limit = @$v['limit'] ? $v['limit'] : 10 ;
			$order = @$v['order'] ? $v['order'] : ' total_profit' ;
			$orderType = @$v['orderType'] ? $v['orderType'] : ' DESC' ;

			$end = Date('Y-m-d',strtotime($end) + 86400);

			$where = " o.date >= '$start' AND o.date <= '$end' AND o.id_depp = '$idDepp' ";
			if ($cat != 'all') {
				$where .= " AND b.id_cat = '$cat' ";
			}

			if ($model != 'all') {
				$where .= " AND p.id_model = '$model' ";
			}

			if ($brand != 'all') {
				$where .= " AND p.id_brand = '$brand' ";
			}

			if ($product != 'all') {
				$where .= " AND p.id = '$product' ";
			}

			$sql = "SELECT p.code AS code,c.name AS category,b.name AS brand,m.name AS model,p.name AS product,sum(oe.qty) AS total_qty,SUM(oe.qty * oe.cost) AS total_cost,SUM(oe.qty * oe.profit_one) AS total_profit FROM outdepp_extra oe INNER JOIN outdepp o ON o.id = oe.id_outdepp INNER JOIN product p ON p.id = oe.id_product INNER JOIN brand b ON b.id = p.id_brand LEFT JOIN  model m ON m.id = p.id_model INNER JOIN cat c ON c.id = b.id_cat WHERE $where GROUP BY p.id ORDER BY $order $orderType LIMIT $limit";

			$result = $this->pdo->query($sql);
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);

			$totalQty = 0;
			$totalSell = 0;
			$totalProfit = 0;
			foreach ($rows as $key => $value) {
				$totalQty += $value['total_qty'];
				$totalSell += $value['total_cost'];
				$totalProfit += $value['total_profit'];
			}
		
			array_push($rows, ['code'=>'Total','category'=>'-','brand'=>'-','model'=>'-','product'=>'-','total_qty'=>$totalQty,'total_cost'=>$totalSell,'total_profit'=>$totalProfit]);
			return json_encode(['result'=>true,'rows'=>$rows]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function debtsReport($v){
		try{
			$v = $this->toSave($v);
			$day = @$v['day'] ? $v['day'] : 15;
			$idDepp = @$v['idDepp'] ? $v['idDepp'] : $_SESSION['idDepp'] ;
			$accountType = @$v['accountType'] ? $v['accountType'] : 'partner';

			$day--;
			$date = (new DateTime(Date('Y-m-d',time())))->modify("-$day day")->format('Y-m-d');

			$sql = "SELECT o.id_account FROM outdepp o INNER JOIN account a ON a.id = o.id_account WHERE o.date  < '$date' AND o.id_depp = '$idDepp' AND o.status = 'progress' AND a.`type` = '$accountType' GROUP BY o.id_account";
			$rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

			if (empty($rows)) {
				return json_encode(['result'=>false,'message'=>'No Data']);
			}


			$arrIdAccount = [];
			foreach ($rows as $value) {
				array_push($arrIdAccount, $value['id_account']);
			}
			$arrIdAccount = implode(',', $arrIdAccount);


			$sql = "SELECT  X.id,X.id_account,X.depp_balance,X.name,X.date
			FROM acc JOIN (
			SELECT Pr1.id, Pr1.id_account,Pr1.depp_balance,a.name,substr(Pr1.date,1,10) as date
			FROM acc Pr1 JOIN acc Pr2 ON Pr1.id_account = Pr2.id_account AND Pr1.id <= Pr2.id
			INNER JOIN account a ON a.id = Pr1.id_account
			WHERE Pr1.id_depp = '$idDepp' AND a.type = '$accountType' AND Pr1.id_account IN ($arrIdAccount)
			GROUP BY Pr1.id_account, Pr1.id
			HAVING COUNT(*) <= 3
			) X on X.id = acc.id
			ORDER BY id_account,id DESC";
			$rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

			$idAccount = 0;
			$arrAccount = [];
			$i = -1;
			$k = 1;
			foreach ($rows as $value) {
				if($idAccount == $value['id_account']){
					$arrAccount[$i]["balance$k"] = $value['depp_balance'];
					$arrAccount[$i]["date$k"] = $value['date'];
					$k++;
					continue;
				}
				else{
					$i++;
					$k = 1;
					$arrAccount[$i]["name"] = $value['name'];
					$arrAccount[$i]["id_account"] = $value['id_account'];
					$idAccount = $value['id_account'];
					$arrAccount[$i]["balance$k"] = $value['depp_balance'];
					$arrAccount[$i]["date$k"] = $value['date'];
					$k++;
				}
			}

			foreach ($arrAccount as &$value) {
				$sql = "SELECT o.id,o.invoice,substr(o.date,1,10) as date,o.total,o.type,o.status FROM outdepp o INNER JOIN account a ON a.id = o.id_account WHERE o.date  < '$date' AND o.id_depp = '$idDepp' AND o.status = 'progress' AND o.`id_account` = '{$value['id_account']}'";
				$value['outdepp'] = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			}

			return json_encode(['result'=>true,'rows'=>$arrAccount]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	function debtsReportSimple($v){
		try{
			$v = $this->toSave($v);
			$day = @$v['day'] ? $v['day'] : 15;
			$idDepp = @$v['idDepp'] ? $v['idDepp'] : $_SESSION['idDepp'] ;
			$accountType = @$v['accountType'] ? $v['accountType'] : 'partner';

			$day--;
			$date = (new DateTime(Date('Y-m-d',time())))->modify("-$day day")->format('Y-m-d');


			$sql = "SELECT a.id,a.name,substr(acc.date,1,10) as date,acc.depp_balance,DATEDIFF(now(),acc.date) AS day
					FROM acc INNER JOIN account a ON a.id = acc.id_account
					WHERE acc.id in(
					select max(id) FROM acc WHERE date < '$date' GROUP BY id_account
					)
					AND a.type = '$accountType'
					GROUP BY a.id 
					ORDER BY day DESC";

			$rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

			foreach ($rows as $key => &$value) {
				$sql = "SELECT sum(in_dollar) as totalPaid FROM acc WHERE id_account = '{$value['id']}' AND in_dollar < 0 AND date >= '$date'";
				$row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
				$value['paid'] = abs($row['totalPaid']);
				$value['remained'] = $value['depp_balance']  - $value['paid'];
				if($value['remained'] <= 0)
					unset($rows[$key]);
				// dsh($row);
			}

			$day++;
			$date = (new DateTime(Date('Y-m-d',time())))->modify("-$day day")->format('Y-m-d');
			foreach ($rows as $key => &$value) {
				$sql = "SELECT sum(in_dollar) as totalPaid FROM acc WHERE id_account = '{$value['id']}' AND in_dollar < 0 AND date >= '$date'";
				$row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
				$value['paid'] = abs($row['totalPaid']);

				$sql = "SELECT acc.depp_balance	FROM acc INNER JOIN account a ON a.id = acc.id_account
					WHERE acc.id in(
					select max(id) FROM acc WHERE date < '$date' GROUP BY id_account
					)
					AND a.type = '$accountType' AND a.id = '{$value["id"]}';
					";
				$value['depp_balance1'] = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['depp_balance'];

				$value['remained1'] = $value['depp_balance1']  - $value['paid'];
				if($value['remained1'] <= 0)
					unset($rows[$key]);
			}

			$day++;
			$date = (new DateTime(Date('Y-m-d',time())))->modify("-$day day")->format('Y-m-d');
			foreach ($rows as $key => &$value) {
				$sql = "SELECT sum(in_dollar) as totalPaid FROM acc WHERE id_account = '{$value['id']}' AND in_dollar < 0 AND date >= '$date'";
				$row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
				$value['paid'] = abs($row['totalPaid']);

				$sql = "SELECT acc.depp_balance	FROM acc INNER JOIN account a ON a.id = acc.id_account
					WHERE acc.id in(
					select max(id) FROM acc WHERE date < '$date' GROUP BY id_account
					)
					AND a.type = '$accountType' AND a.id = '{$value["id"]}';
					";
				$value['depp_balance2'] = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['depp_balance'];


				$value['remained2'] = $value['depp_balance2']  - $value['paid'];
				if($value['remained2'] <= 0)
					unset($rows[$key]);
			}

			// dsh($rows);



			return json_encode(['result'=>true,'rows'=>$rows]);
		}
		catch(PDOEXCEPTION $e){
			dsh($e);
		}
	}

	
}
$report = new report();

// dsh(json_decode($report->debtsReportSimple(['day'=>16])));






?>