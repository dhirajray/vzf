<?php

error_reporting(E_WARNING & E_ERROR);
error_reporting(E_ALL);
set_time_limit(0);
mysql_connect("localhost", "root", "root") or die(mysql_error());
mysql_select_db("ip_location") or die(mysql_error());

echo "Csv File to Database table";

class Logging 
{
	// declare log file and file pointer as private properties
	private $log_file, $fp;
	// set log file (path and name)
	public function chkme()
	{
		echo "Yes I am calling";	
	}
	public function lfile($path) 
	{
		$this->log_file = $path;
	}
	// write message to the log file
	public function lwrite($message) 
	{
		// if file pointer doesn't exist, then open log file

		if (!is_resource($this->fp)) 
		{
			$this->lopen();
		}
		
		// define script name
		$script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
		
		
		$time = @date('[d/M/Y:H:i:s]');
		
		//echo $message; exit;
		// write current time, script name and message to the log file
		fwrite($this->fp, "$time: $message" . PHP_EOL); //($script_name) 
	}

	// close log file (it's always a good idea to close a file when you're done with it)
	public function lclose() 
	{
		fclose($this->fp);
	}
	// open log file (private method)
	private function lopen() 
	{
		// in case of Windows set default log file
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
		{
			$log_file_default = 'c:/php/logfile.txt';
		}
		// set default log file for Linux and other systems
		else 
		{
			$log_file_default = './logfile.txt';
		}
		
		// define log file from lfile method or use previously set default
		 $lfile = $this->log_file ? $this->log_file : $log_file_default;
	
		// open log file for writing only and place file pointer at the end of the file
		// (if the file does not exist, try to create it)
		$this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
	}
}

class MyException extends Exception
{
	public $log;
	
	public function getErrorReport() {
		$this->log = new Logging();
//		echo "Error: " . $this->getMessage() . "<br />File: " . $this->getFile() . "<br />Line: " . $this->getLine(). "<br />";
		$this->log->lfile('s_log.txt');
		$this->log->lwrite( "Error: " . $this->getMessage() . "File: " . $this->getFile() . "Line: " . $this->getLine());
		$this->log->lclose();
	}
}


class csvtodb
{
	public $log;
	public function __construct()
	{
		$this->log = new Logging();
	}
	
	public function city_blocks_fr()
	{
		$filename	=	"GeoLiteCity-Blocks.csv";
		$filehandle = 	fopen("./".$filename, "r");
		$datas 		=	array();
		$rec 		= 	array();
		$cnt		=	0;
		$errorchk	=	0;
		while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
		{
			 $cnt++;	 // Counter for total records
			 if($cnt>2 )  // Condition to avoid first 2 lines of csv fine
			 {
					$ab	=	implode(',', $data1);
					$data	=	explode(',',$ab);
					
					$startIpNum 	= (trim($data[0])!=' ')? mysql_real_escape_string(trim($data[0],'"')):'';
					$endIpNum 		= (trim($data[1])!=' ')? mysql_real_escape_string(trim($data[1],'"')):'';
					$locId		 	= (trim($data[2])!=' ')? mysql_real_escape_string(trim($data[2],'"')):'';
									
					$sqlInsert		=	"INSERT INTO `city_blocks_test` ( `startIpNum`,`endIpNum`, `locId`) VALUES ('$startIpNum','$endIpNum','$locId')";
					try {  
						if (mysql_query($sqlInsert) == '') 
						{
							$errorchk	=	1;	
							throw new MyException(mysql_error());
						}
					 
					} catch (MyException $e) {
					 
						 $e->getErrorReport();
					 
					}
					
					
				}
		}
		if($errorchk==1)
		{
			$this->log->lfile('s_log.txt');
			$this->log->lwrite( "End of import ".$filename);
			$this->log->lclose();
		}
	}
	
