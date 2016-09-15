<?php
$conn = myowndbconnect();
$sql  = "SELECT  smtpkey,A.id,A.clientID,A.MessageTo,A.MessageFrom,A.user,A.useremail,A.emailchack,B.msg_subject,B.msg_body,C.frontUrl,C.noreplyEmail,C.companyFootertext,C.companyName FROM adminmsgemaildetail AS A JOIN adminmessageemail AS B ON  A.msgemailtable_id = B.msg_id AND B.emailtemplateid=1 JOIN domainVariables AS C JOIN domainAPI AS D ON C.clientID = A.clientID AND C.clientID = D.clientID WHERE A.emailchack = 1 LIMIT 50";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row    
    while ($row = $result->fetch_assoc()) {
        if ($row["useremail"] != '') {
            $mailsent = getemailtempate($row);
            if ($mailsent) {
                mysqli_query($conn, "UPDATE adminmsgemaildetail SET emailchack=0 WHERE useremail='" . $row["useremail"] . "' AND id='" . $row["id"] . "'");
            }
        }
    }
}
$conn->close();



function swiftMail($to, $setSubject, $from ='no-reply@onserro.com', $setBodyText,$smtpkey)
{
    require_once dirname(dirname(__FILE__)) .  '/library/lib/swift_required.php';
    $transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587);
    $transport->setUsername('adam@db-csp.com');
    $transport->setPassword($smtpkey);
    
    // Create the message
    $message = Swift_Message::newInstance();
    $message->setTo(array(
        $to => $tofrom
    ));
    //$message->setCc(array("a.derosa@audero.it" => "Aurelio De Rosa"));
   /* $message->setBcc(array(
        "porwal.deshbandhu@gmail.com" => "Bank Boss"
    ));*/

    $message->setSubject($setSubject);
    $message->setBody($setBodyText, 'text/html');
    $message->setFrom($from, 'db-csp');
    //$message->attach(Swift_Attachment::fromPath("path/to/file/file.zip"));
    
    // Send the email
    $mailer = Swift_Mailer::newInstance($transport);
    if (!$mailer->send($message, $failedRecipients)) {
        return false;
    } else {
        return true;
    }
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
    $conn   = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


function getemailtempate($row)
{
    $loginid = customDecoding($row['useremail']);
    $fname   = customDecoding($row["user"]);
    $from    = $row['MessageFrom'];
    $subject = $row['msg_subject'];
    $bodymsg = $row['msg_body'];
    
    $clientID          = $row['clientID'];
    $noreplyEmail      = $row['noreplyEmail'];
    $companyFootertext = $row['companyFootertext'];
    $companyName       = $row['companyName'];
    
    
    $EmailTemplateArray = array(
        'uEmail' => $loginid,
        'uName' => $fname,
        'message' => $bodymsg,
        'subject' => $subject,
        'clientID' => $clientID,
        'noreplyEmail' => $noreplyEmail,
        'companyFootertext' => $companyFootertext,
        'companyName' => $companyName,
        'case' => 4,
        'smtpkey' => $row['smtpkey'],
        'baseurl' => $row['frontUrl'],
    );
    return dbeeComparetemplateOne($EmailTemplateArray);
    
}


function dbeeComparetemplateOne($globalarr)
{
    // Create connection
    GLOBAL $conn;
    extract($globalarr);
    
    $deshboard   = "SELECT `emtemp`.* FROM `emailtemplates` AS `emtemp` WHERE (emtemp.areatype='admin') AND (emtemp.clientID = '" . $clientID . "') AND (emtemp.case = $case) AND (emtemp.active = '1')";
    //echo'<pre>';print_r($deshboard);die;
    $result      = $conn->query($deshboard);
    $bodyContent = $result->fetch_assoc();
    
    if ($case == '4') {
        if (empty($uName) || empty($message)) {
            $checkerror = '';
            if (empty($uName)) {
                $checkerror .= '$uName' . '#';
            }
            if (empty($message)) {
                $checkerror .= '$message' . '#';
            }
            $caseerror = 'case = 4';
        } else {
            $data1      = array(
                '[%%FIRST_NAME%%]',
                '[%%BODY_MESSAGE%%]',
                '[%%COMPANY_FOOTERTEXT%%]'
            );
            $data2      = array(
                $uName,
                $message,
                $companyFootertext
            );
            $datasub1   = array(
                '[%%subject%%]'
            );
            $datasub2   = array(
                $subject
            );
            $subjectMsg = str_replace($datasub1, $datasub2, $bodyContent['subject']);
        }
    }
    
    if (isset($checkerror)) {
        //echo $case;die;
    $errormessagemail = dbeeErrotemplate($checkerror, $caseerror, $case, $clientID, $companyFootertext, $noreplyEmail,$baseurl);
    } else {
        //echo'<pre>';print_r($data2);die;   
        $bodyContentmsg = str_replace($data1, $data2, $bodyContent['body']);
        //echo'<pre>';print_r($bodyContentmsg);die;
        $bodyContentmsg = html_entity_decode($bodyContentmsg);
        //echo'<pre>';print_r($bodyContentmsg);die;
        $messagemail    = dbeeComparetemplate($bodyContentmsg, $bodyContent['footertext'], $clientID,$baseurl);
    }
    
    return swiftMail($uEmail, $subjectMsg, $noreplyEmail, $messagemail,$smtpkey);
}

