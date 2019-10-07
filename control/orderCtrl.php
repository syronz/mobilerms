<?php
require_once realpath(__DIR__ . '/../class/orderClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $order->show($_GET);
	break;
	case 'addMobile':
		echo $order->addMobile(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $order->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $order->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'getInvoice':
		echo $order->invoiceGenerate();
	break;

	
	case 'orderPrint':
		echo $order->orderPrint($_GET['id']);
	break;

	case 'savePayin':
		echo $order->savePayin(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'orderPayins':
		echo $order->orderPayins($_GET['idorder']);
	break;

	/*
	

	

	

	case 'outdeppEdit':
		echo $outdepp->outdeppEdit($_GET['id']);
	break;
	*/

	
	

	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>