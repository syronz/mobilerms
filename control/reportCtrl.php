<?php
require_once realpath(__DIR__ . '/../class/reportClass.php');

switch (@$_GET['action']) {
	case 'snapshot':
		echo $report->snapshot($_GET);
	break;

	case 'dashboardBasicInfo':
		echo $report->dashboardBasicInfo($_GET);
	break;

	case 'dailySell':
		echo $report->dailySell($_GET);
	break;

	case 'dollarRateChart':
		echo $report->dollarRateChart($_GET);
	break;

	// balance report for extend snapchat detail
	case 'balanceReport':
		echo $report->balanceReport($_GET);
	break;

	case 'productSellReport':
		echo $report->productSellReport($_GET);
	break;

	case 'debtsReport':
		echo $report->debtsReport($_GET);
	break;

	case 'debtsReportSimple':
		echo $report->debtsReportSimple($_GET);
	break;

	
	default:
		# code...
		break;
}

// dsh($db->login('a','a'));













?>