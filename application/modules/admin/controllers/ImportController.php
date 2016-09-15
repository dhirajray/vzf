<?php

class Admin_ImportController extends IsadminController
{

	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {

        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        parent::init();
    }

	
    /**
     * Index
     */ 

    public function indexAction()
    {
       $request = $this->getRequest()->getParams();
    }

   /*
   these is being used from model 
   	public function getemailtempate($fname,$Signuptoken,$loginid,$password)
    {
    	$body  = '<tr>
		        <td style="padding:0px 30px 30px 30px; ">
		<strong>Dear '.$fname.'</strong>,<br /> <br /><br />

		A new account has been created for you at <a href="'.BASE_URL.'" target="_blank">'.BASE_URL.'</a>.<br/><br/>

		Your login email address is as below:
		<br><br>
			login email : '.$loginid.'<br>
			password : whatever you set when logging in
		<br /><br />
		Please click on the link below to confirm your email address and activate your '.COMPANY_NAME.' account. <br /> <br />
		<a href="'.BASE_URL.'index/activelink/link/activate/id/'.$Signuptoken.'"  target="_blank" style="color:#ff7709; font-size:24px; display:block; width:550px; word-wrap:break-word;"> '.BASE_URL.'/index/activelink/link/activate/id/'.$Signuptoken.'</a> 
		<br><br>  Please change your password under account settings immediately after login.<br/><br/>
		'.COMPANY_FOOTERTEXT.'
		        </td>
		      </tr>';

		$MailFrom='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>'; //Give the Mail From Address Here
		$MailReplyTo=NOREPLY_MAIL;
        $MailTo 	=	$loginid;
        $MailSubject = "Your ".COMPANY_NAME." account needs activation";
        $MailCharset = "iso-8859-1";
        $MailEncoding = "8bit";
        $MailHeaders  = "From: $MailFrom\n";
        $MailHeaders .= "Reply-To: $MailReplyTo\n";
        $MailHeaders .= "MIME-Version: 1.0\r\n";
        $MailHeaders .= "Content-type: text/html; charset=$MailCharset\n";
        $MailHeaders .= "Content-Transfer-Encoding: $MailEncoding\n";
        $MailHeaders .= "X-Mailer: PHP/".phpversion();
        //echo "<pre>";
        $MailBody = html_entity_decode(str_replace("'","\'",$body));
        return $this->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$body);
    }
	
    public function getdeleteemailtempate($fname='',$loginid='',$from='')
    {
    	 $body  = '<tr>
			        <td style="padding:0px 30px 30px 30px; ">
			<strong>Dear '.$fname.'</strong>,<br /> <br /><br />';

			if($from=='deleteuser'){

				$body  .= 'This email has been sent to inform you that your account on our platform has been deleted by platform administrator. Please visit the platform again if you wish to create a new account.';
				$MailSubject = "Your account has been deleted";
			} else if($from='inviteuser')
			{
				$body  .= 'You have been invited to be a part of '.COMPANY_NAME.' social platform. Click on the link below to join in.<br><br>'. BASE_URL;
				$MailSubject = "You have been invited";
			}

			$body  .= '<br><br>  Best Regards<br/><br/>'.COMPANY_FOOTERTEXT.'
			        </td>
			      </tr>';

			$MailFrom='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>'; //Give the Mail From Address Here
	        $MailReplyTo=NOREPLY_MAIL;
	        $MailTo 	=	$loginid;
	        
	        $MailCharset = "iso-8859-1";
	        $MailEncoding = "8bit";
	        $MailHeaders  = "From: $MailFrom\n";
	        $MailHeaders .= "Reply-To: $MailReplyTo\n";
	        $MailHeaders .= "MIME-Version: 1.0\r\n";
	        $MailHeaders .= "Content-type: text/html; charset=$MailCharset\n";
	        $MailHeaders .= "Content-Transfer-Encoding: $MailEncoding\n";
	        $MailHeaders .= "X-Mailer: PHP/".phpversion();
	        //echo "<pre>";
	        $MailBody = html_entity_decode(str_replace("'","\'",$body)); 
	                
	        $this->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$body);
	}  */  

