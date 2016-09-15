<?php

class Admin_MessageController extends IsadminController
{

  private $options;
  
  /**
   * Init
   * 
   * @see Zend_Controller_Action::init()
   */

    public function init()
    { 
      $deshboard   = new Admin_Model_Deshboard();    
      $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
      parent::init();
    }

    

    /**  * Message Controller    */
    public function indexAction()
    {
       $request = $this->getRequest()->getParams();
       $deshboard   = new Admin_Model_Deshboard();
       /*****public group list******/
       $publicGroups = $deshboard->getGroupPrivacylistgroup(1);
       if(count($publicGroups)>0) $this->view->publicGroups   = $publicGroups;
       else $this->view->publicGroups   = array( '0' => array( 'ID' => '0','GroupName' =>'no users' ) );
       $this->view->publicGroupsCnt = count($publicGroups);

       $privateGroups = $deshboard->getGroupPrivacylistgroup(2);
         if(count($privateGroups)>0) $this->view->privateGroups   = $privateGroups;
       else $this->view->privateGroups   = array( '0' => array( 'ID' => '0','GroupName' =>'no users' ) );
       $this->view->privateGroupsCnt = count($privateGroups);
        
       $requestGroups = $deshboard->getGroupPrivacylistgroup(3);
      if(count($requestGroups)>0) $this->view->requestGroups   = $requestGroups;
       else $this->view->requestGroups   = array( '0' => array( 'ID' => '0','GroupName' =>'no users' ) );
       $this->view->requestGroupsCnt = count($requestGroups);
        

       $siteuserGroups = $deshboard->listsiteusergroupmsg(); 
       $this->view->siteuserGroups   = $siteuserGroups;
       $this->view->siteuserGroupsCnt = count($siteuserGroups);
       /*$this->view->siteuserGroupsCnt = 0;*/

       $allEventlist = $deshboard->eventlistall();
       $this->view->allEventlists   = $allEventlist;
       $this->view->allEventlistCnt = count($allEventlist);
       //echo'<pre>';print_r($this->view->allEventlists);die;
       $filter = new Zend_Filter_StripTags();
       $groupsendmsgval = $filter->filter($this->_request->getPost('groupsendmsgval'));
       $groupsendemailval = $filter->filter($this->_request->getPost('groupsendemailval'));
        if($groupsendemailval=='groupsendemail'){exit();}
        $Groupemailtemplate = $deshboard->getGroupemailtemplate();
        $this->view->keywordshow = $Groupemailtemplate;

        $usertypeobj   = new Admin_Model_Deshboard();
        /****user type****/
        $typeid = array(o,6,100);
        $this->view->usertype = $usertypeobj->filterusertype($typeid);
        /****user type****/
        /****user details****/
        $this->view->userdetails = $usertypeobj->filteruserdetails();
        /****user details****/
        //echo'<pre>';print_r($this->view->usertype);
    }

    public function reportAction()
    {
        require_once 'src/Mandrill.php';
        $mandrill = new Mandrill(smtpkey);
        $result = $mandrill->users->info();

        foreach ($result['stats']['today'] as $key => $value) 
        {
          if($key!='complaints' && $key!='unsubs' && $key!='hard_bounces' && $key!='soft_bounces'){
            $providersData[] = array('y'=> (int)$value);
            $nameArr[]   = array(ucwords(str_replace("_"," ",$key)));
          }
        }
        $this->view->providerscategory   = json_encode($nameArr);
        $this->view->providersData     = json_encode($providersData);
    }

