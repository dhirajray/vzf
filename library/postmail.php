<?php

$conn = myowndbconnect();

$newdate =  date("Y-m-d h:i:s",strtotime ( '-3 day' , strtotime ( date("Y-m-d h:i:s") ) ) );

$chkPosts = 'SELECT DbeeID,User,U.clientID,dburl,`Text`,smtpkey,
fromMail,fromName,emailUrl,frontUrl,noreplyEmail,companyFootertext,companyName from tblDbees as U JOIN domainVariables AS C JOIN domainAPI AS D ON C.clientID = U.clientID AND C.clientID = D.clientID where EmailCheck=0 AND PrivatePost=0 AND PostDate >"'.$newdate.'"';
$postresult = $conn->query($chkPosts );

if ($postresult->num_rows > 0) {
    while ($value = $postresult->fetch_assoc()){

        $follusers     = 'SELECT DISTINCT `tblFollows`.`User`, `u`.`UserID` AS `id`,u.full_name,u.Email,fns.full_name as fnsfullname  FROM `tblFollows` INNER JOIN `tblUsers` AS `u` ON u.UserID = FollowedBy INNER JOIN `tblNotificationSettings` AS `ns` ON ns.User = FollowedBy INNER JOIN `tblUsers` AS `fns` ON fns.UserID = tblFollows.User  WHERE (tblFollows.User = '.$value['User'] .') AND (u.clientID = '.$value['clientID'] .') AND ns.Dbees=1 AND (u.hideuser != 1)';

        $result = $conn->query($follusers );
        
       
        //echo $value['DbeeID'].' - '.count($result);
        while ($value2 = $result->fetch_assoc()){
            // echo "<pre>"; print_r($value2); exit;              
            $fullname = customDecoding($value2['full_name']);
            $myname   = customDecoding($value2['fnsfullname']);
            $emailid  = customDecoding($value2['Email']);

            $mailsent = getemailtempate($myname,$fullname,$emailid,$value );;
            if ($mailsent == true)
                mysqli_query($conn, "UPDATE tblDbees SET EmailCheck =1 WHERE DbeeID='" . $value["DbeeID"] . "' ");
            
            
        } 
        //exit;

    }
}
$conn->close();

function swiftMail($to, $setSubject, $from ='no-reply@onserro.com', $setBodyText,$smtpkey,$companyName='OnSerro')
{
    require_once dirname(dirname(__FILE__)).'/library/lib/swift_required.php';
    $transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587);
    $transport->setUsername('adam@db-csp.com');
    $transport->setPassword($smtpkey);
    
    // Create the message
    $message = Swift_Message::newInstance();
    $message->setTo(array(
        $to => $tofrom
    ));
    //$message->setCc(array("a.derosa@audero.it" => "Aurelio De Rosa"));
    /*$message->setBcc(array(
        "porwal.deshbandhu@gmail.com" => "Bank Boss"
    ));*/

    $message->setSubject($setSubject);
    $message->setBody($setBodyText, 'text/html');
    $message->setFrom($from, $companyName);
    //$message->attach(Swift_Attachment::fromPath("path/to/file/file.zip"));
    
    $mailer = Swift_Mailer::newInstance($transport);
    if (!$mailer->send($message, $failedRecipients)) {
        return false;
    } else {
        return true;
    }
}

function getemailtempate($myname='',$dearname='' ,$emailid='',$postArray)
{
    $emailTemplatemain = dbeeEmailtemplate($postArray['emailUrl']);       
    global $conn;
    $body = '';
             
    $MailSubject = "New post";
   
    $tesxtappent = $myname." started a new post.";
    
    $bodyContentmsg = 'Dear '.$dearname. ',<br><br>'.$tesxtappent.'<br><br> <span style="color:#999;font-size:16px;line-height:20px">'.$postArray['Text'].'</span><br><br><a href="'.BASE_URL.'/dbee/'.$dburl.'">Go to post</a>';

    $ftrque ='SELECT `emtemp`.* FROM `emailtemplates` AS `emtemp` WHERE (emtemp.areatype="admin") AND (emtemp.clientID = '. $postArray['clientID'] .') AND (emtemp.active = 1) ORDER BY `case` asc';

    $bodyContent = $conn->query($ftrque);
   
    $bodyContent = $bodyContent->fetch_assoc();
    
    //echo "<pre>"; print_r($bodyContent); exit;
    $footerContentmsg = $bodyContent['footertext'];
   
    $deshboard = "SELECT id,emailtemplatejson,htmllayout from adminemailtemplates WHERE clientID ='" . $postArray['clientID'] . "'";
    
    $result      = $conn->query($deshboard);
    $emailTemplatejson = $result->fetch_assoc();

    $bodyContentjson = $emailTemplatejson['emailtemplatejson'];
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
    
    $messagemail    =   str_replace($data1,$data2,$emailTemplatemain);
    $MailFrom       =   'no-reply@onserro.com'; //Give the Mail From Address Here
    $MailReplyTo    =   'no-reply@onserro.com';
    $MailTo         =   $emailid;      
    $MailBody = html_entity_decode(str_replace("'","\'",$messagemail));
  
    /*echo $MailBody;
    echo $MailSubject;
    echo $MailFrom; //exit;*/
  
    return $this->swiftMail($MailTo,$MailSubject,$MailFrom,$MailBody,$postArray['smtpkey']);
    //return true;          

}

function dbeeEmailtemplate($url){
    $myImgurl = $url.'/adminraw/img/bgs/';
    $logoImgurl = $url.'/adminraw/img/emailbgimage/';
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

function customDecoding($cryptokey)
{
    if ($cryptokey == '')
        return false;
    $expl           = explode("nqebnzobg", trim($cryptokey));
    $originalString = '';
    foreach ($expl as $key => $value)
        $originalString .= $value . ' ';
    $retuOrg = explode("mabirdnny", trim($originalString));
    return str_rot13($retuOrg[0]);
}
function myowndbconnect()
{
    $servername = 'dbserver';
    $username = 'clientdev_usr';
    $password = 'gKCAQrZJhV';
    $dbname = 'dbcsp_clientdev';
    $conn       = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
/*function myowndbconnect()
{
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'db_revamp';
    $conn       = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}*/


