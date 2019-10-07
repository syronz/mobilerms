<?php
require_once realpath(__DIR__ . '/../class/depp_catClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $depp_cat->show($_GET);
	break;
	case 'add':
		echo $depp_cat->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $depp_cat->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $depp_cat->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'depp_catList':
		echo $depp_cat->depp_catList();
	break;

	case 'deppCatAuth':
		echo json_encode($depp_cat->deppCatAuth($_GET['idDepp']));
	break;



	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));

?>