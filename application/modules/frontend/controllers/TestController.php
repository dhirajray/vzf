<?php
class TestController extends Zend_Controller_Action
{  

	public function init()
	{
        parent::init();
        $this->myclientdetails = new Application_Model_Clientdetails();
        // echo $this->make_clickable('Since 2008, we have been committed to unleashing the power of the link. Based in New York City, we shorten nearly half a billion links per month as an integral part of social, SMS, email efforts (and more) from brands, marketers, publishers, government http://stackoverflow.com/questions/910912/extract-urls-from-text-in-php organizations, non-profits and individual users. Processing more than eight billion clicks on those links per month, we continue to work to unveil new https://bitly.com/pages/about organizations products while generating one of the most valuable proprietary datasets in the world today.'); die;
	}
    public function shortUrl($longUrl)
    {
        // set HTTP header
        $headers = array(
            'Content-Type: application/json',
        );
        $postData = array('format'=>'json','longUrl' => $longUrl, 'access_token' => "adf0ecca6d54059cafc4be154d2bd200d545ddcf");
        $url = 'https://api-ssl.bitly.com/v3/shorten?' . http_build_query($postData);
        //echo $url; exit;
        // Open connection
        $ch = curl_init();

        // Set the url, number of GET vars, GET data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Execute request
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Close connection
        curl_close($ch);
        if ($status == 200) 
        {
            $resultdata = json_decode($result, true);
            return $resultdata['data']['url'];
        }
         else{
            return $longUrl;
        }
    }
    public function make_clickable($text) 
    {
        /*$regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
        return preg_replace_callback($regex, function ($matches) {
            return $this->shortUrl($matches[0]);
        }, $text);*/
    }

