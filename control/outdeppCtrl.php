<?php
require_once realpath(__DIR__ . '/../class/outdeppClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $outdepp->show($_GET);
	break;
	case 'add':
		echo $outdepp->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $outdepp->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $outdepp->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'outdeppList':
		echo $outdepp->outdeppList();
	break;

	case 'outdeppPrint':
		echo $outdepp->outdeppPrint($_GET['id']);
	break;

	case 'savePayin':
		echo $outdepp->savePayin(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'outdeppPayins':
		echo $outdepp->outdeppPayins($_GET['idoutdepp']);
	break;

	case 'outdeppEdit':
		echo $outdepp->outdeppEdit($_GET['id']);
	break;

	case 'getInvoice':
		echo $outdepp->invoiceGenerate();
	break;

	case 'toggleStatus':
		echo $outdepp->toggleStatus($_GET['id']);
	break;

	

	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>