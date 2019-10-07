<?php
require_once realpath(__DIR__ . '/../class/mobile_repairmanClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $mobile_repairman->show($_GET);
	break;
	case 'add':
		echo $mobile_repairman->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $mobile_repairman->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $mobile_repairman->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'mobile_repairmanList':
		echo $mobile_repairman->mobile_repairmanList();
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>