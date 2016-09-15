<?php
class ReportController extends IsController
{
    public function init()
    {
        parent::init();
    }
    /**
     *  make own expert
     */
    public function abuseAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeID = (int)$this->_request->getPost('db');
            $commentID = (int)$this->_request->getPost('commentID');
            $type = (int)$this->_request->getPost('type');
         
          
			$Text = "Are you sure you would like to report this as abuse?";

            $content         = $Text . '<form name="abuseform">
            <input type="hidden" id="db_report" name="db" value="' . $dbeeID . '">
            <input type="hidden" id="comment_report" name="comment" value="' . $commentID . '">
            <input type="hidden" id="type_report" name="type" value="' . $type . '">
            </form>';
            $data['content'] = $content;
            $data['status']  = 'success';
        
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function sentabuseAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
           
            $request   = $this->getRequest();
            $dbee      = $request->getpost('db');
            $commentid = $request->getpost('comment');
            $type      = $request->getpost('type');
            if ($type == '1')
                $label = 'post';
            elseif ($type == '2') {
                $label  = 'comment';
                $anchor = '#' . $comment;
            }
            
            // FETCH REPORTING USER NAME
            $getUserData = $this->User_Model->getUserDetail($this->_userid);
           
            // SEND ABUSE REPORT TO ADMIN
            /*echo'<pre>';print_r($type);
            die;*/
            if ($type == '1')
                {
                    $Row = $this->myhome_obj->getDbeeDetails($dbee);
                }
            elseif ($type == '2')
                {
                    $Row = $this->dbeeCommentobj->getcommentbyid($commentid);
                }
            
                    
            if ($Row['Type'] == '1'){
                $Text = ($type == '1') ? $Row['Text'] : $Row['Comment'];}
                
            elseif ($Row['Type'] == '2'){
                $Text = $Row['Text'].'<br/><a href="'.BASE_URL.'/dbee/'.$Row['dburl'].'">';
            }
            elseif ($Row['Type'] == '3'){
            $Text = $Row['Text'].'<br /><img src="'.IMGPATH.'/imageposts/medium/'.$Row['Pic'].'" width="200" border="0" />';
            }              
            elseif ($Row['Type'] == '4'){
                $Text = $Row['Text'].'<br/><a href="'.BASE_URL.'/dbee/'.$Row['Vid'].'">';
            }
            elseif ($Row['Type'] == '5'){
                $Text = $Row['PollText'].'<br/><a href="'.BASE_URL.'/dbee/'.$Row['dburl'].'">';
            }
            
            // mail code here
            $EmailTemplateArray = array(
            	'userName' => 'Admin',
                'Email' => ADMIN_EMAIL,
                'cookieuser' => $this->_userid,
                'label' => $label,
                'ReportingUser' => $getUserData['Name'],
                'lname' => $getUserData['lname'],
                'Text' => $Text,
                'dbee' => $dbee,
                'case' => 13
            );
            $EmailTemplateackArray = array(
            	'userName' => $getUserData['Name'],
                'Email' => $getUserData['Email'],
                'cookieuser' => $this->_userid,
                'label' => $label,
                'ReportingUser' => $getUserData['Name'],
                'lname' => $getUserData['lname'],
                'Text' => $Text,
                'dbee' => $dbee,
                'case' => 39
            );
           
            $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateArray); 
            $bodyContentmsgallack  = $this->dbeeComparetemplateOne($EmailTemplateackArray);            
            $data['message'] = 'successfully sent';
            $data['status']  = 'success';
        
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }

    public function reportabugAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $content .= '<div id="signuplastWrapper">
            <div class="user-name" style="padding:10px 0px">Found a bug? Report it here</div>
            <div id="passform" class="postTypeContent">
                <div id="bugform">
                    <div class="formRow">
                        <div class="formField"><textarea id="bug" class="textareafield"></textarea> <i class="optionalText">Bug details</i></div>
                    </div>
                    <div class="formRow">
                        <div class="formField">
                         <select id="userbrowser" style="padding:5px;">
                            <option value="0">Select your browser</option>
                            <option value="Internet Explorer">Internet Explorer</option>
                            <option value="Mozilla Firefox">Mozilla Firefox</option>
                            <option value="Google Chrome">Google Chrome</option>
                            <option value="Safari">Safari</option>
                        </select>
                        <i class="optionalText">Your browser</i></div>
                    </div>
                    <div id="bugreport-message" style="padding:10px; margin-top:5px; font-size:14px; font-weight:bold; color:#999999;"></div>
                </div>
            </div>';
            $data['status']  = 'success';
            $data['content'] = $content;
            
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
   
    public function reportabugprcessAction()
    {
        $request = $this->getRequest()->getParams();
         
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST') 
        {
         
              $bug         = $this->_request->getPost('bug');
              $userbrowser = $this->_request->getPost('userbrowser');
              $Row         = $this->myhome_obj->getrowuser($this->_userid);
              $EmailTemplateArray = array(
              	'userName' => $Row['Name'],
                'Email' => ADMIN_EMAIL,
                'Name' => $Row['Name'],
                'UserID' => $Row['UserID'],
                'bug' => $bug,
                'userbrowser' => $userbrowser,
                'case' => 14
                );

              $EmailTemplateackArray = array(
              	'userName' => $Row['Name'],
                'Email' => $Row['Email'],
                'Name' => $Row['Name'],
                'lname' => $Row['lname'],
                'UserID' => $Row['UserID'],
                'bug' => $bug,
                'userbrowser' => $userbrowser,
                'case' => 40
                );
              
             $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateArray);
             $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateackArray);
             $data['status']  = 'success';
            
            return $response->setBody(Zend_Json::encode($data));
        }
    }
    
}
