<?php

/**
 * This is site's main class/controller.  added by deshbandhu
 */
class IsloginController extends Zend_Controller_Action
{   
    
    private $options;
    
    public function init()
    {
        parent::init();
        $data = array();        
        $this->config = Zend_Registry::get("config");
        $auth =  Zend_Auth::getInstance();      
        $storage = new Zend_Auth_Storage_Session(); 
        if($this->_request->getParam('id')!="")
        {
         $searchparam = new Zend_Session_Namespace();
         $searchparam->searchdb = $this->_request->getParam('id');
        }
        if ($auth->hasIdentity())
        {
           $this->_helper->redirector('index', 'index', 'home');
        }
        $this->_helper->layout()->setLayout('layout');
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $this->deshboard   = new Admin_Model_Deshboard();
    }
    
    function getDomainFromEmail($email)
    {
        // Get the data after the @ sign
        $domain = substr(strrchr($email, "@"), 1);

        return $domain;
    }
  
    public function sendWithoutSmtpMail($to, $setSubject, $from ='no-reply@dbee.me', $setBodyText)
    {
        
        $EMAIL_HEADER ='<head><meta content="text/html; charset=utf-8" http-equiv="Content-Type"><title>tvconnected.co.uk</title><style type="text/css"></style></head><body style="background:#E8E9EB url(http://new.dbee.me/img/bg1.gif) repeat;"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="padding-top:100px; padding-bottom:100px; "><table width="623" style="background:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:13px;" align="center" border="0" cellspacing="0" cellpadding="30"><tr><td><a href="http://new.db-csp.com"  title="http://new.db-csp.com" target="_blank"><img src="http://new.db-csp.com/img/mailers/images/brandLogo.png" border="0"  style="display:inline-block" alt="dbee logo" /></a></td></tr>';
    
        $EMAIL_FOOTER = '<tr><td style="background:#d3d3d3;"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><div align="right"><a href="http://www.db-csp.com" target="_blank" style="text-decoration:none"><font color="#666">powered by db corporate social platforms</font></a></div></td></tr></table></td></tr></table></td></tr></table></body>';
        $this->myclientdetails = new Admin_Model_Clientdetails();
        $setBodyText = $EMAIL_HEADER.$setBodyText.$EMAIL_FOOTER; 
        $this->myclientdetails->sendWithoutSmtpMail($to, $setSubject, $from, $setBodyText);
    } 

    public function dbeeEmailtemplate(){
            $myImgurl = FRONT_URL.'/adminraw/img/bgs/';
            $logoImgurl = FRONT_URL.'/adminraw/img/emailbgimage/';
            return '<table width="100%"><tbody><tr>
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
             
    }  
}

/*end of class */
