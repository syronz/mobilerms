<?php
require_once realpath(__DIR__ . '/../class/deppClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $depp->show($_GET);
	break;
	case 'add':
		echo $depp->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $depp->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $depp->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'deppList':
		echo $depp->deppList();
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>