    public function getuseridAction()
    {
        $uAr = 'j.m.brewitt@hotmail.co.uk,sgsb201@exeter.ac.uk,owlbreeze@hotmail.com,n.callan-graves@consultuscare.com,shan69uk@yahoo.com,g.dickson@consultuscare.com,charlieandallanguy@gmial.com,sueparker87@hotmail.co.uk,jodietaylor96@ymail.com,janepearce124@gmail.com,chantelle.jacobs@hotmail.co.uk,myrianthi.lambrou@gmail.com,brendalake2000@yahoo.com,corinneelt@yahoo.co.uk,fanner101@mweb.co.za,anne.h.fairey@gmail.com,lfackerell@aol.com,shannieto@hotmail.co.uk,michellejane01@yahoo.co.uk,edwards.marge@googlemail.com,michelle.eadygosling@9online.fr,ushka_davies@hotmail.com,janedauth@yahoo.co.uk,mandy11@sfr.fr,mchant.daniel1@gmail.com,artgrove@zol.co.zw,sue.aiff@gmail.com,naomiahrens@yahoo.com,caablitt@gmail.com,benkeeley88@gmail.com,hayden@barkweb.co.uk,stefmurtagh@yahoo.co.uk,sylviabevis@yahoo.co.uk,emilygarvey@hotmail.com,aleks@barkweb.co.uk,p.astill@consultuscare.com,adam@db-csp.com,stephaniemcoyne@gmail.com,OperationsDirector@consultuscare.com,ben@barkweb.co.uk,c.seal@consultuscare.com,CarerDevelopmentManager@consultuscare.com,NHDManager@consultuscare.com,p.seldon@consultuscare.com,heatherdebruyn140@gmail.com,margaret_travel@hotmail.com,lynnerosierose@btinternet.com,louisegg@live.co.za,choochooandgeorgeali@gmail.com,summerbreeze56@yahoo.co.uk,keenancolleen18@gmail.com,sarah.kentish@btinternet.com';

        $uarry = explode(',', $uAr);
        $uEmail = '';
        $uidsl = '';
        foreach ($uarry as $key => $value) {
            $uEmail = $this->myclientdetails->customEncoding($value);
            $allids = $this->myclientdetails->passSQLquery('select UserID from tblUsers where Email ="'.$uEmail.'"');
            $uidsl  = $allids[0]['UserID'].',';
        }
        echo $uidsl;
        print_r($uarry);
        exit;
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
        
        $bodyContentmsg = 'Dear '.$dearname. ',<br><br>'.$tesxtappent.'<br><br> <span style="color:#999;font-size:16px;line-height:20px">'.$posttxt.'</span><br><br><a href="'.BASE_URL.'/dbee/'.$dburl.'">Go to post</a>';

       
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

   

    public function nletAction()
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

            $this->getemailtempate($myname,$fullname,$emailid,$comentandgroup,'' );

             
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
    public function soapAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $wsdl = "http://webservicetest.consultuscare.com:9595/ConsultusWebService.asmx?wsdl";
            $passphrase = 'livephrase';
            $soapClient = new SoapClient($wsdl);
            $theResponse = $soapClient->test();
            
            $params = array(
              "Connection" => 'CarerLive',
              "PassPhrase" => $passphrase
            );
            
            $response1 = $soapClient->__soapCall("GetToken", array($params));

            $params = array(
              "Connection" => 'CarerLive',
              "CarerId" => 50859,
              "Token" => $response1->GetTokenResult->Token
            );
            
            $response = $soapClient->__soapCall("GetCarerBookingList", array($params));
            $WSCarerBookingData = array();
            $WSCarerBooking = $response->GetCarerBookingListResult->BookingList->WSCarerBooking;
            $i = 0;
            foreach ($WSCarerBooking as $key => $value) 
            {
               $WSCarerBookingData[$i]['BookingId'] = $value->BookingId;
               $WSCarerBookingData[$i]['ClientAccountId'] = $value->ClientAccountId;
               $WSCarerBookingData[$i]['ClientId'] = $value->ClientId;
               $WSCarerBookingData[$i]['AccountName'] = $value->AccountName;
               $WSCarerBookingData[$i]['StartDate'] = $value->StartDate;
               $WSCarerBookingData[$i]['EndDate'] = $value->EndDate;
               $WSCarerBookingData[$i]['ClientTitle'] = $value->ClientTitle;
               $WSCarerBookingData[$i]['ClientSurname'] = $value->ClientSurname;
               $WSCarerBookingData[$i]['ClientFirstname'] = $value->ClientFirstname;
               $WSCarerBookingData[$i]['ClientProfile'] = $value->ClientProfile;
               $WSCarerBookingData[$i]['ClientAddressLine1'] = $value->ClientAddressLine1;
               $WSCarerBookingData[$i]['ClientAddressLine2'] = $value->ClientAddressLine2;
               $WSCarerBookingData[$i]['ClientAddressLine3'] = $value->ClientAddressLine3;
               $WSCarerBookingData[$i]['ClientAddressLine4'] = $value->ClientAddressLine4;
               $WSCarerBookingData[$i]['ClientPostCode'] = $value->ClientPostCode;
               $WSCarerBookingData[$i]['ClientHomePhone'] = $value->ClientHomePhone;
               $WSCarerBookingData[$i]['ClientDOB'] = $value->ClientDOB;
               $i++;
            }
            print_r($WSCarerBookingData);
            die;
        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }


    public function soap1Action()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo "<pre>";
        $wsdl = "http://webservicetest.consultuscare.com:9595/ConsultusWebService.asmx?wsdl";
        
        $soapClient = new SoapClient($wsdl);
        
        $params = array(
          "Connection" => 'Dev',
          "CarerId" => 125
        );
        
        $response = $soapClient->__soapCall("GetCarerBookingList", array($params));

