<?php

class GlobalController extends Zend_Controller_Action
{
	public function init()
	{
		parent::init();
		$this->view->myclientdetails = $this->myclientdetails = new Admin_Model_Clientdetails();
	}

	public function refreshsessionAction()
	{
		echo  json_encode(array('Return'=> 'Session refreshed'));	exit;
	}

	public function logoutAction()
	{ 
		
		//echo "<pre>";
		//print_r($_SERVER['HTTP_REFERRER']);

		//print_r($_SESSION['Zend_Auth']['storage']['UserID']);

		
		 $domainip = gethostbyname('www.americangolf.co.uk');
		
		$request = $this->getRequest()->getParams();

		if($request['token']=='160a8c15bfa5de6473a222db80f849ae5a8a5b1c1d97078fa' )
		{

			if($_SESSION['Zend_Auth']['storage']['UserID']=='')
			{
				$this->_redirect('http://americangolf.co.uk');
				exit;
			}
			
			$this->_userid = $request['user'] = $_SESSION['Zend_Auth']['storage']['UserID'];


			if($request['user']!='' && $request['clearcache']==1)
			{
			 	$this->getResponse()->setHeader('Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0',1);
		        $this->getResponse()->setHeader('Expires','Thu, 19 Nov 1981 08:52:00 GMT',1);
		        $this->getResponse()->setHeader('Pragma','no-cache',1);

		        $myhome_obj  = new Application_Model_Myhome();
		        $data = array('newfeatures'=>0);
	            $sucess = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$this->_userid);
				
			}

			$this->myclientdetails->updatedata_global('tblUsers',array('chatstatus'=>0,'isonline'=>0),'UserID',$this->_userid);

			$this->myclientdetails->updatedata_global('tbluserlogindetails',array('logoutdate'=>date("Y-m-d H:i:s")),'userid',$this->_userid);
			
			$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
			
			if (isset($_SERVER['HTTP_COOKIE']))
			{
				$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
				foreach($cookies as $cookie) 
				{
					$parts = explode('=', $cookie);
					$name = trim($parts[0]);
					setcookie($name, '', time()-1000, '/','onserro.com');
				}
			}

			if(isSet($_COOKIE['RememberEmail']) && $_COOKIE['RememberEmail']!="" && isSet($_COOKIE['Rememberpass']) && $_COOKIE['Rememberpass']!="")
			{
			    setcookie("RememberEmail","", time()- 3600, '/');
			    setcookie("Rememberpass","", time()- 3600, '/');
			}

			Zend_Auth::getInstance()->clearIdentity();
			

			Zend_Session::namespaceUnset('User_AllowTC');
			unset($user_allowTC_session->allowTC);
			
			
			Zend_Session::namespaceUnset('application');
			$authNamespace = new Zend_Session_NameSpace('identify');

			$authNamespace->unsetAll();
			
			session_destroy();
			session_unset();
			
			$this->_redirect('http://americangolf.co.uk');
			//$this->_redirect('/');

			echo 1; exit;
		}
		else
		{
			echo  json_encode(array('Return'=> 'Invalid Token'));
			exit;
		}
		/********************facebook logout *********************/
		 
	}


	 
	
	public function npostemailAction()
    {

        
        //$posts = "SELECT GroupID,post.DbeeID,post.User  FROM `tblDbees` as post,`tblDbeeComments` as cmnt  WHERE post.DbeeID=cmnt.DbeeID  AND  post.GroupID!='' AND `LastActivity` > '2015-06-22 17:59:06'"
       
        $FollowingTable = new Application_Model_Following();
        $newdate =  date("Y-m-d h:i:s",strtotime ( '-1 day' , strtotime ( date("Y-m-d h:i:s") ) ) );

        //$follusers     = 'SELECT DISTINCT `tblFollows`.`User`, `u`.`UserID` AS `id`,u.full_name,u.Email  FROM `tblFollows` INNER JOIN `tblUsers` AS `u` ON u.UserID = FollowedBy  WHERE (User = 25) AND (u.clientID = '.clientID .') AND (u.usertype != 100) AND (u.hideuser != 1)';

        $chkPosts = 'SELECT DbeeID,User,clientID,dburl,`Text` from tblDbees where EmailCheck=0 AND PrivatePost=0 AND PostDate >"'.$newdate.'"';

        $postresult = $this->myclientdetails->passSQLquery($chkPosts );

        foreach ($postresult as $key => $value) {
      

            $follusers     = 'SELECT DISTINCT `tblFollows`.`User`, `u`.`UserID` AS `id`,u.full_name,u.Email,fns.full_name as fnsfullname  FROM `tblFollows` INNER JOIN `tblUsers` AS `u` ON u.UserID = FollowedBy INNER JOIN `tblNotificationSettings` AS `ns` ON ns.User = FollowedBy INNER JOIN `tblUsers` AS `fns` ON fns.UserID = tblFollows.User  WHERE (tblFollows.User = '.$value['User'] .') AND (u.clientID = '.$value['clientID'] .') AND ns.Dbees=1 AND (u.hideuser != 1)';

            $result = $this->myclientdetails->passSQLquery($follusers );
            
           // echo "<pre>"; print_r($result); exit;
            //echo $value['DbeeID'].' - '.count($result);
            foreach ($result as $key => $value2) {
                               
                $fullname = $this->myclientdetails->customDecoding($value2['full_name']);
                $myname   = $this->myclientdetails->customDecoding($value2['fnsfullname']);
                $emailid  = $this->myclientdetails->customDecoding($value2['Email']);
                
                $this->getemailtempate($myname,$fullname,$emailid,$value['Text'],$value['dburl'] );
            } 
            //exit;

        }
        
        exit;
    }

    public function getemailtempate($myname='',$dearname='' ,$emailid='',$posttxt='',$dburl='')
    {
        $emailTemplatemain = $this->dbeeEmailtemplate();       
        $deshboard   = new Admin_Model_Deshboard();
        $bodyContent = $deshboard->getGroupemailtemplate(); 
       
        $body = '';
        
         
        $MailSubject = "New post";
       
        $tesxtappent = "";
        
        $bodyContentmsg = 'Dear '.$dearname. ',<br><br>'.$tesxtappent.' <span style="color:#999;font-size:16px;line-height:20px">'.$posttxt.'</span><br><br>';//<a href="'.BASE_URL.'/dbee/'.$dburl.'">Go to post</a>

       
        $footerContentmsg = $bodyContent[0]['footertext'];
       
      
        $emailTemplatejson = $this->myclientdetails->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates');

        $bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
        $bodyContentjsonval = json_decode($bodyContentjson, true);
        $data1 = array('[%bodycontentbacgroColor%]',
                       '[%bodycontenttxture%]',
                       '[%headerbacgroColor%]',
                       '[%headertxture%]',
                       '[%bannerfreshimg%]',
                       '[%contentbodyfontColor%]',
                       '[%contentbodybacgroColor%]',
                       '[%contentbodytxture%]',
                       '[%%body%%]',
                       '[%footerfontColor%]',
                       '[%footerbacgroColor%]',
                       '[%footertxture%]',
                       '[%footerfontColor%]',
                       '[%%footertext%%]'
                       );

        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],
                       $bodyContentjsonval['bodycontenttxture'],
                       $bodyContentjsonval['headerbacgroColor'],
                       $bodyContentjsonval['headertxture'],
                       $bodyContentjsonval['bannerfreshimg'],
                       $bodyContentjsonval['contentbodyfontColor'],
                       $bodyContentjsonval['contentbodybacgroColor'],
                       $bodyContentjsonval['contentbodytxture'],
                       $bodyContentmsg,
                       $bodyContentjsonval['footerfontColor'],
                       $bodyContentjsonval['footerbacgroColor'],
                       $bodyContentjsonval['footertxture'],
                       $bodyContentjsonval['footerfontColor'],
                       $footerContentmsg);
        
        $messagemail = str_replace($data1,$data2,$emailTemplatemain);
        $MailFrom=SITE_NAME; //Give the Mail From Address Here
        $MailReplyTo=NOREPLY_MAIL;
        $MailTo     =   $emailid;      
        $MailBody = html_entity_decode(str_replace("'","\'",$messagemail));
        echo $MailBody;
        echo $MailSubject;
        echo $MailFrom; //exit;
      
        //$this->myclientdetails->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$MailBody);
        return true;          

    } 

    public function dbeeEmailtemplate(){
                $myImgurl = FRONT_URL.'/adminraw/img/bgs/';
                $logoImgurl = FRONT_URL.'/adminraw/img/emailbgimage/';
                $emailTemplate ='<table width="100%"><tbody><tr>
                     <td style="padding-top:100px;padding-bottom:100px;background:#[%bodycontentbacgroColor%] url('.$myImgurl.'[%bodycontenttxture%]) repeat;" class="editingBlck" titleval="Body" editType="bodycontent">
                     <form id="mainForm">
                       <table width="623" style="background:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px" align="center" border="0" cellspacing="0" cellpadding="30">
                        <tbody>
                            <tr>
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url('.$myImgurl.'[%headertxture%]) repeat; padding-top:20px; padding-bottom:20px;"><a href="#"  ><img src="'.$logoImgurl.'[%bannerfreshimg%]" id="bannerHolder" border="0" style="display:inline-block" alt="dbee logo"></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#[%contentbodyfontColor%]; padding:20px 30px 30px 30px; background:#[%contentbodybacgroColor%] url('.$myImgurl.'[%contentbodytxture%]) repeat;"  class="editingBlck bodymsg" titleval="Body container" editType="contentbody" id="bodymsg" >
                                   <div id="bodyEmailMsg" style="padding-top:10px">[%%body%%]</div>
                                 </td>
                            </tr>
                            <tr>
                                <td style="color:#[%footerfontColor%]; background:#[%footerbacgroColor%] url('.$myImgurl.'[%footertxture%]) repeat;" class="editingBlck" titleval="Footer" editType="footer">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div align="right"><a href="http://www.db-csp.com" style="text-decoration:none; color:#[%footerfontColor%]" target="_blank"  editType="footercontent" id="footerMsg">
                                                        [%%footertext%%]
                                                     </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                       </table>
                     </form>    
                     </td></tr>
                     </tbody></table>';
        return $emailTemplate;
    } 

   

    public function newsletterAction()
    {
        //$posts = "SELECT GroupID,post.DbeeID,post.User  FROM `tblDbees` as post,`tblDbeeComments` as cmnt  WHERE post.DbeeID=cmnt.DbeeID  AND  post.GroupID!='' AND `LastActivity` > '2015-06-22 17:59:06'"
        
        $newdate =  date("Y-m-d h:i:s",strtotime ( '-1 month' , strtotime ( date("Y-m-d h:i:s") ) ) );
        
        $cmnts = "SELECT DISTINCT  GroupID  FROM `tblDbees` WHERE GroupID!='' AND `LastActivity` > '".$newdate."' AND Active=1 AND clientId=".clientID;
       
        $postGrps = "SELECT DISTINCT GroupID  FROM `tblDbees` as post,`tblDbeeComments` as cmnt,`tblGroups` as grup WHERE post.GroupID= grup.ID AND post.DbeeID=cmnt.DbeeID  AND  post.GroupID!='' AND `LastActivity` > '".$newdate."' AND post.clientId=".clientID;

        $cmntGrps = $this->myclientdetails->passSQLquery($cmnts);
        $postGrps = $this->myclientdetails->passSQLquery($postGrps);

        $groups   =  $cmntGrps+$postGrps;

        $tmpArr = array();
        foreach ($groups as $sub) {
          $tmpArr[] = implode(',', $sub);
        }
         echo "<br><br> Gorop ids : ".$grpids = implode(',', $tmpArr);
        

        $result;
        $cmnts = "SELECT DISTINCT User,ProfilePic,full_name,Email,gm.GroupID  FROM `tblGroupMembers`as gm,`tblUsers` as user WHERE gm.User=user.UserID AND gm.`GroupID` IN ($grpids) AND gm.Status=1 AND user.Status=1 AND user.clientId=".clientID." group by user.UserID";

        $mailingUsers = $this->myclientdetails->passSQLquery($cmnts);
        $uRec = '';

        //echo "<br><br>Emails will send to these users only-- ";
        foreach ($mailingUsers as $key => $value) {
            //echo "<pre>";
            //echo $uRec = '<br><br>'.$value['User'].' | '.$this->myclientdetails->customDecoding($value['full_name']).' | '.$this->myclientdetails->customDecoding($value['Email']).' | '.$value['GroupID'];
            $selGrpsUsers =$this->getGroupDetails($value['User'],$value['GroupID']);
            $selCmnsUsers =$this->getCommentDetails($value['User'],$value['GroupID']);

            $fullname = $this->myclientdetails->customDecoding($value['full_name']);
            $myname   = '';
            $emailid  = $this->myclientdetails->customDecoding($value['Email']);
            
            $comentandgroup =   $selGrpsUsers.$selCmnsUsers;     
            if($comentandgroup!='') $this->getemailtempate($myname,$fullname,$emailid,$comentandgroup,'' );

             
            // print_r($selGrpsUsers);
        }
        //echo "<pre>"; echo "<br><br> Commenting users : ";print_r($cmntGrps);echo "<br><br> Posts users : ";print_r($postGrps);echo "<br><br> Combined users : ";print_r($groups);
        exit;
    }
    public function getCommentDetails($userid,$groupid)
    {
        $selCmnts = "SELECT DISTINCT GroupID,GroupName  FROM `tblDbees` as post,`tblDbeeComments` as cmnt,`tblGroups` as grup WHERE post.GroupID= grup.ID AND post.DbeeID=cmnt.DbeeID AND cmnt.UserID='".$userid."' AND post.Type=1 AND post.clientId=".clientID." group by cmnt.DbeeID order by cmnt.CommentDate desc  ";       
      
        $selCmnts = $this->myclientdetails->passSQLquery($selCmnts);

        // print_r($selCmnts); //exit;
        if(count($selCmnts)>0){
            $ret = ''; 
            $ret .= '<br> ******************************** Comment Email *********************************';           
            foreach ($selCmnts as $key => $value) {
                $ret .= '<br><br>Group Name : '.$value['GroupName'];
                
                $selcntss = "SELECT CommentID,Comment,GroupID,post.DbeeID,post.text  FROM `tblDbees` as post,`tblDbeeComments` as cmnt WHERE  post.DbeeID=cmnt.DbeeID AND post.GroupID='".$value['GroupID']."' AND post.Type=1 AND post.clientId=".clientID."  order by cmnt.CommentDate desc  ";
                $selcntss = $this->myclientdetails->passSQLquery($selcntss);
                $pc = 1;
                //print_r($selcntss); //exit;
                foreach ($selcntss as $key => $value) {
                    $ret .= '<br>Post:"'.$value['text'].'" Comment : '.$value['Comment'];
                    $pc++;

                    if($pc>3) break;
                }

            }
            return $ret;
        }
        else
        {
            //return '<br><br>No Comment';
        }
    }
    public function getGroupDetails($userid,$groupid)
    {
       $selGrps = "SELECT  post.GroupID,grup.GroupName,post.Text,post.DbeeID,post.LastActivity  FROM `tblDbees` as post, `tblGroups` as grup WHERE post.GroupID= grup.ID   AND  post.GroupID!='' AND post.`User` = '".$userid."' AND post.clientId=".clientID." group by post.GroupID order by GroupDate desc";
        $selGrpsUsers = $this->myclientdetails->passSQLquery($selGrps);

        // print_r($selGrpsUsers);
        if(count($selGrpsUsers)>0){
            $ret = '';
            $pb = 1;
            $ret .= '<br> ******************************** Group Email *********************************';
            foreach ($selGrpsUsers as $key => $value) {
                $ret .= '<br><br>Group Name : '.$value['GroupName'];
                $selPostss = "SELECT post.Text  FROM `tblDbees` as post WHERE post.GroupID = '".$value['GroupID']."' AND post.clientId=".clientID." order by LastActivity desc";
                $selPostss = $this->myclientdetails->passSQLquery($selPostss);
                $pc = 1;
                foreach ($selPostss as $key => $value) {
                    $ret .= '<br>Post Name : '.$value['Text'];
                    $pc++;

                    if($pc>3) break;
                }
                $pb++;
                if($pb>3) break;

            }
            return $ret;
        }
        else
        {
            //return '<br><br>No groups';
        }
    }
	

}
