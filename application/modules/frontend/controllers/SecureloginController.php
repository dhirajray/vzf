<?php
class SecureloginController extends IsloginController
{
    public function init()
    {
        parent::init();
        // Disable the main layout renderer
        $this->_helper->layout->disableLayout();
        // Do not even attempt to render a view
    }
     
    public function indexAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $auth =  Zend_Auth::getInstance();
        $this->session = $storage->read();
        $this->view->type = $type = $this->_request->getParam('type');
       
        if($auth->hasIdentity() && $type==''){
            $this->_helper->redirector->gotosimple('index', 'myhome', true);
        }
       
        $globalsecuretoken = $this->_request->getParam('token');
        $selctGlobalSetting = $this->Myhome_Model->getLoginToken();
        if($this->session['UserID'] !='' && $this->session['UserID']==adminID && $type!=''){ 
            if($type=='twitter')
                $this->_redirect('/twitter/admin');
            elseif ($type=='linkedin') 
                $this->_redirect('/social/adminlinkedin');
        }
        if($globalsecuretoken == $selctGlobalSetting && !empty($this->Adminresult))
        {
            $storage->write($this->Adminresult);
            $this->view->status = 'success';
        }else
            $this->_redirect('/');
    }
}