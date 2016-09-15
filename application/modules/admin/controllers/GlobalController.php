<?php
    /**
	 * 
	 * @ admin global setting
	 */
class Login_GlobalController extends IsadminController
{
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
       parent::init();
    }
    public function loginAction()
    {
       $token = $this->getRequest()->getParam('token');
       $selctGlobalSetting = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings',array('*'));
       if($token == $selctGlobalSetting['logout_token'])
       {
          die('done');
       }else
       {
          $this->_redirect('/');
       }
    }
}
