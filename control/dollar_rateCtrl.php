<?php
require_once realpath(__DIR__ . '/../class/dollar_rateClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $dollar_rate->show($_GET);
	break;
	case 'add':
		echo $dollar_rate->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $dollar_rate->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $dollar_rate->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'dollar_rateList':
		echo $dollar_rate->dollar_rateList();
	break;

	case 'dollar_rateDepp':
		echo $dollar_rate->get_dollar_rate(@$_GET['idDepp']);
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>