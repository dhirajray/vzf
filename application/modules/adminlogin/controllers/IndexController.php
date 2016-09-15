<?php

class Adminlogin_IndexController extends IsloginController
{

    /**
     * Init
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        parent::init();
        $this->myclientdetails = new Admin_Model_Clientdetails();
        $auth = Zend_Auth::getInstance();  
        if ($auth->hasIdentity()) 
        {
           $data = $auth->getStorage()->read();
           $Roledetailsval = new Admin_Model_Reporting();
           $AllRolesdetailslist = $Roledetailsval->getRolesDetails($data['role']);            
           if(count($AllRolesdetailslist)>0){
               $modulefirst = $AllRolesdetailslist[0]['module'];
               $controllerfirst = $AllRolesdetailslist[0]['controller']; 
               $actionfirst = $AllRolesdetailslist[0]['action'];
               $urlrole = "/".$modulefirst."/".$controllerfirst."/".$actionfirst;
               if($modulefirst!='' && $controllerfirst!='' && $actionfirst!='')
                    $this->_redirect($urlrole);
            }
            else{
               $this->_redirect('/'); 
            }     
        }
    }

    public function cronAction()
    { 
        ini_set('display_errors',1);
        error_reporting(E_ALL|E_STRICT);
        ini_set('error_log','script_errors.log');
        ini_set('log_errors','On');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        require_once('parsecsv/parsecsv.lib.php');
        # create new parseCSV object.
        $csv = new parseCSV();
        $data_das   = '19072016';
        $dir = $_SERVER['DOCUMENT_ROOT'].'/reevoo_agdata_'.$data_das.'.csv';
        
        //date("dmY")

        $username = 'agdata';
        $password = 'Pisa75dictaas23';
        $url = 'https://agdata.reevoo.com/reevoo_agdata_'.$data_das.'.csv'; 
        $context = stream_context_create(array(
          'http' => array(
          'header'  => "Authorization: Basic " . base64_encode("$username:$password")
        )
        ));

        $remote_file_contents = file_get_contents($url, false, $context);
        $remote_file_contents;
        
        //Get the contents
        file_put_contents($dir, $remote_file_contents);
        # Parse '_books.csv' using automatic delimiter detection...
        $csv->auto('reevoo_agdata_'.$data_das.'.csv');
        
        foreach ($csv->data as $key => $row): 

              $userID = $this->UserDataCheck($row);
              $data['UserID'] =  $userID;
              $data['clientID'] =  clientID;
              $data['Comment'] =  nl2br($row['text_answer']);
              $data['CommentDate'] =  $row['submitted_at'];
              if(trim($row['image_url_answer'])!=''){
                  $data['Pic'] = $this->getRevooPic($row['image_url_answer']);
              }else{
                  $data['Pic'] = '';
              }
              $data['Type'] = 1;
              $dataDbeeID =  $this->getPostIdByTag($row['tag']);
              if($dataDbeeID['DbeeID']!='' && $row['tag']!='')
              {
                $this->postCommentClientData($data);
                $this->updateDbeeRecord($dataDbeeID);
              }
          
        endforeach;        

        echo $i.' comment inserted';
    }


    public function UserDataCheck($row)
    {

      $password = getrandmax();
      $userModal    =   new Admin_Model_User();
      $encodedName = $this->myclientdetails->customEncoding($row['first_name']);
      $userData    =  $userModal->chkUsersHashExists($row['email_hash']);
      if(count($userData)<1)
      {
        $string = strtolower(preg_replace('/\s+/', '', $row['first_name']));
        $usname   = $string.rand(1000,9999);
        $dataval  = array(
              'clientID' => clientID,
              'Name'=> $encodedName,
              'full_name'=> $encodedName,
              'Email'=>'',
              'EmailHash'=>$row['email_hash'],
              'Pass'=> '',//$this->_generateHash($password),
              'Username'=> $this->myclientdetails->customEncoding($usname),
              'Signuptoken'=>'',
              'RegistrationDate'=> date("Y-m-d H:i:s"),
              'Status' => 0,
              'emailsent'=>0,
              'fromcsv'=>0,
              'lastcsvrecord'=>0,
              'guest'=>1
              );
        //print_r($dataval); die;
        $userID =  $userModal->insertdata($dataval); 

        $data2['fingerprint'] =  ($row['rr_finger_print']==NULL)?'':$row['rr_finger_print'];
        $data2['retailerscustomerref'] = ($row['retailers_customer_ref']==NULL)?'':$row['retailers_customer_ref'];
        $data2['UserId'] =  $userID;
        $data2['clientID'] =  clientID;

        $this->myclientdetails->insertdata_global('tblUserMeta', $data2);

        $notify = array('User' => $userID,'clientID'=>clientID,'Dbees'=>0,'ReDbees'=>0,'Comments'=>0,'Mentions'=>0,'Scores'=>0,'Groups'=>0,'Messages'=>0,'Followers'=>0,'SiteAlerts'=>0);

        $this->myclientdetails->insertdata_global('tblNotificationSettings',$notify);
        return $userID;
      }
      else {
          return $userData[0]['UserID'];
      }
    }

    public function postCommentClientData($data)
    {
        $this->myclientdetails->insertdata_global('tblDbeeComments', $data);
        
    }

    public function updateDbeeRecord($dataDbeeID)
    {
        $grouptypes = new Application_Model_Groups();
        
        $grupdetais = $grouptypes->groupuserdetail($dataDbeeID['DbeeID']);
        
        $groupinfo = array(
            'GroupID' => $dataDbeeID['DbeeID'],
            'Owner' => $grupdetais[0]['UserID'],
            'User' => $dataDbeeID['UserID'],
            'JoinDate' => date('Y-m-d H:i:s'),
            'SentBy' => 'Self',
            'Status' => '1',
            'clientID' => clientID
        );
        $checkgroupinter = $grouptypes->insertgroup($groupinfo);


        $orderbyEventpArr = array('CommentDate'=>'DESC');
       
        $commentData = $this->myclientdetails->getAllMasterfromtable('tblDbeeComments', array(
            '*'
        ), array(
            'DbeeID' => $dataDbeeID['DbeeID']
        ),$orderbyEventpArr);

        $CommentDate = $commentData[0]['CommentDate'];
        
        $wherexx = array(
            'DbeeID' => $dataDbeeID['DbeeID']
        ); 

        $allusers = $this->myclientdetails->getAllMasterfromtable('tblDbeeComments', array(
            'DISTINCT(UserID)'
        ), $wherexx);

        $count_user = count($allusers);
        $count_comment = count($commentData);

        $data2 = array(
            'Comments' => $count_comment,
            'LastCommentUser' => $allusers[0]['UserID'],
            'LastActivity' => $CommentDate,
            'no_of_commented_users'=> $count_user
        );

        $this->myclientdetails->updatedata_global('tblDbees', $data2, 'DbeeID', $dataDbeeID['DbeeID']);
    }

    public function getPostIdByTag($dbee_tag)
    {
        $dbeeData = $this->myclientdetails->getAllMasterfromtable('tblDbees', array(
            '*'
        ), array(
            'dbee_tag' => $dbee_tag
        ));
        return $dbeeData[0];
    }


    public function getRevooPic($pics)
    {

        if($pics)
        {
            $picture = time().'.png';

            $dir = ABSIMGPATH."/imageposts/".$picture;

            $storeFolder    = ABSIMGPATH."/imageposts/";
            
          
            chmod($storeFolder , 0777);
            $iscopy = copy($pics, $dir);
            

            $filename       = ABSIMGPATH."/imageposts/medium/".$picture;
            $filename1      = ABSIMGPATH."/imageposts/small/".$picture;
            list($width,$height) = getimagesize(IMGPATH."/imageposts/".$picture);
            $src = imagecreatefrompng(IMGPATH."/imageposts/".$picture);
            if($iscopy)
            {
                if($width < 484)
                {                   
                    $medium=copy($storeFolder.$picture, $filename);

                    if($width < 200)
                    {                     
                      $medium=copy($storeFolder.$picture, $filename1);  
                    }
                    else
                    {
                        $newwidth1=200;
                        $newheight1=($height/$width)*$newwidth1;
                        $tmp1=imagecreatetruecolor($newwidth1,$newheight1);
                        $red = imagecolorallocate($tmp1, 255, 0, 0);
                        $black = imagecolorallocate($tmp1, 0, 0, 0);
                        imagecolortransparent($tmp1, $black);
                        imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
                        $quality = round(abs((70 - 100) / 11.111111));                      
                        imagepng ($tmp1,$filename1,$quality);   
                        imagedestroy($src);                 
                        imagedestroy($tmp1);
                    }
                }
                else
                {
                    $newwidth=484;
                    $newheight=($height/$width)*$newwidth;
                    $tmp=imagecreatetruecolor($newwidth,$newheight);

                    $newwidth1=200;
                    $newheight1=($height/$width)*$newwidth1;
                    $tmp1=imagecreatetruecolor($newwidth1,$newheight1);
                    $red = imagecolorallocate($tmp1, 255, 0, 0);
                    $black = imagecolorallocate($tmp1, 0, 0, 0);
           
                    imagecolortransparent($tmp1, $black);
                    imagecolortransparent($tmp, $black);
                    

                    imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
                    imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
                    
                    $quality = round(abs((70 - 100) / 11.111111));
                    imagepng ($tmp,$filename,$quality);
                    imagepng ($tmp1,$filename1,$quality);
                    imagedestroy($src);
                    imagedestroy($tmp);
                    imagedestroy($tmp1);
                }

                return $picture;
            }else{
                return '';
            }

        }
    }
     
    /**
     * Index
     */
    public function indexAction()
    {
        $searchparam = new Zend_Session_Namespace();
       $this->_helper->layout()->setLayout('layout');
       $flash = $this->_helper->getHelper('flashMessenger');        
        if ($flash->hasMessages()) {
            $this->view->message = $flash->getMessages();
        }
        $opt= array (
                'custom' => array (
                    'timeout' => $this->_options['auth']['timeout']
                )       
        );
        $this->view->form = new Adminlogin_Form_Login($opt);
        $this->render('login');
        
    }

   

    
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();   
        if ($auth->hasIdentity()) 
        {
            $data = $auth->getStorage()->read();
            //print_r($data); exit;
            $this->_userid = $data['user_id'];
            $this->_redirect('/admin/index');
        }
    }
    /**
     * Login
     */
    public function loginAction()
    {
        $request   = $this->getRequest()->getParams();
        
        if(!empty($request['token']) && ($request['username']=='') && ($request['password']=='')){ 
            $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");
            $this->_redirect('/admin/login');  
        }
        else if(!isset($request['token'])){ 
          $this->_redirect('/admin/login');
        }
        
        $namespacexx = new Zend_Session_Namespace('zend_token_admin'); 
        $salt = 'b79jsMaEzXMvCO2iWtzU2gT7rBoRmQzlvj5yNVgP4aGOrZ524pT5KoTDJ7vNiIN';
       
        $token = sha1($salt . $namespacexx->times);
        if($token == $request['token'])
        {
           if ($this->getRequest()->getMethod() == 'POST') 
            {
                $host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
                $auth= Zend_Auth::getInstance();
                $config = Zend_Registry::get('config');
                $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
                $opt = array (
                        'custom' => array (
                            'timeout' => 60000,
                        )       
                );
                $form = new Adminlogin_Form_Login($opt);
         
                if (!$form->isValid($this->getRequest()->getPost())) {
                    $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");
                    $this->view->form = $form;
                    $this->_redirect('/admin/login');
                }
                        $options=array();
                $options['username'] = $this->myclientdetails->customEncoding($this->getRequest()->getParam('username'));
                $options['clientID']= clientID;
                $options['Status']= 1;
                $userresult = $this->myclientdetails->getAllMasterfromtable('tblUsers','*',$options);
                $options['password']= $this->getRequest()->getParam('password');
                $authNamespace = new Zend_Session_Namespace('identify');
                $authNamespace->setExpirationSeconds((1209600));

                $authNamespace->role = $userresult[0]['role'];
                $adapter = new Zend_Auth_Adapter_DbTable($db);
                $adapter->setTableName('tblUsers');
                $adapter->setIdentityColumn('Username');
                $adapter->setCredentialColumn('Pass');
                $adapter->setIdentity($options['username']);
                $chkPass = $adapter->setCredential($adapter->setCredentialdbee($options['password'], $userresult[0]['Pass']));
                $select = $adapter->getDbSelect();
                $select->where('clientID = ?',$options['clientID']);
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) 
                {
                    // update login time
                    $this->myclientdetails->updatedata_global('tblUsers',array('LastLoginDate'=>date('Y-m-d H:i:s')),'UserID',$userresult[0]['UserID']);
                    $authdta = $auth->getStorage()->write($userresult[0]);
                    $authNamespace->id = $userresult[0]['UserID'];
                    $authNamespace->user = $userresult[0]['Username'];
                    $authNamespace->lockedtogroup = $userresult[0]['groupid'];
                    
                    $namespace = new Zend_Session_Namespace('Zend_Auth');
                    $namespace->setExpirationSeconds((1209600));


                    $searchparam = new Zend_Session_Namespace();
                    //echo $searchparam->searchdb; die;
                    if($searchparam->searchdb!="")
                    {
                      $searchdbvar=$searchparam->searchdb;
                      unset($searchparam->searchdb);
                      $searchparam->searchdb="";
                      $this->_redirect('/admin/dashboard/post/id/'.$searchdbvar);
                    }
                    else
                    {
                      if($userresult[0]['role']==''){
                         $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");
                         $this->_redirect('/admin/login');
                       }
                      else if($userresult[0]['role']!=1 && $userresult[0]['role']!=1001){
                       $Roledetailsval = new Admin_Model_Reporting();
                       $AllRolesdetailslist = $Roledetailsval->getRolesDetails($userresult[0]['role']); 
                                       
                       $modulefirst = $AllRolesdetailslist[0]['module'];
                       $controllerfirst = $AllRolesdetailslist[0]['controller']; 
                       $actionfirst = $AllRolesdetailslist[0]['action'];
                       $urlrole = "/".$modulefirst."/".$controllerfirst."/".$actionfirst;

                       if(empty($controllerfirst)){
                            $auth = Zend_Auth::getInstance();
                            $auth->clearIdentity();
                            //session_destroy();
                            $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");      
                            $this->_redirect('/admin/login');
                       }
                       $this->_redirect($urlrole);
                       }else if($userresult[0]['role']==1){
                         $this->_redirect('/admin/index');
                       }else if($userresult[0]['role']==1001){
                         $this->_redirect('/admin/dbeeauth');
                       }

                    }    
                        
                } else {
                    $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");
                    $this->_redirect('/admin/login');
                }
            }
        }else{
            $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");
            $this->_redirect('/admin/login');
        }   
    }

    public function registrationAction()
    {

        $this->view->chkform  = 'registration';
        $flash = $this->_helper->getHelper('flashMessenger');       
        if ($flash->hasMessages()) {
            $this->view->message = $flash->getMessages();
        }
        $opt= array (
                'custom' => array (
                    'timeout' => 60000//$this->_options['auth']['timeout']
                )       
        );
        $auth= Zend_Auth::getInstance();
        $config = Zend_Registry::get('config');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $user= new Adminlogin_Model_Users($db);

        $deshboard= new Admin_Model_Deshboard();

        $chkreg =  $this->myclientdetails->passsqlquery('select count(*) as clientcou from tblUsers where clientID='.clientID.' and role=1');  //$user->chkforregistration();
        $this->view->accountexist = $chkreg[0]['clientcou'];

      
        if($this->getRequest()->getPost())
        {

            $form = new Adminlogin_Form_Registration($opt);
           
            if (!$form->isValid($this->getRequest()->getPost())) {   
                $this->view->form = $form;
                //return $this->render('registration');
            }
            
            $result = 0;
            $params = $this->getRequest()->getPost();
             

            $chkfuser = $this->myclientdetails->getfieldsfromtable(array('Email','Pass'),'tblUsers','Email',trim($this->myclientdetails->customEncoding($params['email'])));

            if(count($chkfuser)>0)
            {
                $this->_helper->flashMessenger->addMessage("Given email address is already taken. ");
                $this->_redirect(BASE_URL.'/admin/registration');
            }


           
            if ($params) {
               
                if($_FILES['attachment']['size']!='')
                {
                    $picture    =   strtolower(time().'_'.$_FILES['attachment']['name']);
                    $copydone   =   copy($_FILES['attachment']['tmp_name'], 'adminraw/profilepic/' .$picture);
                    $copydone   =   copy($_FILES['attachment']['tmp_name'], 'userpics/' .$picture);
                
                } else 
                {
                    $picture    =   'default-avatar.jpg';
                }

                $spuname    =  split('@', $params['email']);
                $usname     =   $spuname[0].rand(1000,9999);
              
                $data= array(
                    'clientID' => clientID,
                    'role' =>1,
                    'Name' => $this->myclientdetails->customEncoding($params['username']),
                    'Username' => $this->myclientdetails->customEncoding($params['username']),//$this->myclientdetails->customEncoding($usname),
                    'usertype' => '10',
                    'takeatour' => '1',
                    'typename' => 'Administrator',
                    'activeFirstTime'=>1,
                    'Email' => $this->myclientdetails->customEncoding($params['email']),
                    'Pass'=> $this->_generateHash($params['password']),                    
                    'ProfilePic' =>$picture,
                    'Gender' =>$this->myclientdetails->customEncoding($params['Gender']),
                    'Birthdate' =>date('Y-m-d',strtotime($params['dob'])),
                    'Status' =>1,
                    'RegistrationDate' =>date("Y-m-d H:i:s"),
                );
                $froUId = $user->insertdata_global('tblUsers',$data);

                if($froUId)
                {
                    $user->insertdata_global('tblNotificationSettings',array('clientID' => clientID,'User' => $froUId ));
                    /*for ($s = 1; $s < 5; $s++)
                    {
                        $datarss = array(
                            'clientID' => clientID,
                            'Site' => $s,
                            'User' => $froUId
                        );
                        $user->insertdata_global('tblUserRss',$datarss);
                    }*/
                    $rss   = new Application_Model_Rss();
                    $rss->insertactivefeed($froUId);
                    $emailTemplatemain= $this->dbeeEmailtemplate();
                    //$deshboard   = new Home_Model_Deshboard();
                    $areatype ='admin';
                    $bodyContent = $deshboard->getGroupemailtemplate($areatype);                   
                    $footerContentmsg = 'powered by db corporate social platforms';                                   
                    $bodyContentjsonval = Array(
                                                'fontColor' => 'e4e4f0',
                                                'background' => 'e8e9eb', 
                                                'bodycontentfontColor' =>'e4e4f0',
                                                'bodycontentbacgroColor' =>'e8e9eb',
                                                'bodycontenttxture' =>'',
                                                'headerfontColor' =>'333',
                                                'headerbacgroColor' =>'fff',
                                                'headertxture' =>'',
                                                'contentbodyfontColor' => '333',
                                                'contentbodybacgroColor' => 'fff',
                                                'contentbodytxture' =>'',
                                                'bannerfreshimg' =>'dblogo-black.png',
                                                'footerfontColor' => '333',
                                                'footerbacgroColor' => 'd3d3d3',
                                                'footertxture' => '',
                                                'footerMsgval' => 'powered by db corporate social platforms'
                                               );
                    $bannerfreshimgdef ='dblogo-black.png';
                  
                    $bodyContentmsg  ='<table>
                                        <tr><td style="padding:0px 30px 30px 30px; ">
                                            <strong>Dear '.$params['username'].'</strong>,<br /> <br />
                                            Your account is now active and your login details are as follows:<br /> <br />
                                            <strong>Account Details:</strong><br/><br/>
                                            <strong>Email:</strong>'.$params['email'].'<br/>
                                            <strong>Password:</strong> what you set on sign up<br/><br/>                                           
                                            Best wishes<br/>
                                            db csp team
                                        </td></tr>
                                    </table>';
                     
                    $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]','[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]','[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
                       '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]');

                    $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],$bodyContentjsonval['bodycontenttxture'],$bodyContentjsonval['headerbacgroColor'],
                                   $bodyContentjsonval['headertxture'],$bannerfreshimgdef,$bodyContentjsonval['contentbodyfontColor'],
                                   $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],$bodyContentmsg,
                                   $bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],$bodyContentjsonval['footertxture'],
                                   $bodyContentjsonval['footerfontColor'],$footerContentmsg);  

                    $messagemail = str_replace($data1,$data2,$emailTemplatemain);
                    
                    $from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
                    $replyto = NOREPLY_MAIL;
                    $to = $params['email']; 
                    $setSubject = "Welcome administrator";
                    $setBodyText = html_entity_decode($messagemail);                                 
                    
                    $this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);

                    $data2= array(
                        'clientID' => clientID,
                        'front_userid' => $froUId,
                        'username' => $params['username'],
                        'name' => $params['username'],
                        'email' => $this->myclientdetails->customEncoding($params['email']),
                        'password'=> sha1($this->_options['auth']['salt'].trim($params['password'])),
                        'secretquestion' => $params['quetion'],
                        'answer' => $params['answer'],
                        'picture' =>$picture,
                        'id_role' =>3,
                        'adddate' =>date("Y-m-d H:i:s"),
                    );
                    $adminent = $user->insertdata_global('users',$data2);

                }
                else
                {
                   $this->_helper->flashMessenger->addMessage("Some thing going wrong, Please try again.");
                   $this->_redirect('admin/login/');
                }

                // echo "<pre>";print_r($data); exit;
                
                $options=array();
                $options['username'] = $this->myclientdetails->customEncoding($this->getRequest()->getParam('username'));
                $options['clientID']= clientID;
                $options['Status']= 1;
                $userresult = $this->myclientdetails->getAllMasterfromtable('tblUsers','*',$options);
                
                $options['password']= $this->getRequest()->getParam('password');

                $authNamespace = new Zend_Session_Namespace('identify');
                $authNamespace->setExpirationSeconds((1209600));
                $authNamespace->role = $userresult[0]['role'];

                $adapter = new Zend_Auth_Adapter_DbTable($db);
                $adapter->setTableName('tblUsers');
                $adapter->setIdentityColumn('Username');
                $adapter->setCredentialColumn('Pass');
                $adapter->setIdentity($options['username']);
                $chkPass = $adapter->setCredential($adapter->setCredentialdbee($options['password'], $userresult[0]['Pass']));
                $select = $adapter->getDbSelect();
                $select->where('clientID = ?',$options['clientID']);

                $result = $auth->authenticate($adapter);

                //echo $result->isValid(); exit;
                if ($result->isValid()) {
                    // update login time
                    $this->myclientdetails->updatedata_global('tblUsers',array('LastLoginDate'=>date('Y-m-d H:i:s')),'UserID',$userresult[0]['UserID']);
                    $authdta = $auth->getStorage()->write($userresult[0]);
                    $authNamespace->id = $userresult[0]['UserID'];
                    $authNamespace->user = $userresult[0]['Username'];
                    $authNamespace->lockedtogroup = $userresult[0]['groupid'];
        
                    $this->searchparam = new Zend_Session_Namespace();
                    if($this->searchparam->searchdb!="")
                    {
                      $searchdbvar=$this->searchparam->searchdb;
                      unset($this->searchparam->searchdb);
                      $this->searchparam->searchdb="";
                      $this->_redirect('/admin/dashboard/post/id/'.$searchdbvar);
                    }
                    else
                    {
                      if($userresult[0]['role']==''){
                         $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");
                         $this->_redirect('/admin/login');
                       }
                      else if($userresult[0]['role']!=1){
                       $Roledetailsval = new Admin_Model_Reporting();
                       $AllRolesdetailslist = $Roledetailsval->getRolesDetails($userresult[0]['role']); 
                                       
                       $modulefirst = $AllRolesdetailslist[0]['module'];
                       $controllerfirst = $AllRolesdetailslist[0]['controller']; 
                       $actionfirst = $AllRolesdetailslist[0]['action'];
                       $urlrole = "/".$modulefirst."/".$controllerfirst."/".$actionfirst;

                       if(empty($controllerfirst)){
                            $auth = Zend_Auth::getInstance();
                            $auth->clearIdentity();
                            //session_destroy();
                            $this->_helper->flashMessenger->addMessage("Sorry incorrect log in details.");      
                            $this->_redirect('/admin/login');
                       }
                       $this->_redirect($urlrole);
                       }else if($userresult[0]['role']==1){
                         $this->_redirect('/admin/index');
                       }

                    }
                } else {
                    $this->_helper->flashMessenger->addMessage("Sorry incorrect procedure followed.");
                    $this->_redirect('/admin/registration');
                }
            }
        } else
        {
            $this->view->form = new Adminlogin_Form_Registration($opt);
            $this->view->chkform  = 'registration';
            $this->render('registration'); 
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


     
    public function forgetpasswordAction()
    {
        $request    = $this->getRequest()->getParams();
        $db         = $this->getInvokeArg('bootstrap')->getResource('db');
        $usersdetail       = new Adminlogin_Model_Users($db);   
        $chkreg = $usersdetail->chkforregistration_tblUsers();


        if($chkreg<1)
        {
            $this->view->form = new Adminlogin_Form_Registration($opt);
            $this->_redirect('login/index');
        }
    
        $flash = $this->_helper->getHelper('flashMessenger');      
        if ($flash->hasMessages()) {
            $this->view->message = $flash->getMessages();
        }
     

        if($request['submit']=='SUBMIT' && $request['adminEmail']!='')
        {
            $result =  $usersdetail->getUserEmail($request['adminEmail']);
            //echo count($result ); exit;
            if(count($result )==1)
            {
                $this->view->okay = 1;
                $this->view->ques = $result[0]['secretquestion'];
                $this->view->id   = $result[0]['id'];
            }
            else
            {
                $this->_helper->flashMessenger->addMessage("Incorrect email");
                $this->_redirect('adminlogin/index/forgetpassword');
            }
            //exit;
        }
        if($request['answer']=='SUBMIT' && $request['myanswer']!='' && $request['myid']!='')
        {
            $myid = substr($request['myid'], 6); 
            $result =  $usersdetail->getUserbyId($myid);
            
            if(count($result )==1)
            {

                if(trim($result[0]['answer'])==trim($request['myanswer']))
                {
                    $rand = rand(45982, 99999);
                    //$store = $usersdetail->resetpasscode($result[0]['id'],$rand,$this->_options['auth']['salt']);
             
                    echo $this->myclientdetails->updatedata_global('tblUsers',array('Pass'=>$this->_generateHash($rand)),'UserID',$result[0]['front_userid']);

                   

                    $dbeeEmailtemplate = new RawEmailtemplate();
                    $emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplate();
                    // $deshboard   = new Admin_Model_Deshboard();
                    // $areatype ='admin';
                    // $bodyContent = $deshboard->getGroupemailtemplate($areatype);                    
                  
                    /*********/
                    $datasub1 = array('[$$COMPANY_NAME$$]');
                    $datasub2 = array(COMPANY_NAME);
                    $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
                    $footerContentmsg = $bodyContent[0]['footertext'];
                    /*********/
                    $emailTemplatejson = $this->myclientdetails->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates','id',1);
                    //echo'<pre>';print_r($emailTemplatejson);die;
                    
                    $bodyContentjsonval = Array(
                                                'fontColor' => 'e4e4f0',
                                                'background' => 'e8e9eb', 
                                                'bodycontentfontColor' =>'e4e4f0',
                                                'bodycontentbacgroColor' =>'e8e9eb',
                                                'bodycontenttxture' =>'',
                                                'headerfontColor' =>'333',
                                                'headerbacgroColor' =>'fff',
                                                'headertxture' =>'',
                                                'contentbodyfontColor' => '333',
                                                'contentbodybacgroColor' => 'fff',
                                                'contentbodytxture' =>'',
                                                'bannerfreshimg' =>'dblogo-black.png',
                                                'footerfontColor' => '333',
                                                'footerbacgroColor' => 'd3d3d3',
                                                'footertxture' => '',
                                                'footerMsgval' => 'powered by db corporate social platforms'
                                               );
                    $bannerfreshimgdef ='dblogo-black.png';
                   
                    $setBodyText ='<table>
                                    <tr>
                                <td style="padding:0px 30px 30px 30px; ">
                                <p style="padding-bottom:5px; border-bottom:1px solid #CCC;"></p><p><font size="2" face="Arial, Helvetica, sans-serif">Dear '.$result[0]['username'].',<br><br>
                                 Please find your new password below. We strongly suggest you change it under your account settings soon after logging in.
                                <br><br><a href="'.FRONT_URL.'/admin">'.FRONT_URL.'/admin</a>
                                <br><br>New password:<br>'.$rand.'
                                
                                <br /><br />
                                <br />'.COMPANY_FOOTERTEXT.'
                                </td>
                                </tr>
                        </table>';
                     $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]','[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]','[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
                       '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]');

                    $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],$bodyContentjsonval['bodycontenttxture'],$bodyContentjsonval['headerbacgroColor'],
                                   $bodyContentjsonval['headertxture'],$bannerfreshimgdef,$bodyContentjsonval['contentbodyfontColor'],
                                   $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],$setBodyText,
                                   $bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],$bodyContentjsonval['footertxture'],
                                   $bodyContentjsonval['footerfontColor'],$footerContentmsg);
                            $messagemail = str_replace($data1,$data2,$emailTemplatemain); 
                           
                            $from='"'.FromName.'" < '.NOREPLY_MAIL.' >'; //Give the Mail From Address Here
                            $replyto = NOREPLY_MAIL;                                  
                            $MailCharset = "iso-8859-1";
                            $MailEncoding = "8bit";                         
                            $headers  = "From: $from\n";
                            $headers .= "Reply-To: $replyto\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-type: text/html; charset=$MailCharset\n";
                            $headers .= "Content-Transfer-Encoding: $MailEncoding\n";
                            $headers .= "X-Mailer: PHP/".phpversion(); 

                            $to =$this->myclientdetails->customDecoding($result[0]['email']);    
                        /*** donot delete *********/
                           // $chk = $this->myclientdetails->sendWithoutSmtpMail($to,'Your password reset request',$headers, '-f'.NOREPLY_MAIL,$messagemail);

                          $chk = mail($to,'Your password reset request',$messagemail,$headers, '-f'.NOREPLY_MAIL);   
                           
                            if($chk)
                            {
                                 $this->_helper->flashMessenger->addMessage("An email has been sent to your registered email address with a temporary password.");
                                 $this->_redirect('admin/');
                            } else
                            {
                                 $this->_helper->flashMessenger->addMessage("something went wrong, please try again.");
                                 $this->_redirect('adminlogin/index/forgetpassword');
                            }
                }
                else
                {

                    $this->_helper->flashMessenger->addMessage("Given answer does not match with our records, please try again.");
                    $this->view->okay = 1;
                    $this->view->ques = $result[0]['secretquestion'];
                    $this->view->id   = $result[0]['id'];
                    $this->_redirect('adminlogin/index/forgetpassword');
                }
            }
            else
            {
                $this->_helper->flashMessenger->addMessage("Given answer does not match with our records, please try again.");
                $this->_redirect('adminlogin/index/forgetpassword');
            }
            
        }


    }

    public function recoverAction()
    {
        $request    = $this->getRequest()->getParams();
        $db         = $this->getInvokeArg('bootstrap')->getResource('db');
        $usersdetail       = new Login_Model_Users($db);   

        $flash = $this->_helper->getHelper('flashMessenger');      
        if ($flash->hasMessages()) {
            $this->view->message = $flash->getMessages();
        }

        if($request['submit']!='' && $request['recovercode']!='')
        {
            $chkcode = $usersdetail->checkresetcode($request['id'], $request['recovercode']);
            //if($chkcode=='')
        }
    }    
    /**
     * Logout
     */
    public function logoutAction() 
    {
        if(isset($_SERVER['HTTP_COOKIE'])) 
        {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
           
            foreach($cookies as $cookie) 
            {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
        $this->session_name_space = new  Zend_Session_Namespace('User_Session');
        $this->session_name_space->LinkedInLogined = '';
        $this->session_name_space->twitterLogined = '';

        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        session_destroy();
        $this->_redirect('login');
    }

    public function passAction() {
        echo $password=sha1($this->_options['auth']['salt'].'enrico');
        die($password);
    }
}