    public function invitationoprationAction() // Invitation : reinvite and delete operations
    {
       $request 	= $this->getRequest()->getParams();
       $inviteModal =	new Admin_Model_Invite();
       $common		=   new Admin_Model_Common();
       $deshboard   = new Admin_Model_Deshboard();
       $userObj		=   new Admin_Model_User();
       
       //echo'<pre>';print_r($request);die('=====');
      
       if($request['calle']=='reinvitevips')
       {
       		$ret 	= $inviteModal->reinviteUser($request['userid']);

       		$userdetail = $this->myclientdetails->getfieldsfromtable(array('Email','Name','Username','Signuptoken'),'tblUsers','UserID',$request['userid']);
       		//echo'<pre>';print_r($userdetail);die('=====');
       		$password 	=	rand(1000000,99999);
       		$token 		=  	md5(time());
 			      $dataval  = array(
	 					'Pass'=> $this->_generateHash($password),
	 					'Signuptoken'=>$token,
	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
	 					'Status' => 0
	 					);
 			    $Insertdataadmin	=	$this->myclientdetails->updatedata_global('tblUsers',$dataval,'UserID',$request['userid']);
       		$ret = $common->getemailtempate($this->myclientdetails->customDecoding($userdetail[0]['Name']),$token,$this->myclientdetails->customDecoding($userdetail[0]['Email']),$password);
       		echo 'reinvitevips';
       		exit();

       }
       else if($request['calle']=='addcarerid')
       {
          $tret = 'false';
          if($request['carerid']>0)
          {
            $this->myclientdetails->insertdata_global('tblUserMeta',array('clientID'=>clientID,'UserId'=>$request['userid'],'carerid'=>$request['carerid'],'date_added'=>date("Y-m-d H:i:s")));
            $tret =  'careridsuccess';
          }
          echo $tret ;
          exit();
       }
       else if($request['calle']=='reinviteAll')
       {
          
          $password   = rand(1000000,99999);
          $token    =   md5(time());
          $dataval  = array(
          'Pass'=> $this->_generateHash($password),
          'Signuptoken'=>$token,
          'emailsent'=>0,
          'fromcsv'=>1,
          'RegistrationDate'=> date("Y-m-d H:i:s"),
          'Status' => 0
          );
          $Insertdataadmin  = $this->myclientdetails->updateMaster('tblUsers',$dataval,array('activeFirstTime'=>0,'Status' => 0,'clientID'=>clientID));
          //$ret = $common->getemailtempate($this->myclientdetails->customDecoding($userdetail[0]['Name']),$token,$this->myclientdetails->customDecoding($userdetail[0]['Email']),$password);
          echo 'reinviteAll';
          exit();
       }
       else if($request['calle']=='deactiveAll')
       {
          
          $dataval  = array(
              'LastUpdateDate'=> date("Y-m-d H:i:s"),
              'Status' => 0
          );
          $Insertdataadmin  = $this->myclientdetails->updateMaster('tblUsers',$dataval,array('activeFirstTime'=>1,'Status' => 1,'clientID'=>clientID));
         
          echo 'deactiveAll';
          exit();
       }
       else if($request['calle']=='activateAll')
       {
          
          $dataval  = array(
              'LastUpdateDate'=> date("Y-m-d H:i:s"),
              'Status' => 1
          );
          $Insertdataadmin  = $this->myclientdetails->updateMaster('tblUsers',$dataval,array('activeFirstTime'=>1,'Status' => 0,'clientID'=>clientID));
         
          echo 'activateAll';
          exit();
       }
       else if($request['calle']=='deleteallvips')
       {
       		foreach($request['deleteall'] as $key=>$userid)
       		{
	       		$udtl = $this->myclientdetails->getfieldsfromtable(array('Email','Name','activeFirstTime'),'tblUsers','UserID',$userid);
	       		
	       		if($inviteModal->deleteinviteUser($userid,'deletevips')) {
	       			if($udtl[0]['activeFirstTime']==1)
	       			$MailBody = $common->getdeleteemailtempate($udtl[0]['Name'],$udtl[0]['Email'],'deleteuser');
	       			
	       		}
	       	}
	       	echo 'deleteallvips~'.$request['page'].'~'.$request['urlpage'];
       		exit();
       }
      else if($request['calle']=='markedallvip')
       {
          $socialModal  = new Admin_Model_Social();
          $userModal  = new Admin_Model_User();
          foreach($request['deleteall'] as $key=>$userid)
          {            
            $userModal->updateMarkVIP($userid);
            
            
            $userdetailall = $userModal->getUserByUserID($userid);
            $Profile_URL= BASE_URL.'/user/'.$this->myclientdetails->customDecoding($userdetailall[0]['Username']);
            //$socialModal->commomInsert(46,56,'',$this->adminUserID,$userid);

            $EmailTemplateArray = array('uEmail'  => $userdetailall[0]['Email'],
                                          'uName'   => $userdetailall[0]['full_name'],
                                          'Profile_URL'  => $Profile_URL,
                                          'case'      => 8);
            $this->dbeeComparetemplateOne($EmailTemplateArray);           
          }
          echo 'markedallvip~'.$request['page'].'~'.$request['urlpage'];
          exit();
       }
       else if($request['calle']=='deletevips')
       {	
       		$udtl = $this->myclientdetails->getfieldsfromtable(array('Email','Name','activeFirstTime'),'tblUsers','UserID',$request['userid']);
       		
       		if($inviteModal->deleteinviteUser($request['userid'],'deletevips')) {
       			if($udtl[0]['activeFirstTime']==1)
       			$MailBody = $common->getdeleteemailtempate($udtl[0]['Name'],$udtl[0]['Email'],'deleteuser');
       			echo 'deletevips';
       		}
       		exit();
       }
       else if($request['calle']=='re')
       {
       		$ret 	= $inviteModal->reinviteUser($request['userid']);
       		$uEmail = $inviteModal->getSpecificRecord('inv_email',$request['userid']);			 
       		$uName = $inviteModal->getSpecificRecord('inv_name',$request['userid']);
        	/****for email ****/ 
			     $EmailTemplateArray = array('uEmail' => $uEmail,
		                                'uName'  => $uName,
		                                 'case'   => 2);
            $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
			     /****for email ****/
       }
       else if($request['calle']=='delete')
       {
       		$inviteModal->deleteinviteUser($request['userid']);
       		echo 'deleted';
       		exit();
       }
       if($request['calle']=='invideagainall')
       {
       	 //echo "string";exit();
       		$getInactve =$userObj->getUsersbyStatus('','vipuser',2);
       		$count = 0;
       		foreach ($getInactve as $key => $userdetail) 
       		{
       			$count++;
	       		$ret 	= $inviteModal->reinviteUser($userdetail['UserID']);

	       		//echo'<pre>';print_r($userdetail);die('=====');
	       		$password 	=	rand(1000000,99999);
	       		$token 		=  	md5(time());
	 			     $dataval  = array(
		 					'Pass'=> $this->_generateHash($password),
		 					'Signuptoken'=>$token,
		 					'RegistrationDate'=> date("Y-m-d H:i:s"),
		 					'Status' => 0
		 					);
	 			 $Insertdataadmin	=	$this->myclientdetails->updatedata_global('tblUsers',$dataval,'UserID',$userdetail['UserID']);
	       		$ret = $common->getemailtempate($userdetail['Name'],$token,$userdetail['Email'],$password,$reinvite='reinvite');
	       	}
       		echo 'invideagainall~'.$count;
       		exit();

       }
       exit;
    }

