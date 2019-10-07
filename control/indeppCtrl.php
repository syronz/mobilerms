<?php
require_once realpath(__DIR__ . '/../class/indeppClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $indepp->show($_GET);
	break;
	case 'add':
		echo $indepp->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $indepp->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $indepp->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'indeppList':
		echo $indepp->indeppList();
	break;

	case 'indeppPrint':
		echo $indepp->indeppPrint($_GET['id']);
	break;

	case 'savePayout':
		echo $indepp->savePayout(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'indeppPayins':
		echo $indepp->indeppPayins($_GET['idIndepp']);
	break;

	case 'indeppEdit':
		echo $indepp->indeppEdit($_GET['id']);
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>