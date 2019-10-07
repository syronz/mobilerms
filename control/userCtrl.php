<?php
require_once realpath(__DIR__ . '/../class/userClass.php');

switch (@$_GET['action']) {
	case 'list':
		echo $user->show($_GET);
	break;
	case 'add':
		echo $user->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
		echo $user->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
		echo $user->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'login':
		echo $user->login(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'checkLogin':
		echo $user->checkLogin();
	break;
	case 'logout':
		echo $user->logout();
	break;

	case 'getLang':
		echo $user->getLang();
	break;

	case 'userList':
		echo $user->userList();
	break;

	case 'authUserPart':
		echo $user->authUserPart();
	break;

	case 'updateAuthUserPart':
		echo $user->updateAuthUserPart($_GET['id']);
	break;





	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>