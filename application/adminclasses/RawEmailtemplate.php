<?php
class RawEmailtemplate {

    public function dbeeEmailtemplate(){
    $myImgurl = BASE_URL.'/adminraw/img/bgs/';
    $logoImgurl = BASE_URL.'/adminraw/img/emailbgimage/';  
    $emailTemplate ='<table width="100%"><tbody><tr>
                     <td style="padding-top:100px;padding-bottom:100px;background:#[%bodycontentbacgroColor%] url('.$myImgurl.'[%bodycontenttxture%]) repeat;" class="editingBlck" titleval="Body" editType="bodycontent">
                     <form id="mainForm">
                       <table width="623" style="background:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px" align="center" border="0" cellspacing="0" cellpadding="30">
                        <tbody>
                            <tr>
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url('.$myImgurl.'[%headertxture%]) repeat;"><a href="#"  ><img src="'.$logoImgurl.'[%bannerfreshimg%]" id="bannerHolder" border="0" style="display:inline-block" alt="dbee logo"></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#[%contentbodyfontColor%]; padding:0px 30px 30px 30px; background:#[%contentbodybacgroColor%] url('.$myImgurl.'[%contentbodytxture%]) repeat;"  class="editingBlck bodymsg" titleval="Body container" editType="contentbody" id="bodymsg" >
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


    public function dbeeEmailtemplatestatic(){
    $myImgurl = BASE_URL.'/adminraw/img/bgs/';
    $logoImgurl = BASE_URL.'/adminraw/img/emailbgimage/';   
    $emailTemplate ='<table width="100%"><tbody><tr>
                     <td style="padding-top:100px;padding-bottom:100px;background:#[%bodycontentbacgroColor%] url('.$myImgurl.'[%bodycontenttxture%]) repeat;" class="editingBlck" titleval="Body" editType="bodycontent">
                     <form id="mainForm">
                       <table width="623" style="background:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px" align="center" border="0" cellspacing="0" cellpadding="30">
                        <tbody>
                            <tr>
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url('.$myImgurl.'[%headertxture%]) repeat;"><a href="#"  ></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#[%contentbodyfontColor%]; padding:0px 30px 30px 30px; background:#[%contentbodybacgroColor%] url('.$myImgurl.'[%contentbodytxture%]) repeat;"  class="editingBlck bodymsg" titleval="Body container" editType="contentbody" id="bodymsg" >
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

    /*public function dbeeComparetemplateOne($fname='',$loginid='',$Signuptoken='',$id=''){
        $deshboard   = new Admin_Model_Deshboard();
        $bodyContent = $deshboard->getGroupemailtemplate();
        switch ($id) {
            case '0':
                $data1 = array('[$$fname$$]','[$$FRONT_URL$$]','[$$FRONT_URL$$]','[$$loginid$$]',
                                      '[$$COMPANY_NAME$$]','[$$FRONT_URL$$]','[$$Signuptoken$$]',
                                      '[$$COMPANY_FOOTERTEXT$$]');
                $data2 = array($fname,FRONT_URL,FRONT_URL,$loginid,COMPANY_NAME,FRONT_URL,
                                       $Signuptoken,FRONT_URL,COMPANY_FOOTERTEXT); 
                $datasub1 = array('[$$COMPANY_NAME$$]');
                $datasub2 = array(COMPANY_NAME);
                $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$id]['subject']);
            break;
         }   
        $bodyContentmsg = str_replace($data1,$data2,$bodyContent[$id]['body']);
        $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent[$id]['footertext']);
        $chk = $this->sendWithoutSmtpMail($loginid,$subjectMsg,FROM_MAIL,$messagemail); 
        return $chk;
    }

    public function dbeeComparetemplate($bodyContentmsg,$footerContentmsg){
        $deshboard   = new Admin_Model_Deshboard();
        $emailTemplatemain = $this->dbeeEmailtemplate();
        $emailTemplatejson = $deshboard->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),
                             'adminemailtemplates','id',1);
        $bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
        $bodyContentjsonval = json_decode($bodyContentjson, true);
        $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]',
        '[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]',
        '[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
        '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]');
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],$bodyContentjsonval['bodycontenttxture'],
        $bodyContentjsonval['headerbacgroColor'],$bodyContentjsonval['headertxture'],
        $bodyContentjsonval['bannerfreshimg'],$bodyContentjsonval['contentbodyfontColor'],
        $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],
        $bodyContentmsg,$bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],
        $bodyContentjsonval['footertxture'],$bodyContentjsonval['footerfontColor'],$footerContentmsg);

        return $messagemail = str_replace($data1,$data2,$emailTemplatemain);
    } */
}
?>