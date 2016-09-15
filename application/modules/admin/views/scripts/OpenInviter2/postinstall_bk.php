<?php
set_time_limit(0);
$rewrite_config=false;
include('config.php');

function row2text($row)
	{
	$text='';
	$flag=0;
	$i=0;
	foreach ($row as $var=>$val)
		{
		if($flag==1)
			$text.=", ";
		elseif($flag==2)
			$text.=",\n";
		$flag=1;
		//Variable
		if(is_numeric($var))
			if($var{0}=='0')
				$text.="'$var'=>";
			else
				{
				if($var!==$i)
					$text.="$var=>";
				$i=$var;
				}
		else
			$text.="'$var'=>";
		$i++;
		//letter
		if(is_array($val))
			{
			$text.="array(".row2text($val).")";
			$flag=2;
			}
		else
			$text.="\"$val\"";
		}
	return($text);
	}

//Check username and private key
echo "Checking username and private key... ";
if (empty($openinviter_settings['username']) OR empty($openinviter_settings['private_key']))
	{
	echo "Username or private key missing.Get your own at <a href='http://openinviter.com/register.php'>OpenInviter</a><br>\n";
	exit;
	}
else echo "<b>OK</b><br>\n";

//Check PHP version
echo "Checking PHP version... ";
if (version_compare(PHP_VERSION, '5.0.0', '<')) { echo "<b>NOT OK</b> - OpenInviter requires PHP5, your server has PHP ".PHP_VERSION." installed";exit; }
else echo "<b>OK</b><br>\n";

//Check support for DOMDocument
echo "Checking DOMDocument support... ";
if (!extension_loaded('dom') OR !class_exists('DOMDocument')) { echo "<b>NOT OK</b> - OpenInviter will not run correctly on this system.";exit; }
else echo "<b>OK</b><br>\n";

//Check transport type
$transport='curl';
echo "Checking transport method... ";
if (!extension_loaded('curl') OR !function_exists('curl_init'))
	{
	$transport='wget';
	passthru("wget --version",$return_var);
	if ($return_var!=0)
		{
		echo "Neither <b>libcurl</b> nor <b>wget</b> is installed.<br>\nYou will not be able to use OpenInviter.";
		exit;
		}
	else echo "<b>wget</b> is installed. Using <b>Wget</b> to handle requests<br>\n";
	}
else echo "<b>libcurl</b> is installed. Using <b>cURL</b> to handle requests<br>\n";
if ($openinviter_settings['transport']!=$transport) { $rewrite_config=true;$openinviter_settings['transport']=$transport; }

//Check permisions
$cookie_path='/tmp';
echo "Checking write permisions... ";
if (!is_writable("{$cookie_path}"))
	{
	$cookie_path = session_save_path();
	if (strpos ($cookie_path, ";") !== FALSE)
		$cookie_path = substr ($cookie_path, strpos ($cookie_path, ";")+1);
	if (empty($cookie_path)) $cookie_path='/tmp';
	if (!is_writable("{$cookie_path}"))
		{
		echo "The <b>{$cookie_path}</b> folder is not writable. You will have to manually define a location for logs and temporary files in <b>config.php</b><br>\n";
		exit;
		}
	else echo "<b>{$cookie_path}</b> is writable. Using <b>{$cookie_path}</b> to store cookie files and logs<br>\n";
	}
else echo "<b>{$cookie_path}</b> is writable. Using <b>{$cookie_path}</b> to store cookie files and logs<br>\n";
if ($openinviter_settings['cookie_path']!=$cookie_path) { $rewrite_config=true;$openinviter_settings['cookie_path']=$cookie_path; }

//Write new config file if required
if ($rewrite_config)
	{
	$file_contents="<?php\n";
	$file_contents.="\$openinviter_settings=array(\n".row2text($openinviter_settings).");\n";
	$file_contents.="?>";
	file_put_contents('config.php',$file_contents);
	}

//Instantiate OpenInviter
include('openinviter.php');
$inviter=new OpenInviter();

class PostInstall extends OpenInviter_Base
	{
	public function login($user,$pass)
		{
		return;
		}
	public function getMyContacts()
		{
		return;
		}
	public function logout()
		{
		return;
		}
	public function checkVersion()
		{
		$this->init();
		$res=$this->get('http://update.openinviter.com/updater/check_version.php');
		$this->stopPlugin();
		return $res;
		}
	public function check($url)
		{
		$this->init();
		$res=$this->get($url);
		$this->stopPlugin();
		if (empty($res)) return false; else return true;
		}
	}

$checker=new PostInstall();
$checker->settings=$inviter->settings;
$checker->service_user='postInstall';
$checker->service_pass='postInstall';
$checker->service='postInstall';

//Check version
echo "Checking for new versions of OpenInviter... ";
$xml=$checker->checkVersion();
libxml_use_internal_errors(true);
$parsed_xml=simplexml_load_string($xml);
libxml_use_internal_errors(false);
if (!$parsed_xml)
	echo "Could not connect to server<br>\n";
else
	{
	$server_version=(string)$parsed_xml;
	$version=$inviter->getVersion();
	if (!$inviter->checkVersion($server_version)) echo "You are using OpenInviter <b>{$version}</b> but version <b>{$server_version}</b> is available for download - <a href='http://openinviter.com/download.php'>Download newer version</a><br>\n";
	else echo "Your OpenInviter software is up-to-date<br>\n";
	}

//Check plugins
$plugins=$inviter->getPlugins();
foreach ($plugins as $type=>$dummy)
	foreach ($dummy as $plugin=>$details)
		{
		echo "Checking {$details['name']}... ";
		if ($checker->check($details['check_url'])) echo "<b>OK</b><br>\n"; else echo "<b>NOT OK</b> - This plugin might not work correctly on your system<br>\n";
		}
?>