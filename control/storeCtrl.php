<?php
require_once realpath(__DIR__ . '/../class/storeClass.php');

switch (@$_GET['action']) {
	case 'list':
	echo $store->show($_GET);
	break;
	case 'add':
	echo $store->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
	echo $store->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
	echo $store->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'storeList':
	echo $store->storeList();
	break;

	case 'storeListByModel':
	echo $store->storeListByModel($_GET['idModel']);
	break;

	case 'jardList':
	echo $store->jardList();
	break;

	case 'jardListDate':
	echo $store->jardListDate($_GET['date']);
	break;

	


	
	default:
		# code...
	break;
}

// dsh($db->login('a','a'));

?>