        print_r($response);
        die;
    }

    function csvexplode($str, $delim = ',', $qual = "\"")
    // Explode a single CSV string (line) into an array.
    {
        $len = strlen($str);  // Store the complete length of the string for easy reference.
        $inside = false;  // Maintain state when we're inside quoted elements.
        $lastWasDelim = false;  // Maintain state if we just started a new element.
        $word = '';  // Accumulator for current element.

        for($i = 0; $i < $len; ++$i)
        {
            // We're outside a quoted element, and the current char is a field delimiter.
            if(!$inside && $str[$i]==$delim)
            {
                $out[] = $word;
                $word = '';
                $lastWasDelim = true;
            } 

            // We're inside a quoted element, the current char is a qualifier, and the next char is a qualifier.
            elseif($inside && $str[$i]==$qual && ($i<$len && $str[$i+1]==$qual))
            {
                $word .= $qual;  // Add one qual into the element,
                ++$i; // Then skip ahead to the next non-qual char.
            }

            // The current char is a qualifier (so we're either entering or leaving a quoted element.)
            elseif ($str[$i] == $qual)
            {
                $inside = !$inside;
            }

            // We're outside a quoted element, the current char is whitespace and the 'last' char was a delimiter.
            elseif( !$inside && ($str[$i]==" ")  && $lastWasDelim)
            {
                // Just skip the char because it's leading whitespace in front of an element.
            }

            // Outside a quoted element, the current char is whitespace, the "next" char is a delimiter.
            elseif(!$inside && ($str[$i]==" ")  )
            {
                // Look ahead for the next non-whitespace char.
                $lookAhead = $i+1;
                while(($lookAhead < $len) && ($str[$lookAhead] == " ")) 
                {
                    ++$lookAhead;
                }

                // If the next char is formatting, we're dealing with trailing whitespace.
                if($str[$lookAhead] == $delim || $str[$lookAhead] == $qual) 
                {
                    $i = $lookAhead-1;  // Jump the pointer ahead to right before the delimiter or qualifier.
                }

                // Otherwise we're still in the middle of an element, so add the whitespace to the output.
                else
                {
                    $word .= $str[$i];  
                }
            }

            // If all else fails, add the character to the current element.
            else
            {
                $word .= $str[$i];
                $lastWasDelim = false;
            }
        }

        $out[] = $word;
        return $out;
    }
	
	public function indexAction() 
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $csvInput = 'Name,Address,Phone
        Alice,123 First Street,"555-555-5555"
        Bob,"345 Second Place,   City  ST",666-666-6666
        "Charlie ""Chuck"" Doe",   3rd Circle   ,"  777-777-7777"';
        echo '<pre>';
        // explode() emulates file() in this context.
        foreach(explode("\n", $csvInput) as $line)
        {
            print_r($this->csvexplode($line));
        }
    }

    public function smsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $sms = new Custom_Smsapi_Easysmsv2();
        print_r($sms->sendsms('Dbee','00447989989370','Hello sir sms api test',1,'','prod'));
        exit;
    }

    public function encodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $fieldname = $this->_request->getPost('fieldname');
        $fieldname2 = $this->_request->getPost('fieldname2');
        if($fieldname){
            $this->view->fieldname = $this->myclientdetails->customEncoding($this->_request->getPost('fieldname'));
            $this->view->fieldnamePost = $fieldname; 
        }
        else{
            $this->view->fieldname = '';
            $this->view->fieldnamePost = ''; 
        }
         if($fieldname2){
            $this->view->fieldname2 = $this->myclientdetails->customDecoding($this->_request->getPost('fieldname2'));
            $this->view->fieldnamePost2 = $fieldname2; 
        }
        else{
            $this->view->fieldname2 = '';
            $this->view->fieldnamePost2='';
        }
    }

    public function followtouserchatAction(){
        //echo'remove it';die;
        $myhomesearch = new Application_Model_Myhome();
        $queryall = $myhomesearch->getfollowUsersdet();
        $this->following  = new Application_Model_Following();
        $followedby = array();
        foreach($queryall as $key => $val){ 
            $this->following->chkChatUsers($val['User'],$val['FollowedBy']);
        }
        exit;
    }


    public function phpinfAction(){
        phpinfo();
    }
    

}