    public function invitedetailsAction() // Retriving all records of invited users along with search enabled
    {
        $request = $this->getRequest()->getParams();
		if(isset($request['searchfield']) && $request['searchfield']!='')
		$seachfield = $request['searchfield'];
		else
		$seachfield = '';		
		$u= new Admin_Model_Invite();
		$userData = $u->getUsers($seachfield);
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($userData);
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;
    }

    public function getcsvrecordsAction()
    {
       $request = 	$this->getRequest()->getParams();
       $csvObj	=   new Admin_Model_Common();
       $user_id	=	$_SESSION['Zend_Auth']['storage']['userid'];   
    
       if (empty($_FILES['csvfile']['name'])) {
       		if ($request['csv_submit']=='Invite') {
       			
       			return $this->_helper->redirector->gotoSimple('invitedetails','import',null,array('File' => "Can not be null"));
       		} 
       		else if ($request['csv_submit']=='ADD VIP') {
       			return $this->_helper->redirector->gotoSimple('index','vipuser',null,array('File' => "Can not be null"));
       		}
       		else if ($request['csv_submit']=='ADD USERS') {
       			return $this->_helper->redirector->gotoSimple('index','user',null,array('File' => "Can not be null"));
       		}
          else if ($request['csv_submit']=='ADD USERS CARERID') {
            return $this->_helper->redirector->gotoSimple('index','user',null,array('File' => "Can not be null"));
          }
       		else if ($request['csv_submit']=='ADD FILTER WORD') {
       			return $this->_helper->redirector->gotoSimple('Restrictedurl','profanityfilter',null,array('File' => "Can not be null"));
       		}
       		else {
       			return $this->_helper->redirector->gotoSimple('index','vipuser',null,array('File' => "Can not be null"));
       		}
       }
      	if($request['csv_submit']=='ADD FILTER WORD'){
      		$csvObj->uploadFilterWordcsvOpration($_FILES['csvfile']);
      		//return $this->_helper->redirector->gotoSimple('Restrictedurl','profanityfilter',null,array('File' => "Sucessfully imported"));
			$this->_redirect('/admin/Restrictedurl/profanityfilter/#msgsortprfilteraddcsv');
      	}else{
        	$csvfun	=	$csvObj->uploadcsvOpration($_FILES['csvfile'],$user_id,$request['csv_submit'],$request['user_type']);
    	}
      //print_r($csvfun);exit;
        $this->view->csvData	= $csvfun['failedrec'];
        $this->view->totalSuccess  = $csvfun['totalSuccess'];	
        $this->view->csvRefer	= $request['csv_submit'];    
    }

