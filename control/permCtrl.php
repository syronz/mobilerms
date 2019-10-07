<?php
require_once realpath(__DIR__ . '/../class/permClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $perm->show($_GET);
	break;
	case 'add':
		echo $perm->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $perm->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $perm->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'permList':
		echo $perm->permList();
	break;

	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>