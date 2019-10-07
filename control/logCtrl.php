<?php
require_once realpath(__DIR__ . '/../class/logClass.php');
switch (@$_GET['action']) {
	case 'list':
	echo $log->show($_GET);
	break;

	case 'showExtra':
	echo $log->showExtra($_GET['idLog']);
	break;

	case 'logPrint':
	echo $log->logPrint(json_decode(file_get_contents("php://input"),TRUE));
	break;
	

	

	

	
	default:
		# code...
	break;
}

// dsh($db->login('a','a'));

?>