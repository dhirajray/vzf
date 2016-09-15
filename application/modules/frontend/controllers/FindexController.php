<?php
class FindexController extends IsController
{  
	public function init()
	{
		parent::init();
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->setLayout('index');
		//print_r($this->shortUrl('https://web.whatsapp.com/')); die;
		
	}
	
	public function indexAction()
	{
		
		$user_session = new Zend_Session_Namespace('User_Session');
		$flash  = $this->_helper->getHelper('flashMessenger');
		$this->view->form_status   = $user_session->form_status;
		if ($flash->hasMessages()) {
			$this->view->message = $flash->getMessages(); // set flash error message
		}
		if($user_session->afterSignup)
		{
			$this->view->afterSignup = $user_session->afterSignup;
			$user_session->afterSignup = '';
		}
	}
}