<?php
session_start();
/*echo "<pre>";
print_r($_SESSION);exit;*/
$path = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if($_SESSION['Zend_Auth']['storage']['UserID'] !='' && $_SESSION['Zend_Auth']['storage']['clientID'] !='')
{	

	$file = basename($path);         // $file is set to "index.php"
	

	$ext = end(explode('.', $file));

	$ext = substr(strrchr($filename, '.'), 1);
	$ext = substr($filename, strrpos($filename, '.') + 1);
	$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $filename);

	$exts = split("[/\\.]", $filename);
	$n    = count($exts)-1;
	$ext  = $exts[$n];

	$file = basename($path, ".".$ext);
	
	$filename = '../userpdf/'.str_replace('%20', ' ', $file);
	
	//echo filesize($filename);exit;
	//$filename = 'Custom file name for the.pdf'; /* Note: Always use .pdf at the end. */

	header('Content-type: image/'.$ext);
	header('Content-Disposition: inline; filename="' . $filename . '"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize($filename));
	header('Accept-Ranges: bytes');

	@readfile($filename);
}
else echo "<meta http-equiv='refresh' content='1;url=../' />";
