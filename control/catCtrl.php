<?php
require_once realpath(__DIR__ . '/../class/catClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $cat->show($_GET);
	break;
	case 'add':
		echo $cat->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $cat->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $cat->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'catList':
		echo $cat->catList();
	break;

	case 'catListDepp':
		echo $cat->catListDepp($_GET['idDepp']);
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>