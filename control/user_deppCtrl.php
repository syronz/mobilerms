<?php
require_once realpath(__DIR__ . '/../class/user_deppClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $user_depp->show($_GET);
	break;
	case 'add':
		echo $user_depp->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $user_depp->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $user_depp->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'user_deppList':
		echo $user_depp->user_deppList();
	break;

	case 'userDeppList':
		echo $user_depp->userDeppList();
	break;






	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));

?>