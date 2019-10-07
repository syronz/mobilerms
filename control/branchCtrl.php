<?php
require_once realpath(__DIR__ . '/../class/branchClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $branch->show($_GET);
	break;
	case 'add':
		echo $branch->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $branch->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $branch->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'branchList':
		echo $branch->branchList();
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>