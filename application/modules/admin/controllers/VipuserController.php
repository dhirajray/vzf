<?php

class Admin_VipuserController extends IsadminController
{

	private $options;
	public $defaultimagecheck;
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $this->defaultimagecheck = new Admin_Model_Common();
        parent::init();
    }

    /**
     * Index
     */

    public function indexAction()
    {
        // @todo Add the home page action
 
		$namespace = new Zend_Session_Namespace();  
		
		unset( $_SESSION['Default']['searchfield']);
		$this->view->request = $request = $this->getRequest()->getParams();

		//print_r($request);

		//if( ( isset($request['searchfield']) && $request['searchfield']!='') || ( $_SESSION['Default']['searchfield']!='')) 
		if( ( isset($request['searchfield']) && $request['searchfield']!='') ) 
		{
			//$seachfieldChk = ($request['searchfield'] !='' ? $request['searchfield'] : $_SESSION['Default']['searchfield']);
			$seachfieldChk = ($request['searchfield'] !='' ? $request['searchfield'] : '');
			$namespace->searchfield 	= 	 $seachfieldChk;
			$this->view->seachfield	=	 $seachfieldChk; 

		}
		else { $seachfield = '';  }

		//if( ( isset($request['statussearch']) && $request['statussearch']!='') || ( $_SESSION['Default']['statussearch']!='')) 
		if( ( isset($request['statussearch']) && $request['statussearch']!='')) 
		{
			//$statussearchChk = ($request['statussearch'] !='' ? $request['statussearch'] : $_SESSION['Default']['statussearch']);
			$statussearchChk = ($request['statussearch'] !='' ? $request['statussearch'] : '');
			$namespace->statussearch 	= 	 $statussearchChk;
			$this->view->statussearch	=	 $statussearchChk; 

		}
		else { $statussearchChk = '';  }
		
		//if( (isset($request['orderfield']) && $request['orderfield']!='') || ( $_SESSION['Default']['orderfield']!='')) 
		if( (isset($request['orderfield']) && $request['orderfield']!='') ) 
		{
			//$orderfieldChk = ($request['orderfield'] !='' ? $request['orderfield'] : $_SESSION['Default']['orderfield']);
			$orderfieldChk = ($request['orderfield'] !='' ? $request['orderfield'] : '');
			$namespace->orderfield 	= 	$orderfieldChk;
			$this->view->orderfield	=	$orderfieldChk; 
		}
		else { $orderfield = ''; }
		
		$u= new Admin_Model_User();
		$seachkey = $this->myclientdetails->customEncoding($seachfieldChk,'usersearch');
		$userData = $u->getUsers('','vipuser',$orderfieldChk,$seachkey,$statussearchChk);
		$this->view->totUsers	=	 count($userData);
		$this->view->forcsvrec	=	 $userData->toarray();
	
		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		 */
		$paginator = Zend_Paginator::factory($userData);
		/*
		 * Set the number of counts in a page
		 */
		$paginator->setItemCountPerPage(20);
		/*
		 * Set the current page number
		 */
		$paginator->setCurrentPageNumber($page);
		/*
		 * Assign to view
		 */
		$usertype = new Admin_Model_Usertype();
		$common = new Admin_Model_Common();
		$utype = $usertype->getusertype();
		
		$this->view->getvipuserdropdown = $common->getvipuserdropdown($utype,2);		
		$this->view->paginator = $paginator;
		

    }
	
	public function adduserAction()
    {
        // @todo Add the menu page action
		$request = $this->getRequest()->getParams();
		$u= new Admin_Model_User();
		if(isset($request['contactinfo']) && $request['contactinfo']='submit'){
		   $this->view->beforesubmit = $request['contactinfo'];
		 $password = getrandmax();  
		 $dataval = array('Name'=> $request['Name'],'Email'=> $request['Email'],
		                  'Pass'=> $password,'RegistrationDate'=> date("Y-m-d H:i:s"));
		 $Insertdataadmin	=	$u->insertdata($dataval);
		 if(isset($Insertdataadmin) && $Insertdataadmin='1' ){
				$to = $request['Email'];
				$subject = "Test mail";
				$body = "Hello! This is a simple email message.";
				$from = "someonelse@example.com";
						$footerContentmsg = "powered by db corporate social platforms";			
		$dbeeEmailtemplate = new RawEmailtemplate();
        $emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplate();     

        $emailTemplatejson = $deshboard->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),
        	                 'adminemailtemplates','id',1);
		$bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
		$bodyContentjsonval = json_decode($bodyContentjson, true);
        $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]',
             '[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]',
             '[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
             '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]'
             );
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'], $bodyContentjsonval['bodycontenttxture'],
             $bodyContentjsonval['headerbacgroColor'],$bodyContentjsonval['headertxture'],
             $bodyContentjsonval['bannerfreshimg'],$bodyContentjsonval['contentbodyfontColor'],
             $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],
             $body,$bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],
             $bodyContentjsonval['footertxture'],$bodyContentjsonval['footerfontColor'],$footerContentmsg);
        $messagemail = str_replace($data1,$data2,$emailTemplatemain);
				
		$this->myclientdetails->sendWithoutSmtpMailsendWithoutSmtpMail($to,$subject,$from,$messagemail);
				echo "User added successfully and a confermation mail sent.";		 
		 }
		}
    }
	/**
     * Menu
     */
    public function menuAction()
    {
        // @todo Add the menu page action
    }
	
	public function invitefriendAction()
    {
		
		$request = $this->getRequest()->getParams();
		$u= new Admin_Model_User();
		$login = $request['Email'];
        $password =  $request['Password'];
		$resultarray = $u->getcontacts($login,$password);
    }
	
	public function inviteAction()
    {
		
    }

    public function vipgroupmembersAction()
    {
    	$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$deshboard    = new Admin_Model_Deshboard();
    	$request = $this->getRequest()->getParams();

    	$gmemdata	= $this->myclientdetails->getfieldsfromtable(array('DISTINCT(User)','Status'),'tblGroupMembers','GroupID',$request['uid']);
		
		$memids = '';
		$retarr = '';
		if(count($gmemdata)>0)
		{
			$retarr .= '<div id="userInfoContainer3">';
			foreach ($gmemdata as $key => $value) {
				$accpt = '';
				if($value['Status']==0) $accptGrp = "Invitation pending"; 
				//echo $value['User'];
				$usdata	= $this->myclientdetails->getfieldsfromtable(array('UserID','Name','Email','ProfilePic','Status'),'tblUsers','UserID',$value['User']);
				
				$retarr .= '<div socialfriendlist="true" title="'.$this->myclientdetails->customDecoding($usdata[0]['Name']).'" class="userFatchList boxFlowers">
						<a class="show_details_user" userid="'.$usdata[0]['UserID'].'" href="javascript:void(0)" style="font-weight:normal">
						
	                    
		                    <div class="follower-box">
		                   		<div class="usImg"><img border="0" align="left" src="'.IMGPATH.'/users/small/'.$usdata[0]['ProfilePic'].'"  class="img border"></div>
		                    <div class="oneline" style="margin-bottom:5px;">'.$this->myclientdetails->customDecoding($usdata[0]['Name']).'</div>';
		                   if($usdata[0]['Status']=='0') $retarr .= '<span  class="bx bx-gray"> Pending</span>';
		                   else  $retarr .= '<span class="bx bx-green"> Active</span>';
		                   $retarr .= '</div>
	                    </a> <div style="color:#ff0000; line-height:16px;">'.$accptGrp.'</div>
                    </div><div style="clear:both"></div>';
                $memids .=   $value['User'].',';  
			}
			$retarr .= '<input type="hidden" id="up_memid" value="'.trim($memids,',').'">';
			$retarr .= "</div>";
		}
		else
		{
			$retarr .= '<div id="userInfoContainer3" class="notfound">No members invited to this group!</div>';	
		}

		echo $retarr;
    }

    public function vipgroupsAction()
	{
		$request = $this->view->request= $this->getRequest()->getParams();
		$userpram = $request['uid'];
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';

		$deshboard= new Admin_Model_Deshboard();
		$liveGroupData = $deshboard->getLiveGroup('',$userpram,'VIPGROUP','4');


		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($liveGroupData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(20);
		/*
		 * Set the current page number
		*/
		$paginator->setCurrentPageNumber($page);
		/*
		 * Assign to view
		*/
			
		//echo "<pre>";
		//print_r($paginator);
		//die;
		$usertype = new Admin_Model_Usertype();
		$common = new Admin_Model_Common();
		$this->view->paginator = $paginator;
		$this->view->total = $paginator->getTotalItemCount();
		//$this->view->usertype = $usertype->getusertype();
//		$this->view->getvipuserdropdown = $common->getvipuserdropdown($usertype->getusertype(),1);
		$this->view->getvipuserdropdown = $common->getvipuserdropdown($usertype->getusertype(),2);
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();
	}

	public function createvipgroupAction()
	{
		$socialModal	=	new Admin_Model_Social();
		$common     	=	new Admin_Model_Common();
		$request = $this->getRequest()->getParams();
		
		$admId 		= $_SESSION['Zend_Auth']['storage']['UserID'];
		$success    =    0;
		$restrict 	=	($request['restrict']!='')?$request['restrict'] : '0';
		$expert 	=	($request['expert']!='') ?$request['expert'] : '0';
		$getType	=	($request['usertype']!='')?$request['usertype'] : '';

		$grpid		=	($request['grp_id']!='')?$request['grp_id'] : '';

		$alrmemuser = array();

		if($request['membersofgroup']!='')
		{
			$alrmemuser  =  explode(',', $request['membersofgroup']);
			 $memchkarr = array();
			foreach ($alrmemuser as $key => $value) {
				$memchkarr[] = $value;
			}
		}	
		if($_FILES['grouppicture']['error']=='0')
		{
			$ext = pathinfo($_FILES['grouppicture']['name'], PATHINFO_EXTENSION);
			$oploadgrouppicture	=	strtolower(time().'.'.$ext);	   		
	   		$copydone		=	copy($_FILES['grouppicture']['tmp_name'], './grouppics/' .$oploadgrouppicture);
		}
		else $oploadgrouppicture = $request['oldpicture'];
		if(empty($oploadgrouppicture)) $oploadgrouppicture ='default_pic.jpg';
		
		$groupdata = array('clientID'=>clientID, 'GroupName'=>$request['grp_name'],'GroupPic'=>$oploadgrouppicture,'GroupDesc'=>$request['grp_des'],'Invitetoexpert'=>$expert,'GroupRes'=>$restrict,'GroupPrivacy'=>'4','GroupDate'=>date('Y-m-d H:i:s'),'User'=>$admId); 
		if($grpid=='')	
		{
			$GroupID	=	$this->myclientdetails->insertdata_global('tblGroups',$groupdata);
			$socialModal->commomInsert(12,34,$GroupID,$admId,$admId,'',4);
			$action = "added";
			
			
		}
		else
		{
			$GroupUpdID	=	$this->myclientdetails->updatedata_global('tblGroups',$groupdata,'ID',$grpid);
			if($GroupUpdID) $GroupID 	=	$grpid;
			$action = "updated";
		} 	

		if($GroupID!='')
		{
			if(count($request['groupuser'])>0)
			{
				//if(count($alrmemuser)<1) $alrmemuser = $request['groupuser'];
				foreach ($request['groupuser'] as $key => $value) 
				{
					//print_r($alrmemuser);
					//echo $value.'<br>';
					if (!in_array($value, $alrmemuser)) 
					{
						//echo $value.'<br>';
						$mem_data = array('clientID'=>clientID,'GroupID'=>$GroupID,'Owner'=>$admId,'User'=>$value,'JoinDate'=>date('Y-m-d H:i:s'),'SentBy'=>'Owner');
						$GroupMemID	=	$this->deshboard->insertdata_global('tblGroupMembers',$mem_data);
						if($GroupMemID)
						{
							$notifData = array('clientID'=>clientID,'act_type'=>'12','act_message'=>'23','act_typeId'=>$GroupID,'act_userId'=>$value, 'act_ownerid'=>$admId,'act_date'=>date("Y-m-d H:i:s") );

				   			$return_noti = $this->myclientdetails->insertdata_global('tblactivity',$notifData);
				   			if($return_noti)
				   			{
				   				
				   				$usdata	= $this->myclientdetails->getfieldsfromtable(array('UserID','Name','Email'),'tblUsers','UserID',$value);
				                /****for email ****/ 
								//mann.delus@gmail.com,anildbee@gmail.com,porwal.deshbandhu@gmail.com       
								$EmailTemplateArray = array('uEmail' => $usdata[0]['Email'],
											                'uName'   => $usdata[0]['Name'],
											                'lname'   => $usdata[0]['lname'],
											                'request_grp_name' => $request['grp_name'],
											                'case'      => 36);

								$bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray); 
								/****for email ****/
						   		$success = 1;
						   	}	
						}
					}	
						
				}
				$this->_redirect('/admin/vipuser/vipgroups/response/'.$action);
				// if($success==1) $this->_redirect('/admin/vipuser/vipgroups/response/true/');
				// else $this->_redirect('/admin/vipuser/vipgroups/response/false');
			} 
			else if($_FILES['uploadvipcsv']['error']==0)
			{

				$csvuserarr = $this->common_model->uploadcsvOpration($_FILES['uploadvipcsv'],$admId,'VIP GROUP',$getType);
				if(count($alrmemuser)<1) $alrmemuser = $csvuserarr;
				//echo "<pre>";

				foreach ($csvuserarr as $sendkey => $sendEmailvalue) 
			   	{
			   		$userID = $sendEmailvalue['user_id'];
			   		if( $sendEmailvalue['user_id']=='' && $sendEmailvalue['uemail']!='')
			   		{
			   			$em = $this->myclientdetails->customEncoding($sendEmailvalue['uemail']);
			   			$que = ' select `UserID` from tblUsers where Email ="'.$em.'"';
			   			$avail	  =	 $this->myclientdetails->passsqlquery($que);
			   			//echo "<pre>"; print_r($avail);
			   			
			   			$userID = $avail[0]['UserID'];

			   		}
			   		
			   		if (!in_array($userID, $alrmemuser) && $userID!='' ) 
					{
				   		if(is_numeric($userID)!='' && ($sendEmailvalue['usertype']!='0' && $sendEmailvalue['usertype']!='6' && $sendEmailvalue['usertype']!='10' && $sendEmailvalue['usertype']!=''))
				   		{

				   			//echo $sendEmailvalue['usertype'].' # '.$sendEmailvalue['uname'].'<br>';
				   			$mem_data = array('clientID'=>clientID,'GroupID'=>$GroupID,'Owner'=>$admId,'User'=>$userID,'JoinDate'=>date('Y-m-d H:i:s'),'SentBy'=>'Owner');
				   			
				   			 $GroupMemID	=	$this->myclientdetails->insertdata_global('tblGroupMembers',$mem_data);

				   			$notifData = array('clientID'=>clientID,'act_type'=>'12','act_message'=>'18','act_typeId'=>$GroupID,'act_userId'=>$userID, 'act_ownerid'=>$admId,'act_date'=>date("Y-m-d H:i:s") );

				   			$return_noti = $this->myclientdetails->insertdata_global('tblactivity',$notifData);
				   			if($return_noti)
				   			{
					   			if($sendEmailvalue['token']!='')
						   		{

									/****for email ****/ 
									// added by deepak to send 2 emails  1. for activation account 2. for group notification
									
									$common->getemailtempate($sendEmailvalue['uname'],$sendEmailvalue['token'],$sendEmailvalue['uemail'],md5('group'));

									$EmailTemplateArray = array('uEmail' => $this->myclientdetails->customDecoding($sendEmailvalue['uemail']),
												                'uName'   => $this->myclientdetails->customDecoding($sendEmailvalue['uname']),
												                'request_grp_name' => $request['grp_name'],
												                'case'      => 36);
					                $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray); 

									 /* $EmailTemplateArray = array('uEmail'  => $sendEmailvalue['uemail'],
										                          'uName'   => $this->myclientdetails->customDecoding($sendEmailvalue['uname']),
										                          'sendEmailvalue_uemail'  => $sendEmailvalue['uemail'],
										                          'request_grp_name'  => $request['grp_name'],
										                          'sendEmailvalue_token'=>$sendEmailvalue['token'],
										                          'case'      => 7);
									  $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray); */
									/****for email ****/
						   		}
						   		else
						   		{					   				
									/****for email ****/ 
									  				$EmailTemplateArray = array('uEmail' => $this->myclientdetails->customDecoding($sendEmailvalue['uemail']),
																                'uName'   => $this->myclientdetails->customDecoding($sendEmailvalue['uname']),
																                'request_grp_name' => $request['grp_name'],
																                'case'      => 36);
									  $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray); 
									/****for email ****/
						   		}
						   		
						   	}
						}
						else
						{
							//$failedrec[] = array('uemail'=>$sendEmailvalue['uemail'],'uname'=>$sendEmailvalue['uname'],'lname'=>$sendEmailvalue['lname'],'company'=>$sendEmailvalue['company'],'jobtitle'=>$sendEmailvalue['jobtitle'],'usertype'=>'','status'=>$sendEmailvalue['status'],'position'=>'false');
						}	
						$success = 1;

			   		}

			   	}
			    //	echo "<pre>"; print_r($csvuserarr);
			   	echo $this->displayuploadedstatus($csvuserarr,'VIPCSV');
			   	//else echo $this->displayuploadedstatus('','ERROR');

			}
			else if($request['vipusersname']!='' && $request['vipusersemail']!='')
			{
				$getEmail	=	explode(",", $request['vipusersemail']);
			   	$getName	=	explode(",", $request['vipusersname']);
			 	$invusers   =   array();
			 	foreach ($getEmail as $key => $value) 
			   	{
			   		$ncodedEmail = $this->myclientdetails->customEncoding($value);
			   		if(count($alrmemuser)<1) $alrmemuser = $csvuserarr;
			  	 	$avail	  	=	 $this->deshboard->getfieldsfromtable(array('UserID','Name','Email','usertype'),'tblUsers','Email',$ncodedEmail);

			  	 	if(count($avail)<1)
			   	 	{
			   	 		$password 	= rand(10000,999999); 
			   	 		$spuname  	=  	split('@', $value);
				  	 	$spname 	= 	explode(' ', $getName[$key]);
				  	 	$uname 		=	$spuname[0].rand(1000,9999);
				  	 	$token 		=  	md5(rand(100000,999999));
		   	 			$dataval  = array(
		   	 						'clientID'=>clientID,
			   	 					'Name'=> $this->myclientdetails->customEncoding($getName[$key]),
			   	 					'full_name'=> $this->myclientdetails->customEncoding($getName[$key]),
			   	 					'Email'=>$ncodedEmail,
			   	 					'Username'=>$this->myclientdetails->customEncoding($uname),
			   	 					'usertype'=>$getType,
			   	 					'Pass'=> $this->_generateHash($password),
		   	 						'ProfilePic'=>'default-avatar.jpg',
			   	 					'Signuptoken'=>$token,
			   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
			   	 					'Status' => 0
			   	 					);

						$Insertdataadmin	=	$this->myclientdetails->insertdata_global('tblUsers',$dataval);
						$invusers[] = array('clientID'=>clientID,'user_id'=>$Insertdataadmin,'uemail'=>$ncodedEmail,'uname'=>$this->myclientdetails->customEncoding($getName[$key]),'token'=>$token,'password'=>$password);
		   	 		}
		   	 		else {
		   	 			$invusers[] = array('clientID'=>clientID,'user_id'=>$avail[0]['UserID'],'uemail'=>$avail[0]['Email'],'uname'=>$avail[0]['Name'],'usertype'=>$avail[0]['usertype']);
		   	 		}
			   	} 
			   	if(count($alrmemuser)<1) $alrmemuser = $invusers;
			  
			   	foreach ($invusers as $sendkey => $sendEmailvalue) 
			   	{
			   		
			   		/*echo "<pre>";
			   		print_r($alrmemuser);
			   		print_r($sendEmailvalue);

			   		exit;*/
			   		if (!in_array($sendEmailvalue['user_id'], $alrmemuser)) 
					{

				   		if($sendEmailvalue['user_id']!='' && ($sendEmailvalue['usertype']!='0' && $sendEmailvalue['usertype']!='6'))
				   		{
				   			/*echo "<pre>";
							print_r($alrmemuser);
					   		print_r($sendEmailvalue);*/				   		

					   		$mem_data = array('clientID'=>clientID,'GroupID'=>$GroupID,'Owner'=>$admId,'User'=>$sendEmailvalue['user_id'],'JoinDate'=>date('Y-m-d H:i:s'),'SentBy'=>'Owner');

					   		
							 $GroupMemID	=	$this->myclientdetails->insertdata_global('tblGroupMembers',$mem_data);

							$notifData = array('clientID'=>clientID,'act_type'=>'12','act_message'=>'18','act_typeId'=>$GroupID,'act_userId'=>$sendEmailvalue['user_id'], 'act_ownerid'=>$admId,'act_date'=>date("Y-m-d H:i:s") );

					   		$return_noti = $this->myclientdetails->insertdata_global('tblactivity',$notifData);
					   		if($return_noti)
					   		{
					   			//echo $sendEmailvalue['uname'];
						   		if($sendEmailvalue['token']!='')
						   		{
									/****for email ****/	

									// added by deepak to send 2 emails  1. for activation account 2. for group notification

									//$common->getemailtempate($sendEmailvalue['uname'],$sendEmailvalue['token'],$sendEmailvalue['uemail'],md5('group'));
									$common->getemailtempate($this->myclientdetails->customDecoding($sendEmailvalue['uname']),$sendEmailvalue['token'],$this->myclientdetails->customDecoding($sendEmailvalue['uemail']),md5('group'));
									
									$EmailTemplateArray = array('uEmail' => $sendEmailvalue['uemail'],
												                'uName'   => $sendEmailvalue['uname'],
												                'request_grp_name' => $request['grp_name'],
												                'case'      => '36');
					                $this->dbeeComparetemplateOne($EmailTemplateArray);			   		
						   		}
						   		else
						   		{
									/****for email ****/
					  				$EmailTemplateArray = array('uEmail' => $sendEmailvalue['uemail'],
												                'uName'   => $sendEmailvalue['uname'],
												                'request_grp_name' => $request['grp_name'],
												                'case'      => '36');
					                $this->dbeeComparetemplateOne($EmailTemplateArray); 
									/****for email ****/
						   		}

						   		$success = 1;
							}
						}
					}
			

			   		//$MailBody = $common->getemailtempate($getName[$key],$token,$value,$password);
			   	
			   	}

			   	echo $this->displayuploadedstatus($invusers,'VIPCSV');
			   	//else echo $this->displayuploadedstatus('','ERROR');
			 	
				//echo trim($already,',');
			}
			else
			{
				if($success==1) $this->_redirect('/admin/vipuser/vipgroups/response/'.$action);
				else $this->_redirect('/admin/vipuser/vipgroups/response/'.$action);	
			}
		}
		   
		
	}
	private function _generateHash($plainText, $salt = null)
	{
		define('SALT_LENGTH', 9);
		if ($salt === null) {
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		} else {
			$salt = substr($salt, 0, SALT_LENGTH);
		}

		return $salt . sha1($salt . $plainText);
	}
	public function displayuploadedstatus($responseArr,$calle)
	{
		
		if($calle=='VIPCSV')
		{
			$title 	=	"CSV Uploded Successfully";
		}
		else
		{
			$title 	=	"User Invited Successfully";
		}

		//echo $calle;
		$errCount = 0;
		$msgError ='';
		$backUrl = '';
		$retvar = '';
		$retvar .='<input type="hidden" id="chkrefererPage" value="'.$calle.'"> 
					<div id="csvupload" title="'.$title.'">';

		$backUrl = ''.BASE_URL.'/admin/vipuser/vipgroups';

		if($calle=='VIPCSV')
		{
			$vipreport = '';
			
			if($responseArr=='404')
			{
				$msgError = '<div class="message warning">File upload error: A maximum of 1000 records can be uploaded in one go.</div>';
			}
			else if(count($responseArr)>0)
			{
				$msgError = '<div class="message success">&#10004; VIP Group created successfully and invitation sent to all selected users.</div>';
			
				foreach ($responseArr as $key => $value) {
					if($value['user_id']=='' && ($value['usertype']=='' ) && ($value['uname']!='' ))
					{	
						$errCount++;
						$vipreport .= '<tr><td>'.$value['uname'].'</td><td>'.$value['lname'].'</td><td>'.$value['company'].'</td><td>'.$value['jobtitle'].'</td><td>'.$value['uemail'].'</td><td>'.$value['status'].'</td><td><span class="greenTxt">Plateform Users or invalid user type</span></td></tr>';		
					}
					/*elseif($value['error']=='true')
					{
						$errCount++;
						$vipreport .= '<span style="float:left; padding:0px;">'.$value['msg'].'</span><br><br>';
					} */
				};
				//$vipreport .= "</div>";
			}
			else
			{
				$msgError = '<div class="message success">&#10004; VIP Group saved successfully and invitation sent to all selected users.</div>';
			}
		}
				
		$retvar .='</div>
					<div class="">
						<a href="'.$backUrl.'" class="btn btn-black pull-right">Back</a>
					</div>
					<div class="msgInviteWrp"> '. $msgError .'</div>';

		if($errCount>0 ) {
			$retvar .='<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe">
				<thead> 
					<tr>
						<td colspan="7"><div class="message error">NOTE: Failed to send invitation to following non-platform users. These users may already have a platform account or the email address is incorrect.</div></td>
					</tr>
					<tr>
						<td>Fname</td>
						<td>Lname</td>
						<td>Company</td>
						<td>Job-title</td>
						<td>Email</td>
						<td>Status</td>
						<td>Info</td>
					</tr>
				</thead>
				<tbody>'. $vipreport.'</tbody>
			</table></div>';
		}
		return $retvar;			
	}
	
}

