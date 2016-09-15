<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    

	protected function _initConfig()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
    	Zend_Registry::set('config', $config);
    }

    protected function _initTimeZone() {
        date_default_timezone_set('Europe/London');
    }
    
	
    
    protected function _initForceSSL() {
        if($_SERVER['SERVER_PORT'] != '443' && APPLICATION_ENV != 'local') {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }
    }


     protected function _initDB() {

        try {
            $config = Zend_Registry::get('config');
            $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
        } catch (Exception $e) {

            echo $e->getMessage();
            die;
        }

        $domain =  $_SERVER['SERVER_NAME'];

        $clientDtl = new Application_Model_Clientdetails();

        $clientData = $clientDtl->getfieldsfromtable(array('*'),'tblClient','clientDomain',$domain );
        
        if(count($clientData)==1)
        {
            defined('clientID') or define('clientID', $clientData[0]['clientID']);
            $ismobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos|iPod)/i", $_SERVER["HTTP_USER_AGENT"]);
            defined('ismobile') or define('ismobile', $ismobile);
            $domainAPI  = $clientDtl->getfieldsfromtable(array('*'),'domainAPI' );
            $domainVAR  = $clientDtl->getfieldsfromtable(array('*'),'domainVariables' ); 
            $adminData  = $clientDtl->getfieldsfromtable(array('UserID','Email'),'tblUsers','role','1' );  

             // Domain Constants will initialize here ******
            defined('URL') or define('URL',$domainVAR[0]['frontUrl']);
            defined('apikey') or define('apikey','513a219399839fbd5592e92a2684cd05');
            defined('api_password') or define('api_password','055386eacf18b3f98760816af216c1619e730928aa05896b9d4133adff497adf');
            defined('VIPUSER') or define('VIPUSER','VIP User');
            defined('VIPUSERPIC') or define('VIPUSERPIC','VIP.png');
            defined('HIDEUSER') or define('HIDEUSER','Anonymous VIP');
            defined('HIDEUSERPIC') or define('HIDEUSERPIC','VIP.png');  
            defined('adminURL') or define('adminURL', $domainVAR[0]['adminURL']);
            defined('BASE_URL_IMAGES') or define('BASE_URL_IMAGES', $domainVAR[0]['baseUrlImages']);
            defined('FRONT_URL') or define('FRONT_URL', $domainVAR[0]['emailUrl']);
            defined('BASE_URL') or define('BASE_URL', $domainVAR[0]['frontUrl']);
            defined('REMOTE_IP') or define('REMOTE_IP', $_SERVER['REMOTE_ADDR']);
            defined('SITE_NAME') or define('SITE_NAME', $domainVAR[0]['siteName']);
            defined('COMPANY_NAME') or define('COMPANY_NAME', $domainVAR[0]['companyName']);
            defined('POST_NAME') or define('POST_NAME', $domainVAR[0]['postName']);
            defined('FROM_MAIL') or define('FROM_MAIL', $domainVAR[0]['fromMail']);
            $fromName = ($domainVAR[0]['fromName']==NULL)?'db csp':$domainVAR[0]['fromName'];
            defined('FromName') or define('FromName',$fromName);
            defined('NOREPLY_MAIL') or define('NOREPLY_MAIL', $domainVAR[0]['noreplyEmail']);
            defined('COMPANY_FOOTERTEXT') or define("COMPANY_FOOTERTEXT","The ".COMPANY_NAME." team");
            defined('DEACTIVE_ALT') or define('DEACTIVE_ALT', $domainVAR[0]['deactiveAlt']);
            
            defined('ADMIN_EMAIL') or define('ADMIN_EMAIL', $adminData[0]['Email']);
            defined('adminID') or define('adminID', $adminData[0]['UserID']);
            defined('CLIENTFOLDER') or define('CLIENTFOLDER', 'adminraw/knowledge_center/client_'.clientID.'/');
            defined('IMGPATH') or define('IMGPATH', 'https://images.onserro.com/path_'.clientID);
            
            defined('AGCLIENTID') or define('AGCLIENTID', '19');
            
            defined('ABSIMGPATH') or define("ABSIMGPATH", "/home/db-csp.com/clientdev/cimg/path_".clientID."");
           
            $imadmin = $clientDtl->isRoleAdmin();

            defined('isADMIN') or define('isADMIN', $imadmin);

            defined('PAGE_NUM') or define('PAGE_NUM', $domainVAR[0]['pageNum']);
            defined('PAGE_NUM_LEAGE') or define('PAGE_NUM_LEAGE', $domainVAR[0]['pageNumLeague']);            
            defined('Front_Public_Path') or define('Front_Public_Path', $domainVAR[0]['frontpublicpath']);
           
            // Domain API will initialize here **********
            defined('SHORT_URL') or define('SHORT_URL', $domainAPI[0]['short_url_api']);
             //************ sematria start ***************
            defined('CONSUMER_KEY') or define('CONSUMER_KEY', $domainAPI[0]['sematriaKey']);
            defined('CONSUMER_SECRET') or define('CONSUMER_SECRET', $domainAPI[0]['semantriaSecret']);
            // Task statuses
            define('TASK_STATUS_UNDEFINED', 'UNDEFINED');
            define('TASK_STATUS_FAILED', 'FAILED');
            define('TASK_STATUS_QUEUED', 'QUEUED');
            define('TASK_STATUS_PROCESSED', 'PROCESSED');
            //************ sematria end ***************
            defined('facebookAppid') or define('facebookAppid', $domainAPI[0]['facebookAppid']);
            defined('clientType') or define('clientType', $domainAPI[0]['clientType']);
            defined('smtpkey') or define('smtpkey', $domainAPI[0]['smtpkey']);
            defined('facebookSecret') or define('facebookSecret', $domainAPI[0]['facebookSecret']);
            defined('facebookDomain') or define('facebookDomain', $domainAPI[0]['facebookDomain']);
            defined('linkedinAppid') or define('linkedinAppid', $domainAPI[0]['linkedinAppid']);
            defined('linkedinSecret') or define('linkedinSecret', $domainAPI[0]['linkedinSecret']);
            defined('twitterAppid') or define('twitterAppid', $domainAPI[0]['twitterAppid']);
            defined('twitterSecret') or define('twitterSecret', $domainAPI[0]['twitterSecret']);
            defined('twitterAccessToken') or define('twitterAccessToken', $domainAPI[0]['twitterAccessToken']);
            defined('twitterAccessSecret') or define('twitterAccessSecret', $domainAPI[0]['twitterAccessSecret']);
            defined('gplusClientappid') or define('gplusClientappid', $domainAPI[0]['gplusClientappid']);
            defined('gplusClientSecret') or define('gplusClientSecret', $domainAPI[0]['gplusClientSecret']);
            defined('gplusRedierctUrl') or define('gplusRedierctUrl', $domainAPI[0]['gplusRedierctUrl']);

            defined('GoogleEmail') or define('GoogleEmail', $domainAPI[0]['google_email']);
            defined('GooglePassword') or define('GooglePassword', $domainAPI[0]['google_password']);

            // youtube api 
            defined('youtubeapi') or define('youtubeapi', $domainAPI[0]['youtubeapi']);
            defined('youtubescret') or define('youtubescret', $domainAPI[0]['youtubescret']);
            defined('youtubeRedirect') or define('youtubeRedirect', $domainAPI[0]['youtubeRedirect']);
            defined('INVALID_DOMAIN') or define('INVALID_DOMAIN', '');
            require_once 'gplus/src/config.php';
            require_once 'gplus/src/apiClient.php';
            require_once 'gplus/src/contrib/apiPlusService.php';
            require_once 'gplus/src/contrib/apiOauth2Service.php';
            //print_r($domainAPI[0]); exit;

        }
        else
        {
            define('INVALID_DOMAIN', 'WRONG_URL');
        }
    }
   
   

	public function _initRouter( array $options = null ) {
		
		$frontControler = Zend_Controller_Front::getInstance();
	    $router = $frontControler->getRouter();

        if(clientID==AGCLIENTID)
        { 
            $router->addRoute("/", new Zend_Controller_Router_Route(
                            "/", array( 'module' => 'aglogin', 'controller' => 'index', 'action' => 'index' ) ) );
        }else{
            $router->addRoute("index", new Zend_Controller_Router_Route(
                            "/index", array( 'module' => 'frontend', 'controller' => 'index', 'action' => 'index' ) ) );
        }
        
		
		$router->addRoute("dbee/titles", new Zend_Controller_Router_Route(
				"dbee/:title", array( 'module' => 'frontend', 'controller' => 'dbeedetail', 'action' => 'home' ) ));
		
		$router->addRoute("user/username", new Zend_Controller_Router_Route(
				"/user/:username", array( 'module' => 'frontend', 'controller' => 'dashboarduser', 'action' => 'index' ) ));
		
		$router->addRoute("platformusers/viewall", new Zend_Controller_Router_Route(
				"/platformusers/viewall", array( 'module' => 'frontend', 'controller' => 'dashboarduser', 'action' => 'viewall' ) ));
		
		$router->addRoute("mainleague", new Zend_Controller_Router_Route(
				"/mainleague", array( 'module' => 'frontend', 'controller' => 'dbleagues', 'action' => 'index' ) ));
		
		$router->addRoute("user/id/:id", new Zend_Controller_Router_Route(
				"/user/id/:id", array( 'module' => 'frontend', 'controller' => 'dashboarduser', 'action' => 'index' ) ));
		
		$router->addRoute("secureadmin/login", new Zend_Controller_Router_Route(
				"/secureadmin/login", array( 'module' => 'adminlogin', 'controller' => 'index', 'action' => 'login' ) ));


        $router->addRoute("v1/registration", new Zend_Controller_Router_Route(
                "/v1/registration", array( 'module' => 'v1', 'controller' => 'index', 'action' => 'registration' ) ));


        $router->addRoute("admin/registration", new Zend_Controller_Router_Route(
                "/admin/registration", array( 'module' => 'adminlogin', 'controller' => 'index', 'action' => 'registration' ) ));

        $router->addRoute("admin", new Zend_Controller_Router_Route(
                "/admin", array( 'module' => 'admin', 'controller' => 'index', 'action' => 'index' ) ));
		
        
        $router->addRoute("admin/login", new Zend_Controller_Router_Route(
                "/admin/login", array( 'module' => 'adminlogin', 'controller' => 'index', 'action' => 'index' ) ));

        $router->addRoute("admin/password", new Zend_Controller_Router_Route(
                "/admin/forgotpassword", array( 'module' => 'adminlogin', 'controller' => 'index', 'action' => 'forgetpassword' ) ));
        
        $router->addRoute("admin/logout", new Zend_Controller_Router_Route(
                "/admin/logout", array( 'module' => 'admin', 'controller' => 'index', 'action' => 'logout' ) ));

        
	}

	protected function _initMisc() 
	{
        include_once APPLICATION_PATH . "/classes/IsController.php";  
        include_once APPLICATION_PATH . "/adminclasses/IsadminController.php";  
        include_once APPLICATION_PATH . "/adminclasses/RawEmailtemplate.php";  
        include_once APPLICATION_PATH . "/classes/IsloginController.php";
		require_once 'gplus/src/config.php';
		require_once 'gplus/src/apiClient.php';
		require_once 'gplus/src/contrib/apiPlusService.php';
		require_once 'gplus/src/contrib/apiOauth2Service.php';
    }
	
}

