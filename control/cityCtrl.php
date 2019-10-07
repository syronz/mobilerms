<?php
require_once realpath(__DIR__ . '/../class/cityClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $city->show($_GET);
	break;
	case 'add':
		echo $city->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $city->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $city->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'cityList':
		echo $city->cityList();
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>