<?php
require_once realpath(__DIR__ . '/../class/rangeClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $range->show($_GET);
	break;
	case 'add':
		echo $range->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $range->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $range->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'rangeList':
		echo $range->rangeList();
	break;




	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>








