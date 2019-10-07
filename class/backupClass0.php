<?php
require_once realpath(__DIR__ . '/databaseClass.php');
require_once realpath(__DIR__ . '/../settingClass.php');

class backup extends database{
	private $table = 'backup';

	function addBackup(){
		if(!$this->perm($this->table,'add')){
			echo "<h1 style='color:red;text-align:center;'>You Haven't Permission</h1><meta http-equiv='refresh' content='2; url=".setting::APP_URL."' />";
			// sleep(3);
			// header('Location:'.setting::APP_URL);
			return false;
		}


		date_default_timezone_set("Asia/Baghdad");
		$file_name = date('Y-m-d_H.i.s',time()).'.sql';
		$target = "c:\\xampp\\htdocs\\hi\\backups\\$file_name";
		// in windows xampp
		// system("c:/xampp/mysql/bin/mysqldump -u root --password= exchange > $target"); 
		system("c:\\xampp\\mysql\\bin\\mysqldump -u root -p".setting::MYSQL_PASSWORD.' '.setting::DATABASE." > $target"); 

		//in linux
		// system("mysqldump -u root --password='787' hi > $target");

		header('Content-type: text/appdb');
		header('Content-Disposition: attachment; filename="' . $file_name);
		readfile($target);
		// @unlink($target);
		exit(0);
	}
}

$backup = new backup();
$backup->addBackup();


?>