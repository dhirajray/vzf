<?php

class Admin_ManagerolesController extends IsadminController
{
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $this->defaultimagecheck = new Admin_Model_Common();
        $this->userModal	=	new Admin_Model_User();
        parent::init();
    }  

    public function deletroleuserAction()
    {
        $data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$userModal	=	new Admin_Model_User();
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$UserID = (int) $this->_request->getPost('UserID');
			$roleid = (int) $this->_request->getPost('role');
            /*$this->userModal->delUserRoletable($roleid);*/
			$role =3;
			$notifData = array('clientID'=>clientID,'act_type'=>'38','act_message'=>'42','act_userId'=>adminID,'act_ownerid'=>$UserID,'act_status'=>0,'act_date'=>date("Y-m-d H:i:s") );
       		$return_noti = $this->myclientdetails->insertdata_global('tblactivity',$notifData);
       		$userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$UserID,'clientID',clientID);
       		/******************for mails***************/
						/*$fname = $this->myclientdetails->customDecoding($userresult[0]['Name']);
						$username = $this->myclientdetails->customDecoding($userresult[0]['Username']);
						$loginid = $this->myclientdetails->customDecoding($userresult[0]['Email']);
						$dbeeEmailtemplate = new RawEmailtemplate();
						$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
						$deshboard   = new Admin_Model_Deshboard();
						$areatype ='admin';
						$bodyContent = $deshboard->getGroupemailtemplaterole($areatype);
						$bodyContentmsg ='<tr>
												<td style="padding:0px 30px 30px 30px; "><br />
												<strong>Dear '.$username.'</strong>,<br /> <br /><br />
                                                       Your access to the administration panel has now been removed
												<br /><br />
												<br/><br/>
												'.COMPANY_FOOTERTEXT.'
												</td>
											</tr>';

						$datasub1 = array('[%%COMPANY_NAME%%]');
						$datasub2 = array(COMPANY_NAME);
						$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
						$footerContentmsg = $bodyContent[0]['footertext'];
						$data1 = array('[%%body%%]','[%%footertext%%]');
						//$data2 = array($bodyContentmsg,$footerContentmsg);
						$data2 = $this->dbeeComparetemplate($bodyContentmsg,$footerContentmsg);
						$from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
						$replyto = NOREPLY_MAIL;
						$to = $loginid; 
						$setSubject = $subjectMsg;
						$setBodyText = html_entity_decode($data2);
						$this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);*/
						$fname = $this->myclientdetails->customDecoding($userresult[0]['Name']);
				    	$username=$this->myclientdetails->customDecoding($userresult[0]['Username']);
				    	$loginid = $this->myclientdetails->customDecoding($userresult[0]['Email']);
				    	$dbeeEmailtemplate = new RawEmailtemplate();
				    	$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
				    	$deshboard   = new Admin_Model_Deshboard();
				    	$areatype ='admin';
				    	$bodyContent = $deshboard->getGroupemailtemplateroledel($areatype);
				        $databodymsg1 = array('[%%fname%%]','[%%COMPANY_FOOTERTEXT%%]');
				        $databodymsg2 = array($fname,COMPANY_FOOTERTEXT); 
				        $bodyContentmsg = str_replace($databodymsg1,$databodymsg2,$bodyContent[0]['body']);	
						$datasub1 = array('[%%COMPANY_NAME%%]');
				        $datasub2 = array(COMPANY_NAME);
				        $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
				        $footerContentmsg = $bodyContent[0]['footertext'];
				        $data1 = array('[%%body%%]','[%%footertext%%]');
				        $bodyContentmsg = html_entity_decode($bodyContentmsg);
				        //$data2 = array($bodyContentmsg,$footerContentmsg);
				        $data2 = $this->dbeeComparetemplate($bodyContentmsg,$footerContentmsg);
				        $from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
				        $replyto = NOREPLY_MAIL;
				        $to = $loginid; 
				        $setSubject = $subjectMsg;
				        $setBodyText = html_entity_decode($data2);
				        $this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);
                /*******************for mails**************/
       		$result = $this->userModal->delUserRole($UserID,$role);
	    }
	    echo $result;die;
    }

    public function deletroleuserallAction()
    {
        $data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$userModal	=	new Admin_Model_User();
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$role = (int) $this->_request->getPost('roleid');
			$userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','role',$role,'clientID',clientID);
			$Retuserid = $this->userModal->delUserRoleall($role);		
			foreach($userresult as $value){
                $notifData = array('clientID'=>clientID,'act_type'=>'38','act_message'=>'42','act_userId'=>adminID,'act_ownerid'=>$value['UserID'],'act_status'=>0,'act_date'=>date("Y-m-d H:i:s") );
       		    $return_noti = $this->myclientdetails->insertdata_global('tblactivity',$notifData);
                /******************for mails***************/
						/*$fname = $this->myclientdetails->customDecoding($value['Name']);
						$username = $this->myclientdetails->customDecoding($value['Username']);
						$loginid = $this->myclientdetails->customDecoding($value['Email']);
						$dbeeEmailtemplate = new RawEmailtemplate();
						$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
						$deshboard   = new Admin_Model_Deshboard();
						$areatype ='admin';
						$bodyContent = $deshboard->getGroupemailtemplaterole($areatype);
						$bodyContentmsg ='<tr>
												<td style="padding:0px 30px 30px 30px; "><br />
												<strong>Dear '.$username.'</strong>,<br /> <br /><br />
                                                       Your access to the administration panel has now been removed
												<br /><br />
												<br/><br/>
												'.COMPANY_FOOTERTEXT.'
												</td>
											</tr>';

						$datasub1 = array('[%%COMPANY_NAME%%]');
						$datasub2 = array(COMPANY_NAME);
						$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
						$footerContentmsg = $bodyContent[0]['footertext'];
						$data1 = array('[%%body%%]','[%%footertext%%]');
						//$data2 = array($bodyContentmsg,$footerContentmsg);
						$data2 = $this->dbeeComparetemplate($bodyContentmsg,$footerContentmsg);
						$from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
						$replyto = NOREPLY_MAIL;
						$to = $loginid; 
						$setSubject = $subjectMsg;
						$setBodyText = html_entity_decode($data2);
						$this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);*/
						$fname = $this->myclientdetails->customDecoding($value['Name']);
				    	$username=$this->myclientdetails->customDecoding($value['Username']);
				    	$loginid = $this->myclientdetails->customDecoding($value['Email']);
				    	$dbeeEmailtemplate = new RawEmailtemplate();
				    	$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
				    	$deshboard   = new Admin_Model_Deshboard();
				    	$areatype ='admin';
				    	$bodyContent = $deshboard->getGroupemailtemplateroledel($areatype);
				        $databodymsg1 = array('[%%fname%%]','[%%COMPANY_FOOTERTEXT%%]');
				        $databodymsg2 = array($fname,COMPANY_FOOTERTEXT); 
				        $bodyContentmsg = str_replace($databodymsg1,$databodymsg2,$bodyContent[0]['body']);	
						$datasub1 = array('[%%COMPANY_NAME%%]');
				        $datasub2 = array(COMPANY_NAME);
				        $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
				        $footerContentmsg = $bodyContent[0]['footertext'];
				        $data1 = array('[%%body%%]','[%%footertext%%]');
				        $bodyContentmsg = html_entity_decode($bodyContentmsg);
				        //$data2 = array($bodyContentmsg,$footerContentmsg);
				        $data2 = $this->dbeeComparetemplate($bodyContentmsg,$footerContentmsg);
				        $from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
				        $replyto = NOREPLY_MAIL;
				        $to = $loginid; 
				        $setSubject = $subjectMsg;
				        $setBodyText = html_entity_decode($data2);
				        $this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);
                /*******************for mails**************/
			}
			$this->userModal->delUserRoletable($role);
			$result = $this->userModal->deletroleuserall($role);		
	    }
	    echo $result;die;
    }	

    public function indexAction()
    {
		$namespace = new Zend_Session_Namespace();  		
		unset( $_SESSION['Default']['searchfield']);
		$this->view->request = $request = $this->getRequest()->getParams();
		if( ( isset($request['searchfield']) && $request['searchfield']!='') || ( $_SESSION['Default']['searchfield']!='')) 
		{
			$seachfieldChk = ($request['searchfield'] !='' ? $request['searchfield'] : $_SESSION['Default']['searchfield']);
			$namespace->searchfield 	= 	 $seachfieldChk;
			$this->view->seachfield	=	 $seachfieldChk; 

		}
		else { $seachfieldChk = '';  }		
		if( ( isset($request['statussearch']) && $request['statussearch']!='') || ( $_SESSION['Default']['statussearch']!='')) 
		{
			$statussearchChk = ($request['statussearch'] !='' ? $request['statussearch'] : $_SESSION['Default']['statussearch']);
			$namespace->statussearch 	= 	 $statussearchChk;
			$this->view->statussearch	=	 $statussearchChk; 
		}
		else { $statussearchChk = '';  }				
		if( (isset($request['orderfield']) && $request['orderfield']!='') || ( $_SESSION['Default']['orderfield']!='')) 
		{
			$orderfieldChk = ($request['orderfield'] !='' ? $request['orderfield'] : $_SESSION['Default']['orderfield']);
			$namespace->orderfield 	= 	$orderfieldChk;
			$this->view->orderfield	=	$orderfieldChk; 
		}
		else { $orderfield = ''; }			
		$u= new Admin_Model_User();
		$seachkey = $this->myclientdetails->customEncoding($seachfieldChk,'usersearch');
		$userData = $u->getusersRolslist();
		$this->view->totUsers	=	 count($userData);
		$this->view->forcsvrec	=	 $userData->toarray();
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($userData);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;		
    }

    public function rolesresourceAction(){
		$namespace = new Zend_Session_Namespace();  		
		unset( $_SESSION['Default']['searchfield']);
		$this->view->request = $request = $this->getRequest()->getParams();
		$u= new Admin_Model_User();
		//$seachkey = $this->myclientdetails->customEncoding($seachfieldChk,'usersearch');
		$roleData = $u->getselRols();
		$this->view->totRoles	=	 count($roleData);
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($roleData);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;	
    }

    public function  manageroleresourceAction(){
        $namespace = new Zend_Session_Namespace();  		
		unset( $_SESSION['Default']['searchfield']);
		$this->view->request = $request = $this->getRequest()->getParams();
		//echo'<pre>';print_r($request);die('----');
		$u= new Admin_Model_User();
		$roleData = $u->getselRols();
		$this->view->Roleid = $request['Roleid'];
		$roleData = $u->getselRols();
        //echo'<pre>';print_r($roleData);die('----');
        foreach($roleData as $roledataval){
        	//echo'<pre>';print_r($roledataval);
        	if($roledataval['role_id']==$request['Roleid']){
                $this->view->Rolename = $roledataval['role'];
        	}
        	
        }
		//die('----');

	    $resourceData = $u->getselResource();
	    //echo'<pre>';print_r($resourceData);die('----');
		$this->view->totResources	=	 count($resourceData);
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($resourceData);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;	
    }

    public function addroleAction(){
    	$request = $this->getRequest()->getParams();
    	$userModal = new Admin_Model_User();
    	//echo'<pre>';print_r($request);die;
    	$avail = $userModal->chkRolesExist($request['role']);
    	if($avail<1){
             $inserval = $userModal->insertRoles($request['role']);
             echo $inserval;die;
		}else{
          echo $return="Role avaliable";exit;
		}	
    }

    public function getrolesubresourceAction(){
    	$request = $this->getRequest()->getParams();
    	//echo'<pre>';print_r($request);die;
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    	$userModal = new Admin_Model_User();
    	$getsubres = $userModal->getResourcecat($request['roleid'],$request['resourceid'],$request['parentid']);
    	//echo'<pre>';print_r($getsubres);
    	//$getpermission = $userModal->getPermissions($request['roleid']);
    	//echo'<pre>';print_r($getpermission);
          /*$content.="<select name='rools' id='subresource'>";
	      foreach ($getsubres as $subresouval):
	         $content .= "<option value=".$subresouval['resource']." 
	         name='selectrols' subresourceid=".$subresouval['id'].">
	                      ".$subresouval['resource']."
	                    </option>";
	      endforeach;
	      $content.="</select>";*/
	      //$delid = '.deletecheck';
          $content .='<ul class="globval" id="globval" style="display:none">';
        /*  $content .='<label title="Select all"> 
						<input type="checkbox" id="selectall" roleid="'.$request['roleid'].'" resourceid="'.$request['resourceid'].'" class="selectallval">
						     <a class="btn btn-green btn-mini">
							   <i class="fa fa-eye-open"></i>
									 Select All
							  </a>						     
                            <label for="tlallresult"></label>
					</label>';*/
	      foreach ($getsubres as $subresouval):
            $getpermission = $userModal->getPermissions($request['roleid'],$subresouval['resmangid'] );
            if(!empty($getpermission)){$mycheck='checked';}else{$mycheck='ina';}	  
	        $content .='<li class="deletecheck"><label>
	  					<input '.$mycheck.' class="selectsunresource" id="resourcebox_'.$subresouval['resmangid'].'" type="checkbox" value="'.$subresouval['resmangid'].'~asr~'.$request['resourceid'].'" resourceidval="'.$request['resourceid'].'" subresourceid="'.$subresouval['resmangid'].'" roleid="'.$request['roleid'].'" name="deleteall[]">
	  					<label for="resourcebox_'.$subresouval['resmangid'].'"></label> '.$subresouval["resmangresource"].'
	  				</label></li>';	
	  			   /* }*/	  		 		
	      endforeach;
	       $content .='</ul>';
	       echo $content;
    }	

    public function addroleresourceAction(){
    	$request = $this->getRequest()->getParams();
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    	$userModal = new Admin_Model_User();
    	//echo'<pre>';print_r($request);
    	$updateinsert = $userModal->updateinsertSubresource($request['id_role'],$request['deleteall']);
        echo $updateinsert;
    } 
	
	public function adduserAction()
    {
        // @todo Add the menu page action
		$request = $this->getRequest()->getParams();
		$u= new Admin_Model_User();
		if(isset($request['contactinfo']) && $request['contactinfo']='submit'){
		   $this->view->beforesubmit = $request['contactinfo'];
		 $password = getrandmax();  
		 $dataval = array('role'=>$request['roleid'],'Name'=> $request['Name'],'Email'=> $request['Email'],
		                  'Pass'=> $password,'RegistrationDate'=> date("Y-m-d H:i:s"));
		 $Insertdataadmin	=	$u->insertdata($dataval);
		 //echo $Insertdataadmin;die;
		 if(isset($Insertdataadmin) && $Insertdataadmin='1' ){
				/*$to = $request['Email'];
				$subject = "Test mail";
				$body = "Hello! This is a simple email message.";
				$from = "someonelse@example.com";
				$this->sendWithoutSmtpMail($to,$subject,$from,$body);
				echo "User added successfully and a confermation mail sent.";*/

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

}