    public function ajaxreportAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            require_once 'src/Mandrill.php';
            $mandrill = new Mandrill(smtpkey);
            $result = $mandrill->users->info();
            $type = $this->_request->getPost('type');
            foreach ($result['stats'][$type] as $key => $value) 
            { 
              if($key!='complaints' && $key!='unsubs' && $key!='hard_bounces' && $key!='soft_bounces'){
                $providersData[] = array('y'=> (int)$value);
                $nameArr[]   = array(ucwords(str_replace("_"," ",$key)));
              }
            }
            $data['providerscategory']   = json_encode($nameArr);
            $data['providersData']     = json_encode($providersData);
        }
        echo $data['providerscategory'].'~'.$data['providersData'];
    }
   public function messagelistAction(){
      $request = $this->getRequest()->getParams();
      //echo'<pre>';print_r($request);
      $filter = new Zend_Filter_StripTags();   
      $deshboard   = new Admin_Model_Deshboard();
      $this->_helper->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      $msg_type = $filter->filter($this->_request->getPost('msg_type'));
      $emailtemplateid = $filter->filter($this->_request->getPost('emailtemplateid'));
      /*******for testing *****/
      /*******for testing *****/
      $msgalluserslist = $deshboard->alluserslist($msg_type,$emailtemplateid);

      //echo "<pre>"; print_r($msgalluserslist); exit;
      //echo'<pre>';print_r($msgalluserslist);exit;
                         if(($msg_type=='1')&&($emailtemplateid=='0')){
                            $sendmsg = 'sendmsg_allusermsg';
                            $msgTypeMethodResendval='0';
                            $showtable='1';
                         }else if($msg_type=='1'){
                            $sendmsg = 'resendmsg_allusermsgemail';
                            $msgTypeMethodResendval='1';
                            $showtable='1';                       
                         }else if(($msg_type=='2')&&($emailtemplateid=='0')){
                            $sendmsg = 'sendmsg_alladmingroupusermsg';
                            $msgTypeMethodResendval='0';
                            $showtable='1';
                         } else if($msg_type=='2'){
                            $sendmsg = 'sendmsg_alladmingroupuseremail';
                            $msgTypeMethodResendval='1';
                            $showtable='1';
                         }else if(($msg_type=='3')&&($emailtemplateid=='0')){
                            $sendmsg = 'sendmsg_alladmingroupusermsg';
                            $msgTypeMethodResendval='0';
                            $showtable='1';
                         }else if($msg_type=='3'){
                            $sendmsg = 'sendmsg_alladmingroupusermsgemail';
                            $msgTypeMethodResendval='1';
                            $showtable='1';
                         }else if(($msg_type=='4')&&($emailtemplateid=='0')){
                            $sendmsg = 'sendmsg_allsearchusermsg';
                            $msgTypeMethodResendval='0';
                            $showtable='1';
                         }else if($msg_type=='4'){
                            $sendmsg = 'sendmsg_allsearchusermsgemail';
                            $msgTypeMethodResendval='1';
                            $showtable='1';
                         }
                         else if(($msg_type=='5')&&($emailtemplateid=='0')){
                            $sendmsg = 'sendmsg_alleventmsg';
                            $msgTypeMethodResendval='0';
                            $showtable='1';
                         }else if($msg_type=='5'){
                            $sendmsg = 'sendmsg_alleventmsgemail';
                            $msgTypeMethodResendval='1';
                            $showtable='1';
                         }

                         else{
                             $showtable='0';
                         }
                            
                         if($showtable='1'){
                          if(sizeof($msgalluserslist)>0){
                         if($msg_type==1){
                         $content = '<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe"><thead>      
                                      <tr><td colspan="8"><div class="rpGraphTop clearfix">Previous Messages</div></td></tr>
                                      <tr>
                                      <td style="width: 20%;">Subject</td>
                                      <td style="width: 32%;">Message</td>
                                      <td style="width: 15%;">Sent to</td>
                                      <td style="width: 10%;">User Count</td>
                                      <td style="width: 8%;">Sent Date</td>
                                      <td style="width: 5%;">Action</td>
                                      <td style="width: 5%;">Copy</td>
                                      <td style="width: 5%;">Delete</td>
                                      </tr>
                                      <tbody>';
                         } 
                         elseif($msg_type==5){   
                         $content = '<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe"><thead>      
                                      <tr><td colspan="8"><div class="rpGraphTop clearfix">Previous Messages</div></td></tr>
                                      <tr><td style="width: 20%;">Subject</td><td style="width: 40%;">Message</td><td style="width: 20%;">Event</td><td style="width: 10%;">User Count</td><td style="width: 10%;">Sent Date</td><td style="width: 5%;">Action</td><td style="width: 5%;">Copy</td><td style="width: 5%;">Delete</td></tr>
                                      <tbody>'; 
                           }  
                         elseif($msg_type==4){
                         $content = '<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe"><thead>      
                                      <tr><td colspan="8"><div class="rpGraphTop clearfix">Previous Messages</div></td></tr>
                                      <tr><td style="width: 25%;">Subject</td><td style="width: 45%;">Message</td><td style="width: 10%;">User Count</td><td style="width: 10%;">Sent Date</td><td style="width: 5%;">Action</td><td style="width: 5%;">Copy</td><td style="width: 5%;">Delete</td></tr>
                                      <tbody>';
                         }else{   
                         $content = '<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe"><thead>      
                                      <tr><td colspan="8"><div class="rpGraphTop clearfix">Previous Messages</div></td></tr>
                                      <tr><td style="width: 20%;">Subject</td><td style="width: 40%;">Message</td><td style="width: 20%;">Last Sent To</td><td style="width: 10%;">User Count</td><td style="width: 10%;">Sent Date</td><td style="width: 5%;">Action</td><td style="width: 5%;">Copy</td><td style="width: 5%;">Delete</td></tr>
                                      <tbody>'; 
                           }            
                          }            
                         foreach ($msgalluserslist as $Row):
                          //echo'<pre>';print_r($Row);
                          if($msg_type==1){
                                $raws = explode("<br>",$Row['referenceval']);
                                $raws = array_map("trim", $raws);
                                $keyraws = array();
                                foreach($raws as $currentraw)
                                {
                                    list($key, $value) = explode(" :", $currentraw);
                                    $keyraws[$key] = $value;
                                } 
                          
                          if($Row['emailtemplateid']==0){$siteplateform='admin';}else{$siteplateform='front';}
                          if($Row['usertype']==0){$usertypeval='';}else{$usertypeval=$Row['usertype'];}
                          $msgCount = $deshboard->countmsgemaildetail($Row['msg_id']);
                          $viewusers =$msgCount."<a href='javascript:void(0);' class='viewusersBtn' msgid=".$Row['msg_id']." >View</a>";
                            $content .= "<tr class='msgalluserslist'><td>". $Row['msg_subject']."</td><td><div>".$Row['msg_body']."</div></td><td>".$Row['referenceval']."</td>
                                       <td>".$viewusers."</td><td>".date('d-m-Y', strtotime($Row['msg_postdate']))."</td><td><button class='btn btn-mini btn-green reSendBtn' resend='Resend'  siteplateform='".$Row['siteplateform']."'  usertypeval='".$usertypeval."~".$keyraws['usertype']."'  jobtitleval='".$keyraws['Job title']."'  companyval='".$keyraws['Company']."'   resendvalue='".$sendmsg."'  msgTypeMethodResend='".$msgTypeMethodResendval."'  msg_id='".$Row['msg_id']."'  msg_type='".$Row['msg_type']."'  subjectval='".htmlentities($Row['msg_subject'])."' messageval='".htmlentities($Row['msg_body'])."'  msg_type_id='".$Row['msg_type_id']."' emailtemplateid='".$Row['emailtemplateid']."' >Resend</button>
                                        </td><td><button class='btn btn-mini btn-green copymsgemail'  subjectval='".htmlentities($Row['msg_subject'])."'   messageval='".htmlentities($Row['msg_body'])."'>Copy</button>
                                        </td><td><button class='btn btn-mini btn-green deleteBtn'  messageval='".$Row['msg_id']."' msg_type='".$Row['msg_type']."'  msg_type_id='".$Row['msg_type_id']."' emailtemplateid='".$Row['emailtemplateid']."'>Delete</button>
                                        </td></tr>";           
                          }if($msg_type==2){
                          $msgCount = $deshboard->countmsgemaildetail($Row['msg_id']);
                          $viewusers =$msgCount."<a href='javascript:void(0);' class='viewusersBtn' msgid=".$Row['msg_id']." >View</a>";
                          $content .= "<tr class='msgalluserslist'><td>". $Row['msg_subject']."</td><td><div>".$Row['msg_body']."</div></td><td>".$Row['ugname']."</td>
                                       <td>".$viewusers."</td>
                                       <td>".date('d-m-Y', strtotime($Row['msg_postdate']))."</td>
                                       <td><button class='btn btn-mini btn-green reSendBtn' resend='Resend' resendvalue=".$sendmsg." msgTypeMethodResend=".$msgTypeMethodResendval."  msg_id=".$Row['msg_id']."  msg_type=".$Row['msg_type']."  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']." > Resend</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green copymsgemail'  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'> Copy</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green deleteBtn'   
                                        messageval='".$Row['msg_id']."' msg_type=".$Row['msg_type']."  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']."> Delete</button>
                                        </td>                           
                                       </tr>";
                          }
                          if($msg_type==3){
                             $msgCount = $deshboard->countmsgemaildetail($Row['msg_id']);
                             $viewusers =$msgCount."<a href='javascript:void(0);' class='viewusersBtn' msgid=".$Row['msg_id']." >View</a>";
                             if($Row['GroupPrivacy']==1){$gprivacy='Public';}if($Row['GroupPrivacy']==2){$gprivacy='Private';}if($Row['GroupPrivacy']==3){$gprivacy='Request';} 
                          $content .= "<tr class='msgalluserslist'><td>". $Row['msg_subject']."</td><td><div>".$Row['msg_body']."</div></td><td>".$Row['GroupName']." - <span style='color:red;font-size:12px'>".$gprivacy."</span></td>
                                       <td>".$viewusers."</td>
                                       <td>".date('d-m-Y', strtotime($Row['msg_postdate']))."</td>
                                       <td><button class='btn btn-mini btn-green reSendBtn' resend='Resend' resendvalue=".$sendmsg." msgTypeMethodResend=".$msgTypeMethodResendval."  msg_id=".$Row['msg_id']." msg_type=".$Row['msg_type']."  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']." > Resend</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green copymsgemail'  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'> Copy</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green deleteBtn'   
                                        messageval='".$Row['msg_id']."' msg_type=".$Row['msg_type']."  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']."> Delete</button>
                                        </td>                           
                                       </tr>";
                          }
                          if($msg_type==4){
                          $msgCount = $deshboard->countmsgemaildetail($Row['msg_id']);
                          $viewusers =$msgCount."<a href='javascript:void(0);' class='viewusersBtn' msgid=".$Row['msg_id']." >View</a>";
                          $content .= "<tr class='msgalluserslist'><td>". $Row['msg_subject']."</td><td><div>".$Row['msg_body']."</div></td>
                                       <td>".$viewusers."</td>
                                       <td>".date('d-m-Y', strtotime($Row['msg_postdate']))."</td>
                                       <td><button class='btn btn-mini btn-green reSendBtn' resend='Resend' resendvalue=".$sendmsg." msgTypeMethodResend=".$msgTypeMethodResendval."  msg_id=".$Row['msg_id']." msg_type=".$Row['msg_type']."  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']." > Resend</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green copymsgemail'  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'> Copy</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green deleteBtn'   
                                        messageval='".$Row['msg_id']."' msg_type=".$Row['msg_type']."  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']."> Delete</button>
                                        </td>                           
                                       </tr>";
                          }  

                          if($msg_type==5){
                          
                          $msgCount = $deshboard->countmsgemaildetail($Row['msg_id']);
                          $viewusers =$msgCount."<a href='javascript:void(0);' class='viewusersBtn' msgid=".$Row['msg_id']." >View</a>";
                          $eventname = $deshboard->getEventdetails($Row['msg_type_id']);
                          //echo'<pre>';print_r($eventname);die;
                          $content .= "<tr class='msgalluserslist'><td>". $Row['msg_subject']."</td><td><div>".$Row['msg_body']."</div></td><td>".$eventname[0]['title']."</td>
                                       <td>".$viewusers."</td>
                                       <td>".date('d-m-Y', strtotime($Row['msg_postdate']))."</td>
                                       <td><button class='btn btn-mini btn-green reSendBtn' resend='Resend' resendvalue=".$sendmsg." msgTypeMethodResend=".$msgTypeMethodResendval."  msg_id=".$Row['msg_id']."  msg_type=".$Row['msg_type']."  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']." > Resend</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green copymsgemail'  subjectval='".htmlentities($Row['msg_subject'])."' 
                                        messageval='".htmlentities($Row['msg_body'])."'> Copy</button>
                                        </td>
                                        <td><button class='btn btn-mini btn-green deleteBtn'   
                                        messageval='".$Row['msg_id']."' msg_type=".$Row['msg_type']."  msg_type_id=".$Row['msg_type_id']." emailtemplateid=".$Row['emailtemplateid']."> Delete</button>
                                        </td>                           
                                       </tr>";
                          }           
                        endforeach;
                        }
        $content .= '</tbody></table></div>';
        print_r($content);exit();
    }
 


    public function templatemailAction(){
      $request = $this->getRequest()->getParams();
      $filter = new Zend_Filter_StripTags();   
      $deshboard   = new Admin_Model_Deshboard();
      $this->_helper->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $groupsendmsgval = $filter->filter($this->_request->getPost('groupsendmsgval'));
      $groupsendemailval = $filter->filter($this->_request->getPost('groupsendemailval'));

      if($groupsendemailval=='sendemail'){
        $Groupemailtemplate = $deshboard->getGroupemailtemplate('groupemail');
        $this->view->msgbody = $Groupemailtemplate[0]['body'];
        /***donot delete .it's using **********/
                print_r($this->view->msgbody);exit();
        /***donot delete .it's using **********/
      }

    }

    public function  messageemailAction()
    {
        $request = $this->getRequest()->getParams();
        //echo'<pre>';print_r($request);die;
        $newchainparent=0;
        $cnt = 0;
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();

        $filter = new Zend_Filter_StripTags();

            $subject      = $filter->filter(stripslashes($this->_request->getPost('subject')));
            $message      = nl2br($filter->filter(stripslashes($this->_request->getPost('message'))));
            $msg_type     = $filter->filter(stripslashes($this->_request->getPost('msg_type')));
            $emailtemplateid = $filter->filter(stripslashes($this->_request->getPost('selecttempId'))); 
            $sendmsgval   = $filter->filter(stripslashes($this->_request->getPost('msgTypeMethod')));
            $msg_type_id  = $filter->filter(stripslashes($this->_request->getPost('msg_type_id')));
            $userlist  = $this->_request->getPost('userlist');
            $msg_id  = $filter->filter(stripslashes($this->_request->getPost('msg_id')));
            /********search*******/
            $siteplateform  = $filter->filter(stripslashes($this->_request->getPost('siteplateform')));
            $usertypeval  = $filter->filter(stripslashes($this->_request->getPost('usertypeval')));
            $companyval  = $filter->filter(stripslashes($this->_request->getPost('companyval')));
            $jobtitleval  = $filter->filter(stripslashes($this->_request->getPost('jobtitleval')));
            $usertypeval = explode("~",$usertypeval);
            //echo'<pre>';print_r($siteplateform);
            if($siteplateform=='admin'){
              $usertype = $usertypeval[0];
              $company =$companyval;
              $title =$jobtitleval;
              if(isset($usertypeval[1]) && $usertypeval[1]!='Search user type' && $usertypeval[1]!=''){$referenceval .= 'usertype :<span style="color:#bdbdbd">'.$usertypeval[1].'</span><br>';}
              //elseif(isset($usertypeval[0]) && $usertypeval[1]!='Search User type' && $usertypeval[0]!=''){$referenceval .= 'usertype-'.$usertypeval[0].'<br>';}
              elseif($usertypeval[0]==0 && $usertypeval[1]==''){$referenceval .='';}
              if($company){$referenceval .= 'Company: <span style="color:#bdbdbd">'.$company.'</span><br>';}
              if($title){$referenceval .= 'Job title: <span style="color:#bdbdbd">'.$title.'</span><br>';}
              if($company=='' && $title=='' && $usertypeval[1]==''){
                  $referenceval .='<span style="color:#bdbdbd">All users - Standard</span><br>';
              }
            }
            if($siteplateform=='front'){
              $usertype = array(100);
              $company =$companyval;
              $title =$jobtitleval;
              /*if($company){$referenceval .= 'company-'.$company;}
              if($title){$referenceval .= ',jobtitle-'.$title;}*/
              if($company){$referenceval .= 'Company: <span style="color:#bdbdbd">'.$company.'</span><br>';}
              if($title){$referenceval .= 'Job title: <span style="color:#bdbdbd">'.$title.'</span><br>';}
              if($company=='' && $title=='' && $usertypeval[1]==''){
                  $referenceval .='<span style="color:#bdbdbd">All users - Vip</span><br>';
              }
            }

            /********search*******/
            //echo $usertype;die;
            $limit  = $filter->filter(stripslashes($this->_request->getPost('limit')));
            $offset  = $filter->filter(stripslashes($this->_request->getPost('offsetrange')));
            $lastinsidfromemail = $filter->filter(stripslashes($this->_request->getPost('lastinsidfromemail')));
            $adminMsg = array();
            $adminMsg['msg_body']     =  $message;
            $adminMsg['msg_subject']  =  $subject;
            $adminMsg['msg_type']     =  $msg_type;
            $adminMsg['msg_type_id']  =  $msg_type_id;
            $adminMsg['status']       =  '0';
            $adminMsg['emailtemplateid'] =  $emailtemplateid;
            $adminMsg['msg_postdate']  =  date('Y-m-d H:i:s');
            $adminMsg['clientID']  =  clientID;
            $adminMsg['siteplateform']  = $siteplateform;
            $adminMsg['usertype']= $usertypeval[0];
           

            if($msg_type==1){$adminMsg['referenceval']=$referenceval;}

            $deshboard    = new Admin_Model_Deshboard();
            $this->socialInvite = new Admin_Model_Social();
            
            if($msg_type==1){
                //echo'<pre>';print_r($usertype);die('===i am');
                $useralldetail = $deshboard->getusersdetailData($sendmsgval,$limit,$offset,$usertype,$company,$title);
                $userallcount = $deshboard->getusersdetailData($sendmsgval,'','',$usertype,$company,$title);
             }
             else if($msg_type==2){
                $userallcount = $deshboard->getadmingroupdetailData($msg_type_id);
                $useralldetail = $deshboard->getadmingroupdetailData($msg_type_id,$sendmsgval,$limit,$offset);
             }
             else if($msg_type==3){
                $userallcount = $deshboard->getusersgroupdetailData($msg_type_id);
                $useralldetail = $deshboard->getusersgroupdetailData($msg_type_id,$sendmsgval,$limit,$offset);
             }
             else if($msg_type==5){
                //echo'here';die;
                $userallcount = $deshboard->getuserseventData($msg_type_id);
                //echo'<pre>';print_r($userallcount);die;
                $useralldetail = $deshboard->getuserseventData($msg_type_id,$sendmsgval,$limit,$offset);
             }
             else if($msg_type==4){
                 //echo $msg_type;die;
                if($request['userlist']!='0'){
                  
                  if($emailtemplateid=='1')
                  {
                        $abc='';
                        $emailecheckmy='false';
                        if(is_numeric($request['userlist'][0])==true){
                          //echo '1';die;
                          $emailecheckmy='false'; 
                        }else{
                          
                          foreach($request['userlist'] as $key => $value ){
                              //echo'<pre>';print_r($value);die;
                           if (eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+.([a-zA-Z]{2,4})$', $value)){  
                                $emailecheckmy='true';  
                              } else {  
                                $emailecheckmy='false';  
                              }
                            if($emailecheckmy=='true'){
                              $abc.="'".$this->myclientdetails->customEncoding($value)."',";
                              $where = 'Email';
                            }

                          }
                         
                        }
                        if($emailecheckmy=='false'){
                          $userSearchlist = $request['userlist'];
                          $where = 'UserID';
                        }
                        if($emailecheckmy=='true'){
                          $userSearchlist = rtrim($abc,",");
                        }
                        $useralldetail = $deshboard->getfieldsfromtableusingin('*','tblUsers',$where,$userSearchlist);
                  }
                  
                  if($emailtemplateid==0){                   
                      
                      $idoremail = (is_numeric($request['userlist'][0]) == 1)? 'UserID' :'Email';
                      if(is_array($request['userlist'])!=1){$userSearchlist = $request['userlist'];}
                      else{$userSearchlist = '"'.$request['userlist'][0].'"';}
                      $useralldetail = $deshboard->getfieldsfromtableusingin('*','tblUsers',$idoremail,$userSearchlist);
                      }
                  }
                  if($msg_id){
                        $userresenddetail = $deshboard->selectresend($msg_id);
                        $o=0;
                        $resenduserid = array();
                        foreach($userresenddetail  as $key => $value){
                         $resenduserid[$o]= $value['MessageTo'];
                         $o++;
                        }
                        $userSearchlist = implode(',',$resenduserid);
                        $useralldetail = $deshboard->getfieldsfromtableusingin('*','tblUsers','UserID',$userSearchlist);
                  }                                              
             } 
            
            if(count($useralldetail)>0)
            {
                /***********@ for message *********/
               
                if($sendmsgval=='0' || $sendmsgval==''){ 

                      $datamsg  = array();
                      $adminID  = $this->adminUserID;
                    
                      $x=0;
                      $countval = count($useralldetail);

                      $msg_insertid = $deshboard->insertdata_global('adminmessageemail',$adminMsg);
                      
                      foreach($useralldetail as $key => $value){
                        if(!empty($value['UserID'])){

                          
                          /***************chaninparent********************/
                          $chaninparent_obj = $this->socialInvite->checkchainparent($adminID,$value['UserID']);

                         
                          if($chaninparent_obj['ID']) {   
                          $Chainparent=$chaninparent_obj['ID'];
                          } else $Chainparent=0;
                          /***************chaninparent********************/
                          $datamsg['Message'] =  $message;
                          $datamsg['MessageTo'] =  $value['UserID'];
                          $datamsg['MessageFrom'] =  $adminID;
                          $datamsg['MessageDate'] =  date('Y-m-d H:i:s');
                          $datamsg['Chainparent'] =  $newchainparent;
                          $datamsg['Fromadmin'] =  1;
                          $datamsg['clientID'] =  clientID;
                          
                          $msg_insertid_msg = $deshboard->insertgroup_msgdata($datamsg);
                          if($msg_insertid_msg){
                            $msgdetail['msgemailtable_id']= $msg_insertid;
                            $msgdetail['MessageTo']= $value['UserID'];
                            $msgdetail['MessageFrom']= $adminID;
                            $msgdetail['msg_postdate'] = date('Y-m-d H:i:s');
                            $msgdetail['user']= $value['Name'];
                            $msgdetail['useremail']= $value['Email'];
                            $msgdetail['clientID'] =  clientID;
                            $msgdetail['emailchack']='0';
                            
                            //if($msg_type==1){$msgdetail['referenceval']=$referenceval;}                         
                            $msg_insertdetail = $deshboard->insertgroup_msgdetail($msgdetail);
                            
                          }                        
                          $this->socialInvite->commomInsert('10','10',$msg_insertid_msg,$adminID,$value['UserID']);                   
                          if($cnt==0){                        
                              echo  $newchainparent = $msg_insertid_msg;                                                      
                            }
                        }
                        $cnt++;
                      }
                    
                    echo($msg_insertid);exit();             
                }  /***********@ for message email *********/ 
                else if($sendmsgval=='1'){

                    
                      $finalgroupdetail = array();
                      $msgdetail = array();
                      $adminID = $this->adminUserID; 
                      $x=0;
                      
                      if($offset==0 || $offset=='')
                      {
                        
                        $msg_insertid = $deshboard->insertdata_global('adminmessageemail',$adminMsg);
                      } 
                      $mainmsgid = ($lastinsidfromemail=='') ? $msg_insertid :$lastinsidfromemail;

                      foreach($useralldetail as $key => $value){
                      
                              /****for email ****/
                            $msgdetail['msgemailtable_id']= $mainmsgid;                   
                            $msgdetail['MessageTo']= $value['UserID'];
                            $msgdetail['MessageFrom']= $adminID;
                            $msgdetail['msg_postdate'] = date('Y-m-d H:i:s');
                            $msgdetail['user']= $value['Name'];
                            $msgdetail['useremail']= $value['Email'];
                            $msgdetail['clientID']= clientID;
                            $msgdetail['emailchack']='1'; 
                            //if($msg_type==1){$msgdetail['referenceval']=$referenceval;}
                                                       
                            $msg_insertdetail = $deshboard->insertdata_global('adminmsgemaildetail',$msgdetail);
                            $x++;              
                      }                                         

                      $totus = (count($userallcount )!='') ? count($userallcount ) : 1;

                      
                      $bunch = ($totus/$limit);
                      $offset = ($limit+$offset);
                      $persantage = ($offset/$totus)*100;
                      $remaining = $totus-$offset;

                      if($offset<0) $offset = 0;
                      echo $remaining.'~'.$offset."~".intval($persantage)."~".$mainmsgid;exit();
                }
                /***********@ for message email end*********/
                
            }
            else 
            {
              echo 'nouser';exit;
            }  

      
        
       return $response->setBody(Zend_Json::encode($data));
    } 

    public function emailtemplate($message,$to,$subject)
    {
        $mail  = new Zend_Mail();
        $rawat = new Zend_Mail_Transport_Sendmail('-fnoreplay@dbee.me');
        Zend_Mail::setDefaultTransport($rawat);
        $mail->setBodyHtml(html_entity_decode($message));
        $mail->setFrom(NOREPLY_MAIL,FromName);
        $mail->addTo($to);
        $mail->setSubject($subject);
        $mail->send();
    }

    public function deletemessageemailAction(){
        $request = $this->getRequest()->getParams();
        $msgemailtableid = $request['msgemailtable_id'];
        $deleteMsgemail = $this->deshboard->deletedata_global('adminmessageemail','msg_id',$msgemailtableid);
        $deleteMsgemaildetail = $this->deshboard->deletedata_global('adminmsgemaildetail','msgemailtable_id',$msgemailtableid);
        echo'deltrue';exit;
    }

    public function searchusersAction(){
        $request = $this->getRequest()->getParams();
        
        $this->_helper->viewRenderer->setNoRender(true);
        $param = $request["q"];
        $searchuser = $this->deshboard->searchcomusers($param);
        echo $searchuser;die;
    }

    public function getmsgemailuserlistAction()
    {
        $data = array();
        $usergroupobj= new Admin_Model_User();
        $common = new Admin_Model_Common();
        $request = $this->getRequest()->getParams();
        //echo'<pre>';print_r($request);die;
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {                        
            $content ="<ul>";
                $ulist = $this->deshboard->msgemailuseriddetail($request['msgid']);
                if(count($ulist)>0){
                    foreach($ulist as $row):
                        $checkliveDbpic = $common->checkImgExist($row['ProfilePic'],'userpics','default-avatar.jpg'); 
                    $content.='<li class="listTagProfile">
                        <div class="listUserPhoto show_details_user" userid="'.$row['UserID'].'">
                            <a  href="javascript:void(0)"><img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="85" height="85" border="0" class="recievedUsePic"/></a>
                        </div>
                        <div class="details_user oneline">'.$this->myclientdetails->customDecoding($row['Name']).'</div>
                        <div class="details_user oneline"><a  href="javascript:void(0)" class="graycolor"> @'.$this->myclientdetails->customDecoding($row['Username']).'</a></div>
                        
                        </li>';
                    endforeach;
                }
            
            
            $content.="</ul>";
            $data['content']= $content;
            $data['message']="fetced";
        }
    
        return $response->setBody(Zend_Json::encode($data));
    
    }

}    

