<?php
class FacebookController extends IsController
{
    
    public function init()
    {
        parent::init();
        $storage = new Zend_Auth_Storage_Session();
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data          = $storage->read();
            $this->data = $data;
            $this->_userid = $data['UserID'];
            $this->user_session = $this->session_name_space;
        } else {
            $this->_helper->redirector->gotosimple('index', 'index', true);
        }
    }
    public function facebookfriendlistAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $storage = new Zend_Auth_Storage_Session();
        if($this->getRequest()->isXmlHttpRequest())
        {
            
            $id = $this->facebook_connect_data['facebookid'];
            
            $params = array(
                'appId' => facebookAppid,
                'secret' => $this->_config->facebook->secret,
                'domain' => $this->_config->facebook->domain
            );
            
            $facebook       = new Facebook($params);
            $getAccessToken = $this->facebook_connect_data['access_token'];
            
            try {
                $result = $facebook->api('/'.$id.'/friends', 'GET', array());
            }
            catch (FacebookApiException $e) {
                echo $e->getType();
                echo $e->getMessage();
                exit;
            }
            $data['status']  = 'success';
            $data['content'] = $result;
        }
        else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
}
