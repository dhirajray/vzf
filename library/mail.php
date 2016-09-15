<?php

$conn = myowndbconnect();

$sql    = "SELECT  U.clientID,siteName,UserID, Name, Email, Signuptoken,smtpkey,
fromMail,fromName,frontUrl,noreplyEmail,companyFootertext,companyName FROM `tblUsers` AS U JOIN domainVariables AS C JOIN domainAPI AS D ON
C.clientID = U.clientID AND C.clientID = D.clientID WHERE `emailsent` = 0 AND fromcsv = 1 LIMIT 25";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        if ($row["UserID"] != '') {
            $mailsent = getemailtempate($row);
            if ($mailsent == true)
                mysqli_query($conn, "UPDATE tblUsers SET emailsent =1 WHERE UserID='" . $row["UserID"] . "' AND fromcsv = 1");
        }
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


function getemailtempate($row)
{
    $loginid     = customDecoding($row['Email']);
    $fname       = customDecoding($row["Name"]);
    $from        = $row['fromMail'];
    $Signuptoken = $row['Signuptoken'];
    GLOBAL $conn;
    $emailTemplatemain = dbeeEmailtemplate();
    
    $deshboard = "SELECT `emtemp`.* FROM `emailtemplates` AS `emtemp` WHERE (emtemp.areatype='admin') AND (emtemp.clientID ='" . $row['clientID'] . "')  AND (emtemp.active = '1')";
    
    $result      = $conn->query($deshboard);
    $bodyContent = $result->fetch_assoc();
    
    $databodymsg1 = array('[%%fname%%]','[%%BASE_URL%%]','[%%FRONT_URL%%]','[%%loginid%%]',
            '[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%Signuptoken%%]','[%%COMPANY_FOOTERTEXT%%]');
    /*$databodymsg1 = array(
        '[%%fname%%]',
        '[%%FRONT_URL%%]',
        '[%%loginid%%]',
        '[%%COMPANY_NAME%%]',
        '[%%Signuptoken%%]',
        '[%%COMPANY_FOOTERTEXT%%]'
    );*/
    $databodymsg2 = array(
        $fname,
        $row['frontUrl'],
        $row['frontUrl'],
        $loginid,
        $row['companyName'],
        $row['frontUrl'],
        $Signuptoken,
        $row['companyFootertext']
    );
    
    $bodyContentmsg = str_replace($databodymsg1, $databodymsg2, $bodyContent['body']);
    
    $datasub1 = array(
        '[%%COMPANY_NAME%%]'
    );
    $datasub2 = array(
        $row['companyName']
    );
    
    $subjectMsg = str_replace($datasub1, $datasub2, $bodyContent['subject']);
    
    $footerContentmsg = $bodyContent['footertext'];
    
    $data1       = array(
        '[%bannerfreshimg%]',
        '[%%body%%]',
        '[%%footertext%%]'
    );
    $data2       = array(
        '',
        $bodyContentmsg,
        $footerContentmsg
    );
    $messagemail = str_replace($data1, $data2, $emailTemplatemain);
    $setBodyText = html_entity_decode($messagemail);
    return swiftMail($loginid, $subjectMsg, $from, $setBodyText,$row['smtpkey'],$row['companyName']);
}

function dbeeEmailtemplate()
{
    return '<table width="100%"><tbody><tr>
                     <td style="padding-top:100px;padding-bottom:100px;background:#[%bodycontentbacgroColor%] url([%bodycontenttxture%]) repeat;" class="editingBlck" titleval="Body" editType="bodycontent">
                     <form id="mainForm">
                       <table width="623" style="background:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px" align="center" border="0" cellspacing="0" cellpadding="30">
                        <tbody>
                            <tr>
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url([%headertxture%]) repeat; padding-top:20px; padding-bottom:20px;"><a href="#"  ></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#[%contentbodyfontColor%]; padding:20px 30px 30px 30px; background:#[%contentbodybacgroColor%] url([%contentbodytxture%]) repeat;"  class="editingBlck bodymsg" titleval="Body container" editType="contentbody" id="bodymsg" >
                                   <div id="bodyEmailMsg" style="padding-top:10px">[%%body%%]</div>
                                 </td>
                            </tr>
                            <tr>
                                <td style="color:#[%footerfontColor%]; background:#[%footerbacgroColor%] url([%footertxture%]) repeat;" class="editingBlck" titleval="Footer" editType="footer">
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




