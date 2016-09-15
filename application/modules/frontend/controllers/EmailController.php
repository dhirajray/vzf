<?php
class EmailController extends IsloginController
{
	public function init()
	{
		parent::init();
		$this->_helper->layout->setLayout('index');
	}
	public function notifyrequestAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$valid = new Zend_Validate_NotEmpty();
		if($this->getRequest()->getMethod()=='POST')
		{
			$this->activity = new Application_Model_Activities();
			$this->notification 	=	new Application_Model_Notification();
			$this->Expert_Model = new Application_Model_Expert();
			$dbeeid = (int)$this->_request->getPost('dbeeid');
			$expert_id = (int)$this->_request->getPost('expert_id');
			$loginUserId = (int)$this->_request->getPost('loginUserId');
			$type = $this->_request->getPost('type');
			$userIDArray = array();

			if($valid->isValid($dbeeid) && $valid->isValid($expert_id) && $type=='accept')
			{   
				$userArray = $this->activity->getalldbeeCommentUser($dbeeid);

				foreach ($userArray as $value) {
				 if($value['UserID']!=$loginUserId)
					$this->notification->commomInsert(11,16,$dbeeid,$expert_id,$value['UserID']); // Insert for involve activity 
					$userIDArray[] = $value['UserID'];
				}

				$userArray2 = $this->Expert_Model->getQuestionUser($dbeeid);

				foreach ($userArray2 as $value2) 
				{
					if(!in_array($value2['user_id'],$userIDArray) && $value2['UserID']!=$loginUserId)
						$this->notification->commomInsert(11,16,$dbeeid,$expert_id,$value2['user_id']); // Insert for involve activity 
				}
				
			}else if($valid->isValid($dbeeid) && $valid->isValid($expert_id) && $type=='reject'){

				$userArray = $this->activity->getalldbeeCommentUser($dbeeid);

				foreach ($userArray as $value) 
				{
					if($value['UserID']!=$loginUserId)
					{
					 $this->notification->commomInsert(11,17,$dbeeid,$expert_id,$value['UserID']); // Insert for involve activity
					 $userIDArray[] = $value['UserID'];
					}
				}

				$userArray2 = $this->Expert_Model->getQuestionUser($dbeeid);

				foreach ($userArray2 as $value2) 
				{
					if(!in_array($value2['user_id'],$userIDArray) && $value2['UserID']!=$loginUserId)
						$this->notification->commomInsert(11,17,$dbeeid,$expert_id,$value2['user_id']); // Insert for involve activity 
				}

			}
		}
	}

	public function activeemailAction()
	{
		$this->_helper->layout()->disableLayout();
		$request   = $this->getRequest()->getParams();
		$addEntry  = new Application_Model_DbUser();
		$filter = new Zend_Filter_StripTags();
		$chkUser  = $addEntry->chkAcountval2_user($filter->filter($request['id']));
		if ($request['email'] == 'activate' && !empty($chkUser[0]))
		{
				$storage = new Zend_Auth_Storage_Session();
				$data = $storage->read();
				if ($data['UserID'] != $chkUser[0]['UserID'])
				{
					if (isset($_SERVER['HTTP_COOKIE']))
					{
						$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
						foreach($cookies as $cookie) 
						{
							$parts = explode('=', $cookie);
							$name = trim($parts[0]);
							setcookie($name, '', time()-1000, '/','db-csp.com');
						}
					}
					if(isSet($_COOKIE['RememberEmail']) && $_COOKIE['RememberEmail']!="" && isSet($_COOKIE['Rememberpass']) && $_COOKIE['Rememberpass']!="")
					{
					    setcookie("RememberEmail","", time()- 3600, '/');
					    setcookie("Rememberpass","", time()- 3600, '/');
					}
					Zend_Auth::getInstance()->clearIdentity();
					session_destroy();
					session_unset();
				}
			 	$addEntry->activate_email($chkUser[0]['Email']);     
		        $EmailTemplateArray = array('Email' => $chkUser[0]['Email'],
                                            'Name' =>     $chkUser[0]['Name'],
                                            'lname' =>     $chkUser[0]['lname'],
                                            'case'=>16);
                $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
		        /****for email ****/
		        //print_r($chkUser); 
				$this->view->full_name  = $this->myclientdetails->customDecoding($chkUser[0]['full_name']);
				$this->view->Email  = $this->myclientdetails->customDecoding($chkUser[0]['Email']);
				$this->view->AlreadyActivated = 0;			
		}else
			$this->view->AlreadyActivated = 'your email address has been already activated';
			
	}
	
}