    public function updateuserroleAction(){
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getParams();
        //echo'<pre>';print_r($request);die;
        $userModal	=	new Admin_Model_User();
        $common		=   new Admin_Model_Common(); 
       	$userid	=	explode(",", $request['selectusersid']);
       	$useridval = array_filter($userid);
       	$selectrol	= $request['roleid'];
       	foreach ($useridval as $key => $value){
       		//echo'<pre>';print_r($value);die;
       		$useralldetails = $userModal->UserdetailsByemail($value);
       		//echo'<pre>';print_r($useralldetails);die;
       		$notifData = array('clientID'=>clientID,'act_type'=>'37','act_message'=>'41','act_userId'=>adminID,'act_ownerid'=>$value,'act_status'=>0,'act_date'=>date("Y-m-d H:i:s") );
       		$return_noti = $this->myclientdetails->insertdata_global('tblactivity',$notifData);
       		$Email = $this->myclientdetails->customDecoding($useralldetails[0]['Email']);
       		$Name = $this->myclientdetails->customDecoding($useralldetails[0]['Name']);
          $Username = $this->myclientdetails->customDecoding($useralldetails[0]['Username']);
       		
       		//$valueemail = $this->myclientdetails->customDecoding($Email);
       		//echo'<pre>';print_r($valueemail);die;
            $avail = $userModal->updateUserRole($useralldetails[0]['Email'],$value,$selectrol);         
            //echo'<pre>';print_r($useralldetails[0]['Name']);die;
            $common->getemailtempateroleree($Email,$Name,$Username);
             $Email ='';
             $Name ='';
             $Username ='';
       	}
    }

    public function  checkuserAction(){
        $this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getParams();
        $userModal	=	new Admin_Model_User(); 
       	$username	=	$this->myclientdetails->customEncoding($request['usernamerInput']);
        $avail = $userModal->checkUserByUsername($username); 
        if($avail>0){
           echo '<font color="red">The user name <STRONG>'.$request['usernamerInput'].'</STRONG> is already in use.</font>
                 <input type="hidden" class="nameNotval" value="errornameNot">';;
        }else{ echo 'OK';}  
    }  
	
