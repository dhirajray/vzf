<?php
class GoogleauthController extends IsController
{
    protected $profileTable;
    protected $_userid = null;
    protected $dbeeid;
    protected $myhome_obj;
    protected $dbeeCommentobj;
    protected $commonmodel_obj;
    protected $profile_obj;
    
    public function init()
    {
        parent::init();
        $this->dbeeid = (int) $this->_getParam('id');
        
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) 
        {
            $storage = new Zend_Auth_Storage_Session();
            $data               = $storage->read();
            $this->_userid      = $data['UserID'];
            $this->session_data = $data;
        }
      
        
        
    }

    public function indexAction()
    {
       

        $client = new apiClient();
        $client->setApplicationName("Dbee Login Application");
        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/plus.me'));
        //$domainapidata  = $clientDtl->getfieldsfromtable(array('*'),'domainVariables' ); 

        $client->setClientId('601662717833-i78ohlghnpft16viuu3j77m1u62gl1b2.apps.googleusercontent.com');
        $client->setClientSecret('dDeiWn94VLti_NFtLpgrD3Y2');
        $client->setRedirectUri(FRONT_URL.'/googleauth');
        $plus = new apiPlusService($client);
        $plus2 = new apiOauth2Service($client);
        
        if (isset($_GET['code']))
        {
            $client->authenticate();
            $this->user_session->access_token_gplus = $client->getAccessToken(); 
            $user = $plus2->userinfo->get();
            $authNamespace = new Zend_Session_Namespace('Google_Auth_Dburl');           

            //$GoogelAuthNamespace = new Zend_Session_Namespace('Google_Auth_Email');
            //$GoogelAuthNamespace->Auth_Email = $user['email'];                   
            $this->_redirect("/dbee/".$authNamespace->Auth_Dburl."?authparam=".md5($user['email']));
        }  
             

    }
    
        
}
