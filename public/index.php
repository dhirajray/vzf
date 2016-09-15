<?php
/*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
ini_set('error_log','script_errors.log');
ini_set('log_errors','On');*/
// Define base path obtainable throughout the whole application
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');
defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__)));   

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment

if ($_SERVER['SERVER_NAME'] == 'localhost') 
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local'));
 else
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

error_reporting(1);


// Define ZF Library path
define('ZEND_LIBRARY_PATH', APPLICATION_PATH . '/../library');
define('CACHE_FRONTEND_OPTIONS', serialize(array('automatic_cleaning_factor' => 0)));
define('APP_LIBRARY_PATH', '/path/ZendFramework-1.12.1/library');
//echo'<pre>';print_r(clientID);die;
$paths = array(
	ZEND_LIBRARY_PATH,
	APP_LIBRARY_PATH,
	get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $paths)); 

/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance ();

require_once 'twitter/twitteroauth.php';
require_once 'facebook/facebook.php';
require_once 'Linkedin/linkedin.php';
require_once 'geoplugin.class/geoplugin.class.php';
require_once 'googlecustomsearch/src/Google/CustomSearch.php';
require_once 'semantria/authrequest.php';
require_once 'semantria/common.php';
require_once 'semantria/jsonserializer.php';
require_once 'semantria/session.php';
require_once 'sms/Easysmsv2.php';
require_once 'PHPMailer-master/PHPMailerAutoload.php';


// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
