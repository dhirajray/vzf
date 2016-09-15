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
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url('.$myImgurl.'[%headertxture%]) repeat; padding-top:20px; padding-bottom:20px;"><a href="#"  ><img src="'.$logoImgurl.'[%bannerfreshimg%]" id="bannerHolder" border="0" style="display:inline-block" alt="dbee logo"></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#[%contentbodyfontColor%]; padding:20px 30px 30px 30px; background:#[%contentbodybacgroColor%] url('.$myImgurl.'[%contentbodytxture%]) repeat;"  class="editingBlck bodymsg" titleval="Body container" editType="contentbody" id="bodymsg" >
                                   <div id="bodyEmailMsg"><br /><br />[%%body%%]</div>
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
}
?>