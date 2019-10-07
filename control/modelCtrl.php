<?php
require_once realpath(__DIR__ . '/../class/modelClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $model->show($_GET);
	break;
	case 'add':
		echo $model->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $model->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $model->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'modelList':
		echo $model->modelList();
	break;

	case 'modelListByBrand':
		echo $model->modelListByBrand($_GET['idBrand']);
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));

?>