	public function  lat_lon()
	{
		$filename	=	"GeoLiteCity-Blocks.csv";
		$filehandle = 	fopen("./".$filename, "r");
		$datas 		=	array();
		$rec 		= 	array();
		$cnt		=	0;
		$errorchk	=	0;
		while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
		{
			 $cnt++;	 // Counter for total records
			 if($cnt>2 )  // Condition to avoid first 2 lines of csv fine
			 {
					$ab	=	implode(',', $data1);
					$data	=	explode(',',$ab);
					
					$lat 	= (trim($data[0])!=' ')? mysql_real_escape_string(trim($data[0],'"')):'';
					$lon 	= (trim($data[1])!=' ')? mysql_real_escape_string(trim($data[1],'"')):'';
					$col3	= (trim($data[2])!=' ')? mysql_real_escape_string(trim($data[2],'"')):'';
					$dept 	= (trim($data[3])!=' ')? mysql_real_escape_string(trim($data[3],'"')):'';
					$col5 	= (trim($data[4])!=' ')? mysql_real_escape_string(trim($data[4],'"')):'';
					$col6	= (trim($data[5])!=' ')? mysql_real_escape_string(trim($data[5],'"')):'';
					$col7 	= (trim($data[6])!=' ')? mysql_real_escape_string(trim($data[6],'"')):'';
					$col8 	= (trim($data[7])!=' ')? mysql_real_escape_string(trim($data[7],'"')):'';
					$col9	= (trim($data[8])!=' ')? mysql_real_escape_string(trim($data[8],'"')):'';
									
					$sqlInsert		=	"INSERT INTO `lat_lon` ( `lat`,`lon`, `col3`,`dept`,`col5`, `col6`,`col7`,`col8`, `col9`) VALUES ('$lat','$lon','$col3','$dept','$col5','$col6','$col7','$col8','$col9')";
					try {  
						if (mysql_query($sqlInsert) == '') 
						{
							$errorchk	=	1;	
							throw new MyException(mysql_error());
						}
					 
					} catch (MyException $e) {
					 
						 $e->getErrorReport();
					 
					}
					
					
				}
		}
		if($errorchk==1)
		{
			$this->log->lfile('s_log.txt');
			$this->log->lwrite( "End of import ".$filename);
			$this->log->lclose();
		}
	}
	
	
	public function city_location_fr()
	{
		$filename	=	"GeoLiteCity-Location.csv";
		$filehandle = 	fopen("./".$filename, "r");
		$datas 		= 	array();
		$rec 		= 	array();
		$cnt		=	0;
		$errorchk	=	0;
		while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
		{
			 $cnt++;	 // Counter for total records
			 if($cnt>2)  // Condition to avoid first 2 lines of csv fine
			 {
					$ab	=	implode(',', $data1);
					$data	=	explode(',',$ab);
					$id 		= (trim($data[0])!=' ')? trim(mysql_real_escape_string($data[0])):'';
					$country 	= (trim($data[1])!=' ')? mysql_real_escape_string(trim($data[1],'"')):'';
					$city 		= (trim($data[2])!=' ')? mysql_real_escape_string(trim($data[2],'"')):'';
					$region 	= (trim($data[3])!=' ')? mysql_real_escape_string(trim($data[3],'"')):'';
					$postalCode = (trim($data[4])!=' ')? mysql_real_escape_string(trim($data[4],'"')):'';
					$latitude 	= (trim($data[5])!=' ')? mysql_real_escape_string(trim($data[5],'"')):'';
					$longitude 	= (trim($data[6])!=' ')? mysql_real_escape_string(trim($data[6],'"')):'';
					$metroCode 	= (trim($data[7])!=' ')? mysql_real_escape_string(trim($data[7],'"')):'';
					$areaCode 	= (trim($data[8])!=' ')? mysql_real_escape_string(trim($data[8],'"')):'';
					
					$sqlInsert		=	"INSERT INTO `city_test`  ( `locId`,`country`, `region`, `city`, `postalCode`, `latitude`, `longitude`, `metroCode`, `areaCode`) VALUES ('','$country','$city','$region','$postalCode','$latitude','$longitude','$metroCode','$areaCode')";
					
					 
					try { 
					 
						if (mysql_query($sqlInsert) == '') 
						{
							$errorchk	=	1;
							throw new MyException(mysql_error());							
						}
					 
					} catch (MyException $e) {
					 
						 $e->getErrorReport();
					 
					}
					
					
				}
		}
		if($errorchk==1)
		{
			$this->log->lfile('s_log.txt');
			$this->log->lwrite( "End of import ".$filename);
			$this->log->lclose();
		}
	}
}

$obj = new csvtodb();
//echo $obj->city_blocks_fr();
//echo $obj->city_location_fr();

//************ Records For city_location_fr 

//echo city_location_fr();
function city_location_fr()
{
	$filehandle = fopen("./GeoLiteCity-Location.csv", "r");
	$datas 	= 	array();
	$rec 	= 	array();
	$cnt	=	0;
	while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
	{
		 $cnt++; // Counter for total records
		 if($cnt>2 && $cnt<10) // Condition to avoid first 2 lines of csv fine
		 {
				$ab	=	implode(',', $data1);
				$data	=	explode(',',$ab);
				$id 		= (trim($data[0])!=' ')? trim(mysql_real_escape_string($data[0])):'';
				$country 	= (trim($data[1])!=' ')? mysql_real_escape_string(trim($data[1],'"')):'';
				$city 		= (trim($data[2])!=' ')? mysql_real_escape_string(trim($data[2],'"')):'';
				$region 	= (trim($data[3])!=' ')? mysql_real_escape_string(trim($data[3],'"')):'';
				$postalCode = (trim($data[4])!=' ')? mysql_real_escape_string(trim($data[4],'"')):'';
				$latitude 	= (trim($data[5])!=' ')? mysql_real_escape_string(trim($data[5],'"')):'';
				$longitude 	= (trim($data[6])!=' ')? mysql_real_escape_string(trim($data[6],'"')):'';
				$metroCode 	= (trim($data[7])!=' ')? mysql_real_escape_string(trim($data[7],'"')):'';
				$areaCode 	= (trim($data[8])!=' ')? mysql_real_escape_string(trim($data[8],'"')):'';
				
				$sqlInsert		=	"INSERT INTOa `city_test`  ( `locId`,`country`, `region`, `city`, `postalCode`, `latitude`, `longitude`, `metroCode`, `areaCode`) VALUES ('','$country','$city','$region','$postalCode','$latitude','$longitude','$metroCode','$areaCode')";
	   
				//mysql_query($sqlInsert) or die(mysql_error());
				try { 
					 
						if (mysql_query($sqlInsert) == '') 
						{
							throw new MyException(mysql_error());
						}
					 
					} catch (MyException $e) {
					 
						 $e->getErrorReport();
					 
					}
			}
	}
	echo 'Total => '.$cnt .'records added.......';
}
exit;

?>
