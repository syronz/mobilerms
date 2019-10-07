<?php
require_once realpath(__DIR__ . '/../class/payinClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $payin->show($_GET);
	break;
	case 'add':
		echo $payin->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $payin->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $payin->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'payinList':
		echo $payin->payinList();
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>