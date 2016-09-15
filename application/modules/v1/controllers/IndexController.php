<?php
class V1_IndexController extends IsloginController
{  

	public function init()
	{
		parent::init();
	}	

	public function registrationAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$data = array('status'=>'error','message'=>'Something went wrong Please try after sometimes');
		if ($this->getRequest()->getMethod() == 'POST')
        {
        	$apikey = $this->_request->getPost('apikey');
        	$email = $this->_request->getPost('email');
        	$first_name = $this->_request->getPost('first_name');
        	$last_name = $this->_request->getPost('last_name');
        	$title = $this->_request->getPost('title');

        	if($apikey!=apikey)
        	{
        		$data['status'] = 'error';
        		$data['message'] = 'apikey does not match';
        	}
        	else if ($email=='') 
        	{
        		$data['status'] = 'error';
        		$data['message'] = 'Please enter your email';

        	}else if ($first_name=='') 
        	{
        		
        		$data['status'] = 'error';
        		$data['message'] = 'Please enter your first name';
        	}
        	else if ($last_name=='') 
        	{
        		
        		$data['status'] = 'error';
        		$data['message'] = 'Please enter your last name';
        	}
        	else if ($title=='') 
        	{
        		
        		$data['status'] = 'error';
        		$data['message'] = 'Please enter title name';
        	}
        	else
        	{
				switch (strtolower($title)) 
				{
					case 'mrs':
						$ProfilePic = 'default-avatar-female.jpg';
						$gender = 'Female';
						break;
					case 'mr':
						$ProfilePic = 'default-avatar.jpg';
						$gender = 'Male';
						break;
					default:
						$ProfilePic = 'default-profilepic-std.png';
						$gender = 'No Response';
						break;
				}

				$userModal    =   new Admin_Model_User();
				$encodedEmail = $this->myclientdetails->customEncoding($email);
				$encodedName = $this->myclientdetails->customEncoding($first_name);
				$last_name = $this->myclientdetails->customEncoding($last_name);
				$userData    =  $userModal->chkUsersExists($encodedEmail);
				
				if(count($userData)<1)
				{
					$spuname    =  explode('@', $email);
					$usname   = $spuname[0].rand(1000,9999);

					$dataval  = array(
						'clientID' => clientID,
						'ProfilePic' => $ProfilePic,
						'Name'=> $encodedName,
						'lname'=> $last_name,
						'full_name'=> $encodedName,
						'Email'=>$encodedEmail,
						'Gender'=>$gender,
						'Pass'=> '',
						'Username'=> $this->myclientdetails->customEncoding($usname),
						'Signuptoken'=>'',
						'RegistrationDate'=> date("Y-m-d H:i:s"),
						'Status' => 0,
						'emailsent'=>0,
						'fromcsv'=>0,
						'lastcsvrecord'=>0,
						'usertype'=>96,
					);
					$userModal->insertdata($dataval);
					$data['status'] = 'success';
        			$data['message'] = 'successfully added';
				}
			}
		}
		$this->jsonResponse(200,$data);
		
	}

	protected function jsonResponse($code,$data)
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            -> setHttpResponseCode($code)
            ->setBody(Zend_Json::encode($data))
            ->sendResponse();
            exit;
    }

}