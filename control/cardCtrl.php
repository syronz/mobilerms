<?php
require_once realpath(__DIR__ . '/../class/cardClass.php');

switch (@$_GET['action']) {

	case 'add':
		echo $card->add(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'cardList':
		echo $card->cardList($_GET['idAccount']);
	break;

	case 'addCredit':
		echo $card->addCredit(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'checkCardNumber':
		echo $card->checkCardNumber(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'checkCardPass':
		echo $card->checkCardPass(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'checkCardLogin':
		echo $card->checkCardLogin();
	break;
	case 'getCustomerInfo':
		echo $card->getCustomerInfo();
	break;
	case 'customerLogout':
		echo $card->customerLogout();
	break;

	case 'addCreditCrm':
		echo $card->addCreditCrm(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'tranListCrm':
		echo $card->tranListCrm($_GET);
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>