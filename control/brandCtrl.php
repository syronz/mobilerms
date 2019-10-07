<?php
require_once realpath(__DIR__ . '/../class/brandClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $brand->show($_GET);
	break;
	case 'add':
		echo $brand->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $brand->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $brand->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'brandList':
		echo $brand->brandList();
	break;


	case 'listDepp':
		echo $brand->showDepp($_GET);
	break;
	case 'addDepp':
		echo $brand->addDepp(json_decode(file_get_contents("php://input"),TRUE),$_GET['idDepp']);
	break;
	case 'editDepp':
		echo $brand->editDepp(json_decode(file_get_contents("php://input"),TRUE),$_GET['idDepp']);
	break;
	case 'deleteDepp':
		echo $brand->deleteDepp(json_decode(file_get_contents("php://input"),TRUE),$_GET['idDepp']);
	break;

	case 'brandListDepp':
		echo $brand->brandListDepp(@$_GET['idDepp']);
	break;

	case 'getBrandByCat':
		echo $brand->getBrandByCat(@$_GET['idCat']);
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>