function dbeeErrotemplate($checkerror, $caseerror, $id, $clientID, $companyFootertext, $noreplyEmail,$baseurlval)
{
    // Create connection
    $conn        = myowndbconnect();
    $deshboard   = "SELECT `emtemp`.* FROM `emailtemplates` AS `emtemp` WHERE (emtemp.areatype='admin') AND (emtemp.clientID ='" . $clientID . "') AND (emtemp.case = $id) AND (emtemp.active = '1')";
    //echo'<pre>';print_r($deshboard);die;
    $result      = $conn->query($deshboard);
    $bodyContent = $result->fetch_assoc();
    //echo'<pre>';print_r($bodyContent);die;
    $subjectMsg  = 'Some variables value are missing';
    $uEmail      = 'abc@gmail.com';
    $bodyContentmsg .= "<table><tr>
                           <td style='padding:0px 30px 30px 30px;'>
                           <strong>These variables are not find</strong>,<br /> 
                           <br /><br />";
    $errorValues = explode('#', $checkerror);
    foreach ($errorValues as $value) {
        $bodyContentmsg .= "<font color='#666' style='color:#ff7709;font-size:24px;
                               display:block; width:550px; word-wrap:break-word;'>
                                 " . $value . "
                                </font> 
                                <br /><br />";
    }
    $bodyContentmsg .= "<p>and error find in " . $caseerror . "</p><br/>
                           " . $companyFootertext . "</td></tr></table>";
    $messagemail = dbeeComparetemplate($bodyContentmsg, $bodyContent['footertext'], $clientID,$baseurlval);
    //echo'<pre>';print_r($messagemail);die;     
    return sendWithoutSmtpMail($uEmail, $subjectMsg, $$noreplyEmail, $messagemail);
    
}

function dbeeComparetemplate($bodyContentmsg, $footerContentmsg, $clientID,$baseurlval)
{
    //echo'<pre>';print_r($bodyContentmsg);echo'<pre>';print_r($footerContentmsg);die;
    // Create connection
    $conn              = myowndbconnect();
    $emailTemplatemain = dbeeEmailtemplate($baseurlval);
    $deshboard         = "SELECT `emtemp`.* FROM `adminemailtemplates` AS `emtemp` WHERE (emtemp.clientID='" . $clientID . "')";
    //echo'<pre>';print_r($deshboard);die;
    $result            = $conn->query($deshboard);
    $emailTemplatejson = $result->fetch_assoc();
    //echo'<pre>';print_r($emailTemplatejson);die;
    $bodyContentjson   = $emailTemplatejson['emailtemplatejson'];
    
    if ($bodyContentjson == '') {
        $bodyContentjsonval = Array(
            'fontColor' => 'e4e4f0',
            'background' => 'e8e9eb',
            'bodycontentfontColor' => 'e4e4f0',
            'bodycontentbacgroColor' => 'e8e9eb',
            'bodycontenttxture' => '',
            'headerfontColor' => '333',
            'headerbacgroColor' => 'fff',
            'headertxture' => '',
            'contentbodyfontColor' => '333',
            'contentbodybacgroColor' => 'fff',
            'contentbodytxture' => '',
            'bannerfreshimg' => 'dblogo-black.png',
            'footerfontColor' => '333',
            'footerbacgroColor' => 'd3d3d3',
            'footertxture' => '',
            'footerMsgval' => 'powered by db corporate social platforms'
        );
        $bannerfreshimgdef  = 'dblogo-black.png';
    } else {
        $bodyContentjsonval = json_decode($bodyContentjson, true);
        //$bannerfreshimgdef ='http://www.db-csp.com/img/dblogo-black.png';
        $bannerfreshimgdef  = $bodyContentjsonval['bannerfreshimg'];
    }
    
    $data1 = array(
        '[%bodycontentbacgroColor%]',
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
    $data2 = array(
        $bodyContentjsonval['bodycontentbacgroColor'],
        $bodyContentjsonval['bodycontenttxture'],
        $bodyContentjsonval['headerbacgroColor'],
        $bodyContentjsonval['headertxture'],
        $bannerfreshimgdef,
        $bodyContentjsonval['contentbodyfontColor'],
        $bodyContentjsonval['contentbodybacgroColor'],
        $bodyContentjsonval['contentbodytxture'],
        $bodyContentmsg,
        $bodyContentjsonval['footerfontColor'],
        $bodyContentjsonval['footerbacgroColor'],
        $bodyContentjsonval['footertxture'],
        $bodyContentjsonval['footerfontColor'],
        $footerContentmsg
    );
    /*$sss = str_replace($data1,$data2,$emailTemplatemain);
    echo'<pre>';print_r($sss);die;*/
    return str_replace($data1, $data2, $emailTemplatemain);
}


function dbeeEmailtemplate($baseurlval)
{
    $actual_link = 'https://'.$_SERVER['HTTP_HOST'];
    $myImgurl = $baseurlval.'/adminraw/img/bgs/';
    $logoImgurl = $baseurlval.'/adminraw/img/emailbgimage/'; 
    return '<table width="100%"><tbody><tr>
                     <td style="padding-top:100px;padding-bottom:100px;background:#[%bodycontentbacgroColor%] url('.$myImgurl.'[%bodycontenttxture%]) repeat;" class="editingBlck" titleval="Body" editType="bodycontent">
                     <form id="mainForm">
                       <table width="623" style="background:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px" align="center" border="0" cellspacing="0" cellpadding="30">
                        <tbody>
                            <tr>
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url('.$myImgurl.'[%headertxture%]) repeat; padding-top:20px; padding-bottom:20px;"><a href="#"  ><img src="'.$logoImgurl.'[%bannerfreshimg%]" id="bannerHolder" border="0" style="display:inline-block" alt=""></a>
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
}


