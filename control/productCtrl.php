<?php
require_once realpath(__DIR__ . '/../class/productClass.php');
switch (@$_GET['action']) {
	case 'list':
	echo $product->show($_GET);
	break;
	case 'add':
	echo $product->add(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'edit':
	echo $product->edit(json_decode(file_get_contents("php://input"),TRUE));
	break;
	case 'delete':
	echo $product->delete(json_decode(file_get_contents("php://input"),TRUE));
	break;

	case 'productList':
	echo $product->productList();
	break;

	case 'productListByModel':
	echo $product->productListByModel($_GET['idModel']);
	break;

	case 'productBycode':
	echo $product->productBycode($_GET['code']);
	break;

	case 'productListByBrand':
	echo $product->productListByBrand($_GET['idBrand']);
	break;

	
	default:
		# code...
	break;
}

// dsh($db->login('a','a'));

?>