	public function adduserAction()
    {

       	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $deshboard   = new Admin_Model_Deshboard();
        $userModal	=	new Admin_Model_User();
        $inviteModal=	new Admin_Model_Invite();
        $common		=   new Admin_Model_Common();
        $user_id	=	$_SESSION['Zend_Auth']['storage']['userid'];
       	$request = $this->getRequest()->getParams();
       	//echo'<pre>';print_r($request);die;
		$u= new Admin_Model_User();

		if(isset($request['email']) && $request['require']=='vipusers')
		{ 
		    $getEmail	=	explode(",", $request['email']);
		   	$getName	=	explode(",", $request['username']);
		   	$title		=	explode(",", $request['jobtitle']);
		   	$company	=	explode(",", $request['company']);
		   	$getType	=	$request['usertype'];
		   	$typename   =   $this->myclientdetails->getfieldsfromtable(array('TypeName'),'tblUserType','TypeID',$request['usertype']);
		   	$gettypename	=	$typename[0]['TypeName'];
		 	  $already	=	"";


		   	foreach ($getEmail as $key => $value) 
		   	{
		   	 	$encodeEmail = $this->myclientdetails->customEncoding($value);
		  	 	$avail	  	=	 $userModal->chkUsersExist($encodeEmail);
		  	 	
		  	 	if($avail<1)
		   	 	{
		   	 		
		   	 		$password 	= rand(10000,999999); 
		   	 		$spuname  	=  	split('@', $value);
			  	 	$spname 	= 	explode(' ', $getName[$key]);
			  	 	$uname 		=	$spuname[0].rand(1000,9999);
			  	 	$token 		=  	md5($this->_generateHash($password));
	   	 			$dataval  = array(
		   	 					'Name'=> $this->myclientdetails->customEncoding($getName[$key]),
                  'full_name'=> $this->myclientdetails->customEncoding($getName[$key]),
		   	 					'Email'=>$encodeEmail,
		   	 					'Username'=>$this->myclientdetails->customEncoding($uname),
		   	 					'title'=>$this->myclientdetails->customEncoding($title[$key]),
		   	 					'company'=>$this->myclientdetails->customEncoding($company[$key]),
		   	 					'usertype'=>$getType,
	   	 						'typename'=>$gettypename,
		   	 					'Pass'=> $this->_generateHash($password),
	   	 						'ProfilePic'=>'default-avatar.jpg',
		   	 					'Signuptoken'=>$token,
		   	 					'clientID' => clientID,
		   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
		   	 					'Status' => 0
		   	 					);
	   	 			$Insertdataadmin	=	$userModal->insertdata($dataval);
	   	 			$MailBody = $common->getemailtempate($getName[$key],$token,$value,$password);

	   	 			
	   	 		
	   	 		}
	   	 		else {
	   	 			$already	.=	$value.', ';	
	   	 		}	
		   	} 
		 	
  			echo trim($already,', ');
  		 	exit;
		}

		if(isset($request['email']) && $request['require']=='roleusers')
		{
		    $getEmail	=	explode(",", $request['email']);
		   	$getName	=	explode(",", $request['username']);
		   	$title		=	explode(",", $request['jobtitle']);
		   	$company	=	explode(",", $request['company']);
		   	$lname	    =	$request['lname'];
		   	$usernamerole	=	$request['usernamerole'];
		   	$passwordrole	=	$request['passwordrole'];
		   	//$getType	=	$request['usertype'];
		   	//$typename   =   $this->myclientdetails->getfieldsfromtable(array('TypeName'),'tblUserType','TypeID',$request['usertype']);
		   	$gettypename	=	$typename[0]['TypeName'];
		 	  $already	=	"";


		   	foreach ($getEmail as $key => $value) 
		   	{
		   	 	$encodeEmail = $this->myclientdetails->customEncoding($value);
		  	 	$avail	  	=	 $userModal->chkUsersExist($encodeEmail);
		  	 	//echo'<pre>';print_r($avail);die;
		  	 	if($avail<1)
		   	 	{ 
		   	 		$spuname  	=  	split('@', $value);
			  	 	$spname 	= 	explode(' ', $getName[$key]);
			  	 	if(isset($request['roleid'])){
			  	 	  $uname 		=   $usernamerole;
			  	 	  $password 	=   $passwordrole;
			  	 	  $token 		=  	md5($this->_generateHash($password));
			  	 	  $status       =1;
			  	 	}else{
			  	 	  $uname 		=	$spuname[0].rand(1000,9999);
			  	 	  $token 		=  	md5($this->_generateHash($password));
			  	 	  $password 	= rand(10000,999999);
			  	 	  $status       =0;	
			  	 	}

	   	 			$dataval  = array(
	   	 				        'role'=>$request['roleid'],
		   	 					'Name'=> $this->myclientdetails->customEncoding($getName[$key]),
		   	 					'lname'=>$this->myclientdetails->customEncoding($lname),
                  'full_name'=> $this->myclientdetails->customEncoding($getName[$key].' '.$lname),
		   	 					'Email'=>$encodeEmail,
		   	 					'Username'=>$this->myclientdetails->customEncoding($uname),
		   	 					'title'=>$this->myclientdetails->customEncoding($title[$key]),
		   	 					'company'=>$this->myclientdetails->customEncoding($company[$key]),
		   	 					//'usertype'=>$getType,
	   	 						//'typename'=>$gettypename,
		   	 					'Pass'=> $this->_generateHash($password),
	   	 						'ProfilePic'=>'default-avatar.jpg',
		   	 					'Signuptoken'=>$token,
		   	 					'clientID' => clientID,
		   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
		   	 					'Status' => $status
		   	 					);
	   	 			$Insertdataadmin	=	$userModal->insertdata($dataval);
	   	 			$MailBody = $common->getemailtempaterole($getName[$key],$uname,$token,$value,$password);
	   	 			$already	.=	$avail;	   	 		
	   	 		}
	   	 		else {
	   	 			$already	.=	$avail;
	   	 		}	
		   	} 
		 	
			 echo $already;exit;
		}
		else if(isset($request['email']) && $request['require']=='addadmin')
		{  
		    $getEmail	=	explode(",", $request['email']);
        $getName  = explode(",", $request['username']);
		   	$carerid	=	explode(",", $request['carerid']);
		   	$title		=	explode(",", $request['jobtitle']);
		   	$company	=	explode(",", $request['company']);
		 	  $already	=	"";
		   	foreach ($getEmail as $key => $value) 
		   	{
		   		$encodeEmail = $this->myclientdetails->customEncoding($value);
		   	 	$password = getrandmax(); 
		  	 	$avail	  =	 $userModal->chkUsersExist($encodeEmail);

		   	 	if($avail<1)
		   	 	{
		   	 		$password 	= rand(10000,999999); 
		   	 		$spuname  	=  	split('@', $value);
			  	 	$spname 	= 	explode(' ', $getName[$key]);
			  	 	$uname 		=	$spuname[0].rand(1000,9999);
			  	 	$token 		=  	md5(rand(100000,999999));
			  	 	$getType	=	$request['usertype'];
	   	 			$dataval  = array(
		   	 					'Name'=> $this->myclientdetails->customEncoding($getName[$key]),
                  'full_name'=> $this->myclientdetails->customEncoding($getName[$key]),
		   	 					'Email'=>$encodeEmail,
		   	 					'Username'=>$this->myclientdetails->customEncoding($uname),
		   	 					'title'=>$this->myclientdetails->customEncoding($title[$key]),
		   	 					'company'=>$this->myclientdetails->customEncoding($company[$key]),
	   	 						'usertype'=>6,
		   	 					'Pass'=> $this->_generateHash($password),
	   	 						'ProfilePic'=>'default-avatar.jpg',
		   	 					'Signuptoken'=>$token,	   	 						
		   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
		   	 					'clientID' => clientID,
		   	 					'Status' => 0
		   	 					);
	   	 			
	   	 			$Insertdataadmin	=	$userModal->insertdata($dataval);

            if($carerid[$key])
            {
              $this->myclientdetails->insertdata_global('tblUserMeta',array('clientID'=>clientID,'UserId'=>$Insertdataadmin,'carerid'=>$carerid[$key],'date_added'=>date("Y-m-d H:i:s")));
            }
	   	 			$MailBody = $common->getemailtempate($getName[$key],$token,$value,$password);
	   	 			//$return_noti = $deshboard->insertNotification($Insertdataadmin);

	   	 		}
	   	 		else {
	   	 			$already	.=	$value.', ';	
	   	 		}	
		   	} 
		 	  
  			echo trim($already,', ');
  		 	exit;
		}
		else if(isset($request['email']) && $request['require']=='inviteadmin')
		{
		    $getEmail	=	explode(",", $request['email']);
		   	$getName	=	explode(",", $request['username']);
		 	  $already	=	"";
		   	foreach ($getEmail as $key => $value) 
		   	{
		   	 	 $availUsers	  =	 $userModal->chkUsersExist($this->myclientdetails->customEncoding($value));

		  	 	 $availInvited =	 $inviteModal->chkInviteExist($this->myclientdetails->customEncoding($value));

		   	 	if($availUsers<1 && $availInvited<1)
		   	 	{
	   	 			$dataval  = array(
		   	 					'inv_name'=> $getName[$key],
		   	 					'clientID' => clientID,
		   	 					'inv_email'=>$value,
		   	 					'inv_by'=> $user_id,
		   	 					'inv_added'=> date("Y-m-d H:i:s"),
		   	 					);
	   	 			
	   	 			$Insertdataadmin	=	$inviteModal->insertdata($dataval);
	   	 			$common->getdeleteemailtempate($getName[$key],$value,'inviteuser');
	   	 			
	   	 		}
	   	 		else {
	   	 			$already	.=	$value.', ';	
	   	 		}	
		   	} 
		 	
  			echo trim($already,', ');
  		 	exit;
		}
		exit;
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
	
}

