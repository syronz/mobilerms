<?php
require_once 'class/databaseClass.php';

// dsh($_GET);
$_GET['action'] = 'list';
$_GET['order'] = $_GET['sortFiled'].' '.$_GET['sortType'];
$_GET['limit'] = ' 0,100000000';


ob_start();
require_once 'control/'.$_GET['part'].'Ctrl.php';
$data = json_decode(ob_get_clean(),true);
// dsh($data);

$arrColumns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ'];


$sheetSearch = '';
if($_GET['search'])
     $sheetSearch = " Filter({$_GET['search']})"; 

$sheetName = strtoupper($_GET['part']).$sheetSearch;


// dsh($disInfo);

// die();
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Baghdad');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once 'class/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Diako Amir")
							 ->setLastModifiedBy("Diako Amir")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);


$k = 0;
foreach ($data['rows'][0] as $key => $value) {
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue($arrColumns[$k++].'1', $key);
}




foreach ($data['rows'] as $key => $value) {
      $k = 0;
      foreach ($value as $cell) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($arrColumns[$k++].($key+2), $cell);
      }
}

$objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$arrColumns[$k-1].'1');

$k = 0;
foreach ($data['rows'][0] as $key => $value) {
      $objPHPExcel->getActiveSheet()->getColumnDimension($arrColumns[$k++])->setAutoSize(true);
}

$objPHPExcel->getActiveSheet()->freezePane('A2');



$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                          ->setSize(10);


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($sheetName);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save(str_replace('.php', '.xlsx', __FILE__));

$num = date('Y-m-d His');
$fileName = strtoupper($_GET['part'])." [$num]";
$objWriter->save("excel_archive/".strtoupper($_GET['part'])." [$num].xlsx");
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

// die();

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename='$fileName.xlsx'");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;


// Save Excel 95 file
/*
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
*/



?>