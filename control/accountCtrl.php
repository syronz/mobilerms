<?php
require_once realpath(__DIR__ . '/../class/accountClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $account->show($_GET);
	break;
	case 'add':
		echo $account->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $account->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $account->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'accountList':
		echo $account->accountList(@$_GET['accountType']);
	break;

	case 'accountPayList':
		echo $account->accountPayList(@$_GET['idDepp']);
	break;

	case 'accountDeppList':
		echo $account->accountDeppList($_GET);
	break;

	case 'accountDeppViewList':
		echo $account->accountDeppViewList($_GET);
	break;

	case 'newAcc':
		echo $account->newAcc(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'getAccountByMobile':
		echo $account->getAccountByMobile($_GET['mobile']);
	break;

	case 'addCustomer':
		echo $account->addCustomer(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'accountInfo':
		echo $account->accountInfo($_GET);
	break;

	case 'cashDeppViewList':
		echo $account->cashDeppViewList($_GET);
	break;

	case 'lastBalance':
		echo $account->lastBalance($_GET['id']);
	break;

	case 'idCashInDepp':
		echo $account->idCashInDepp();
	break;

	case 'transferMoney':
		echo $account->transferMoney(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'deleteAcc':
		echo $account->deleteAcc(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'editAcc':
		echo $account->editAcc(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'saveTrn':
		echo $account->saveTrn(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'lastBalanceDepp':
		echo $account->lastBalanceDepp($_GET['id']);
	break;

	case 'accountViewList':
		echo $account->accountViewList($_GET);
	break;

	case 'accountTollList':
		echo $account->accountTollList();
	break;



	
	default:
		# code...
		break;
}

// dsh($account->newAcc(['dollar'=>50,'id_account_other'=>['id'=>3],'id_account'=>1]));













?>