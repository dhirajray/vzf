<?php
class ExpertController extends IsController
{
    public function init()
    {
        parent::init();
        $this->expert = new Application_Model_Expert();
        $this->comment_model = new Application_Model_Comment();
        $this->common_model  = new Application_Model_Commonfunctionality();
    }

  public  function askanexpertAction()
  {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $storage    = new Zend_Auth_Storage_Session();
        $session        = $storage->read();
       
        if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true)
        {
            $grouptypes = new Application_Model_Groups();
            $dbid = (int) $this->_request->getPost('dbid');
            $promoted = $this->_request->getPost('promoted');
            $expertuser = (int) $this->_request->getPost('expertid');
            
            if($expertuser && $promoted=='true')
            {
                $content.='
                <h2 class="titlePop">Ask a question</h2>
                <div class="postTypeContent" id="passform">
                    <div class="formRow">
                     <div class="formField">
                         <input type="hidden" class="textfield" name="dbidfromexpert" id="dbidfromexpert"  value="">
                         <input type="hidden" class="textfield" name="expertfromexpert" id="expertfromexpert"  value="'.$expertuser.'">
                        <textarea class="textareafield" id="ask_to_question" name ="ask_to_question" ></textarea></div>
                    </div>
                </div>
                </div>';
                $data['status']  = 'success';
                $data['content'] = $content;
                return $response->setBody(Zend_Json::encode($data));
            }


            $expertArray = $this->myhome_obj->showDBeeExpert($dbid,$expertuser);
            $row = $this->myhome_obj->getdbeedetail($dbid);
           
            if($row['expertAskQues'])
            {
                $expertAskQues = explode(',', $row['expertAskQues']);
                $expertList = explode(',', $row['expertuser']);

                $resultEx = array_intersect($expertAskQues,$expertList);
                if(empty($resultEx))
                {
                    $content.="Sorry, you can't ask a question.";
                    $data['status']  = 'removed';
                    $data['content'] = $content;
                    return $response->setBody(Zend_Json::encode($data));
                }
            }else
            {
                $content.="Sorry, you can't ask a question.";
                $data['status']  = 'removed';
                $data['content'] = $content;
                return $response->setBody(Zend_Json::encode($data));
            }

            $memberchk = array();
            $grpchk = array();
            $reqgrp = false;
            
            if($row['GroupID']!=0) 
            {
                $grpchk = $grouptypes->allgroupdetailsuser($row['GroupID']);
                if($grpchk['GroupPrivacy'] == '2' || $grpchk['GroupPrivacy'] == '3') 
                {
                    $reqgrp = true;
                    $memberchk = $grouptypes->allgroupdetailsuserre($row['GroupID'],$this->_userid);
                }
            }
            $currentTime  = date('Y-m-d H:i:s');
            $data['eventStartTime'] = $row['eventstart'];

            if ($row['eventstart'] <= $currentTime)
                $data['controls'] = true;
            else
                $data['controls'] = false;

            if($row['Type']!=6)
                $data['controls'] = true;

            if($reqgrp && empty($memberchk) && $row['GroupID']!=0)
            {
                $content.='Sorry, this post belongs to a group that requires you to join before asking a question.';
                $data['status']  = 'removed';
                $data['content'] = $content;
                return $response->setBody(Zend_Json::encode($data));
            }

            if(!empty($row) && !empty($expertArray[0]) && $data['controls']==true)
            {
                $content.='
                <h2 class="titlePop">Ask a question</h2>
                <div class="postTypeContent" id="passform">
                    <div class="formRow">
                     <div class="formField">
                         <input type="hidden" class="textfield" name="dbidfromexpert" id="dbidfromexpert"  value="'.$dbid.'">
                         <input type="hidden" class="textfield" name="expertfromexpert" id="expertfromexpert"  value="'.$expertuser.'">
                        <textarea class="textareafield" id="ask_to_question" name ="ask_to_question" ></textarea></div>
                    </div>
                </div>
                </div>';
                $data['status']  = 'success';
                $data['content'] = $content;
            }else
            {
                $content.='Sorry, this expert has been removed.';
                $data['status']  = 'removed';
                $data['content'] = $content;
            }
        }else
        {
            $data['status']  = 'error';
            $data['content'] = "Some thing went wrong here please try again";
        } 

        return $response->setBody(Zend_Json::encode($data));
   }
    public function answerpostingAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->_request->getPost('replytype') =='answer' && $this->islogin()==true)
        {
            $filter = new Zend_Filter_StripTags();
            $data_array = array();
            $dbeeid = (int) $this->_request->getPost('dbid');
            $commentOwerId = (int)$this->_request->getPost('cmntownerId');
            $parentid = (int)$this->_request->getPost('parentid');
            $expertid = (int)$this->_request->getPost('expert_id');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            $questionDetails = $this->expert_model->getQuestionDetails($parentid);

            if($expertid!=0 && !empty($questionDetails) && $dbeeid!=0)
            {      
                $data_array['qna'] = $filter->filter($this->_request->getPost('answer'));
                $data_array['user_id'] = $this->_userid;
                $data_array['expert_id']  = $expertid;
                $data_array['timestamp'] = date('Y-m-d H:i:s');
                $data_array['parentid'] = $parentid;
                $data_array['status'] = 1;
                $data_array['clientID'] = clientID;
                $data_array['dbeeid'] = $dbeeid;
                $data_array['dbowner'] = $dbee_data['User'];
                $quesid = $this->Expert_Model->insertdbeeqna($data_array);
                // insert value in expert q&a

                $this->notification->commomInsert(11,12,$dbeeid,$this->_userid,$commentOwerId,'',$quesid);

                $result = $this->User_Model->userdetailall($questionDetails['user_id']);
                $this->sendMailToExpertTurnOff($result[0],$dbee_data,'answerpost',$this->session_data['Name']);

                if($expertid!=$dbee_data['User'])
                    $this->notification->commomInsert(11,21,$dbeeid,$this->_userid,$dbee_data['User'],'',$quesid);
                
                $this->Expert_Model->updateAnswerStatus($parentid,$this->_userid);
                $this->Expert_Model->updateQuestionsStatus($parentid,$this->_userid);

                 if($dbee_data['User']==$this->_userid && $this->_userid==$expertid)
                    $qaResult = $this->expert_model->getPendingQuestion($dbeeid,$this->_userid,1,'ownerISexpert');
                else if($this->_userid==$expertid)
                    $qaResult = $this->expert_model->getPendingQuestion($dbeeid,$this->_userid,1,'expertISNOTowner');
                else if($this->_userid==$dbee_data['User'])
                    $qaResult = $this->expert_model->getPendingQuestion($dbeeid,$expertid,1,'ownerISNOTexpert');
                
                $parentid = (int) $this->_request->getPost('parentid');
                $this->expert_model->moveToLiveFeed($parentid);

                $data['status']  = 'success';
                $data['owner']  = $commentOwerId;
                $data['dbowners']  = $dbee_data['User'];
                $data['pendingquestionCount']  = count($qaResult);
                $data['content'] = "your reply successfully posted";
            }
            elseif ($dbeeid==0) 
            {
                $data_array['qna'] = $filter->filter($this->_request->getPost('answer'));
                $data_array['user_id'] = $this->_userid;
                $data_array['expert_id']  = $expertid;
                $data_array['timestamp'] = date('Y-m-d H:i:s');
                $data_array['parentid'] = $parentid;
                $data_array['status'] = 11;
                $data_array['clientID'] = clientID;
                $data_array['dbeeid'] = $dbeeid;
                $data_array['dbowner'] = 0;
                $quesid = $this->Expert_Model->insertdbeeqna($data_array);
                $this->Expert_Model->updateAnswerStatus($parentid,$this->_userid);
                $this->Expert_Model->updateQuestionsStatus($parentid,$this->_userid);
                $this->notification->commomInsert(11,52,$quesid,$this->_userid,$questionDetails['user_id'],'',$quesid);

            }
            else if(empty($questionDetails))
            {
                $data['status']  = 'error';
                $data['removed']  = true;
                $data['message'] = 'Sorry this question has been deleted by the user.';
            }
            else
            {
                $data['status']  = 'error';
                $data['expertRemoved']  = true;
                $data['content'] = "Sorry your question was not submitted as this expert has been removed.";
            }
        }else{
            $data['status']  = 'error';
            $data['content'] = "Some thing went wrong here please try again";
        } 

        return $response->setBody(Zend_Json::encode($data));
    }
    public  function askanexpertsentAction()
    {

        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true)
        {
            $filter = new Zend_Filter_StripTags();
            $data_array = array();
            $data_array['dbeeid'] = (int) $this->_request->getPost('dbid');
            $expertid = (int) $this->_request->getPost('expertid');
            if($expertid!='' && $data_array['dbeeid']!=0)
            {

                $expertArray = $this->myhome_obj->showDBeeExpert($data_array['dbeeid']);
                $dbee_data = $this->Myhome_Model->getDbeeDetails($data_array['dbeeid']); 
                $data_array['expert_id']  = $expertid;
                $data_array['qna'] = $filter->filter($this->_request->getPost('question'));
                $data_array['user_id'] = $this->_userid;
                $data_array['timestamp'] = date('Y-m-d H:i:s');
                $data_array['dbowner'] = $dbee_data['User'];
                $data_array['parentid'] = 0; // get user details
                if($dbee_data['User']==$this->_userid && $this->_userid!=$expertid)
                {
                   $data_array['status'] = 7;
                   $data['refer'] = 'gotoexpert';
                }
                else if($dbee_data['User']==$expertid)
                {
                   $data_array['status'] = 9;
                   $data['refer'] = 'gotoexpert';
                }
                else
                {
                   $data_array['status'] = 2; 
                   $data['refer'] = 'gotoowner';
                }
                $data_array['clientID'] = clientID;
                // dbee url from id
                $QAID = $this->Expert_Model->insertdbeeqna($data_array);  // insert value in expert q&a
                if($dbee_data['User']!=$expertid && $dbee_data['User']!=$this->_userid)
                {
                    // send notification to owner
                    $this->notification->commomInsert(11,43,$data_array['dbeeid'],$this->_userid,$dbee_data['User'],'',$QAID);
                }else if($dbee_data['User']==$data_array['expert_id'])
                { 
                    // send notification to expert
                    $this->notification->commomInsert(11,11,$data_array['dbeeid'],$this->_userid,$expertid,'',$QAID);
                    $result = $this->User_Model->userdetailall($data_array['expert_id']);
                    $this->sendMailToExpertTurnOff($result[0],$dbee_data,'askquestion',$this->session_data['Name']);
                }
                else if($dbee_data['User']==$this->_userid)
                { 
                    // send notification to expert
                    $this->notification->commomInsert(11,11,$data_array['dbeeid'],$this->_userid,$expertid,'',$QAID);
                    $result = $this->User_Model->userdetailall($expertid);
                    $this->sendMailToExpertTurnOff($result[0],$dbee_data,'askquestion',$this->session_data['Name']);
                }
                $data['status']  = 'success';
                $data['content'] = "question sent!";
                $data['Ownerid'] = $dbee_data['User'];
                $data['QAID'] = $QAID;
            }
            else if($data_array['dbeeid']==0 && $expertid!='')
            {
                $data_array['expert_id']  = $expertid;
                $data_array['qna'] = $filter->filter($this->_request->getPost('question'));
                $data_array['user_id'] = $this->_userid;
                $data_array['timestamp'] = date('Y-m-d H:i:s');
                $data_array['dbowner'] = '';
                $data_array['parentid'] = 0; // get user details
                $data_array['status'] = 11;
                $data_array['clientID'] = clientID;
                $QAID = $this->Expert_Model->insertdbeeqna($data_array); 
                $this->notification->commomInsert(11,51,$QAID,$this->_userid,$expertid,'',$QAID);
                $data['QAID'] = $QAID;
                $data['status']  = 'success';
                $data['content'] = "question sent!";
            }else
            {
                $content.='Sorry, this expert has been removed.';
                $data['status']  = 'error';
                $data['content'] = $content;
            }
        }else{

            $data['status']  = 'error';
            $data['content'] = "question not sent!";
        } 

        return $response->setBody(Zend_Json::encode($data));
    }
    function getHashTags($text)
    {
        //Match the hashtags
        preg_match_all('/(^|[^a-z0-9_])#([a-z0-9_]+)/i', $text, $matchedHashtags);
        $hashtag = '';
        // For each hashtag, strip all characters but alpha numeric
        if (!empty($matchedHashtags[0])) {
            foreach ($matchedHashtags[0] as $match) {
                $hashtag .= preg_replace("/[^a-z0-9]+/i", "", $match) . ",";
            }
        }
        //to remove last comma in a string
        return rtrim($hashtag, ',');
    }
    public function movetocommentAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            
            $movetoexpert = $this->_request->getPost('movetoexpert');
            
            if ($movetoexpert == true) 
            {
                $dbid = (int) $this->_request->getPost('dbid');
                $parentid = (int) $this->_request->getPost('parentid');
                $dbowners = (int) $this->_request->getPost('dbowners');
                
                // get question details 
                $questionDetails = $this->expert_model->getQuestionDetails($parentid);
                
                $CommentDate = date('Y-m-d H:i:s');
                
                $hashTag = $this->getHashTags($questionDetails['qna']);

                $data1 = array(
                    'DbeeID' => $dbid,
                    'parentid' => 0,
                    'DbeeOwner' => $dbowners,
                    'UserID' => $questionDetails['user_id'],
                    'Type' => 1,
                    'Comment' => $questionDetails['qna'],
                    'Link' => '',
                    'LinkTitle' => '',
                    'LinkDesc' => '',
                    'UserLinkDesc' => '',
                    'Pic' => '',
                    'PicDesc' => '',
                    'Vid' => '',
                    'VidDesc' => '',
                    'VidSite' => '',
                    'VidID' => '',
                    'Audio' => '',
                    'TwitterComment' => '',
                    'CommentDate' => $CommentDate,
                    'NotifyEmail' => '',
                    'QNA' => 1,
                    'DbTag'=> $hashTag,
                    'QNATYPE' => 1,
                    'qid' => $parentid,
                    'clientID' => clientID
                );
                
                $insertId = $this->comment_model->insertcomment($data1);
                
                $answerDetails = $this->expert_model->getAnswerDetails($parentid);
                $time          = time() + 1;
                $CommentDate2  = date('Y-m-d H:i:s', $time);
                $hashTag = $this->getHashTags($answerDetails['qna']);
                $data2 = array(
                    'DbeeID' => $dbid,
                    'parentid' => $insertId,
                    'DbeeOwner' => $dbowners,
                    'UserID' => $answerDetails['user_id'],
                    'Type' => 1,
                    'Comment' => $answerDetails['qna'],
                    'Link' => '',
                    'LinkTitle' => '',
                    'LinkDesc' => '',
                    'UserLinkDesc' => '',
                    'Pic' => '',
                    'PicDesc' => '',
                    'Vid' => '',
                    'VidDesc' => '',
                    'VidSite' => '',
                    'VidID' => '',
                    'Audio' => '',
                    'TwitterComment' => '',
                    'CommentDate' => $CommentDate2,
                    'NotifyEmail' => '',
                    'QNA' => 0,
                    'QNATYPE' => 1,
                    'DbTag'=> $hashTag,
                    'qid' => $parentid,
                    'clientID' => clientID
                );
                
                $this->comment_model->insertcomment($data2);
                $this->expert_model->MoveToCommentStatus($parentid);
                
            }
            $data['status']  = 'success';
            $data['loginID']  = $this->_userid;
            $data['message'] = 'Question has been moved successfully.';
        } else {
            
            $data['status']  = 'error';
            $data['message'] = 'Question has not been moved successfully.';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }

     public function makeliveAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {   
            $makelive = $this->_request->getPost('makelive');
            
            if ($makelive == true) 
            {
                $dbid = (int) $this->_request->getPost('dbid');
                $dbee_data = $this->Myhome_Model->getDbeeDetails($dbid);
                if($dbee_data['User']==$this->_userid)
                {
                    $parentid = (int) $this->_request->getPost('parentid');
                    $this->expert_model->moveToLiveFeed($parentid);
                    $data['status']  = 'success';
                    $data['loginID']  = $this->_userid;
                    $data['message'] = 'Question has been moved successfully.';
                }
            }
        } else {
            
            $data['status']  = 'error';
            $data['message'] = 'Question has not been moved successfully.';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    public function expertasksettingAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {   
            $status = $this->_request->getPost('status');
            $dbeeid = $this->_request->getPost('dbeeid');
            $expertid = $this->_request->getPost('id');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            $where = array('DbeeID'=>$dbeeid);
            if ($status == 1) 
            {
                if($dbee_data['expertAskQues']=='' || $dbee_data['expertAskQues']==0)
                    $dataExpert = array('expertAskQues'=>$expertid);
                else
                {
                    $expertlist = explode(',', $dbee_data['expertAskQues']);
                    if(!in_array($expertid, $expertlist)){
                        $expertlist[] = $expertid;
                    }
                    $expertlistUpdated = implode(',', $expertlist);
                    $dataExpert = array('expertAskQues' => $expertlistUpdated);
                }
            }
            else
            {
                $expertlist = explode(',', $dbee_data['expertAskQues']);
                $expertlistUpdated = $this->arraydelete($expertlist,$expertid);
                $dataExpert = array('expertAskQues' => implode(',', $expertlistUpdated));
            }
            $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Question has not been moved successfully.';
        }
        return $response->setBody(Zend_Json::encode($dataExpert));
    }
    public function arraydelete($array, $element) {

        $key = array_search($element,$array);
        if($key!==false){
            unset($array[$key]);
        }
        return $array;
    }
    public function removequestionbyexpertAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {
            $dbid        = (int) $this->_request->getPost('dbid');
            $question  = (int) $this->_request->getPost('question');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbid);
            if ($dbee_data['expertuser'] != 0) 
            {
                $questionDetails = $this->expert_model->getQuestionDetails($question);
                if($this->_userid==adminID && $this->session_data['role']==1 && $question!=0)
                {
                    $this->expert_model->QuestionDeleted($question);
                    $this->notification->commomInsert(11, 20, $dbid, $this->_userid, $questionDetails['user_id'], '', $question);
                }
                else if($this->_userid==$dbee_data['User'] && $question!=0)
                {
                    $this->expert_model->QuestionDeleted($question);
                    $this->notification->commomInsert(11, 20, $dbid,$this->_userid, $questionDetails['user_id'], '', $question);
                }
                else if($question!=0)
                    $this->expert_model->QuestionDeleted($question,$this->_userid);
                
                $data['status']  = 'success';
                $data['message'] = 'Question has been removed successfully';
            } else 
            {
                $data['status']  = 'error';
                $data['message'] = "Some thing went wrong here please try again.";
            }
                        
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }
    public function sendMailToExpertTurnOff($data, $dbee_data, $type,$fromName='')
    {
        switch ($type) 
        {
            case 'remove':
               $EmailTemplateArray = array(
                    'Email' => $data['Email'],
                    'Name' => $data['Name'],
                    'lname' => $data['lname'],
                    'dburl' => '<a href="'.BASE_URL.'/dbee/'.$dbee_data['dburl'].'" >'.$dbee_data['Text'].'</a>',
                    'case' => 12
                );
                break;
            case 'askquestion':
               $EmailTemplateArray = array(
                    'Email' => $data['Email'],
                    'Name' => $data['Name'],
                    'lname' => $data['lname'],
                    'dburl' => '<a href="'.BASE_URL.'/dbee/'.$dbee_data['dburl'].'#pqa" >'.$dbee_data['Text'].'</a>',
                    'sessionName' => $fromName,
                    'case' => 29
                );
                break;
             case 'submitquestion':
               $EmailTemplateArray = array(
                    'Email' => $data['Email'],
                    'Name' => $data['Name'],
                    'lname' => $data['lname'],
                    'dburl' => '<a href="'.BASE_URL.'/dbee/'.$dbee_data['dburl'].'" >'.$dbee_data['Text'].'</a>',
                    'sessionName' => $fromName,
                    'case' => 123
                );
                break;
            case 'answerpost':
               $EmailTemplateArray = array(
                    'Email' => $data['Email'],
                    'Name' => $data['Name'],
                    'lname' => $data['lname'],
                    'dburl' => '<a href="'.BASE_URL.'/dbee/'.$dbee_data['dburl'].'#myqa" >'.$dbee_data['Text'].'</a>',
                    'sessionName' => $fromName,
                    'case' => 34
                );
                break;
        }
        $this->dbeeComparetemplateOne($EmailTemplateArray);
    }
    public function movetoexpertAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {            
            $movetoexpert = $this->_request->getPost('movetoexpert');
            $dbid = (int) $this->_request->getPost('dbid');
            $commentid = (int) $this->_request->getPost('commentid');
            $type = $this->_request->getPost('type');
            if ($movetoexpert == true) 
            {
                $dbid = (int) $this->_request->getPost('dbid');
                $commentid = (int) $this->_request->getPost('commentid');
                $dbee_data = $this->Myhome_Model->getDbeeDetails($dbid);
                $questionDetails = $this->expert_model->getQuestionDetails($commentid);
                if(!empty($questionDetails) && $dbee_data['expertuser']!='' && $type =='movetoexpert')
                {
                    $this->expert_model->updateQaStatus($commentid,'verified');
                    $this->notification->commomInsert(11,11,$dbid,$questionDetails['user_id'],$questionDetails['expert_id'],'',$commentid);
                    $data['status']  = 'success';
                    $data['expertid']  = $questionDetails['expert_id'];
                    $data['message'] = 'Question has been moved successfully.';
                    $result2 = $this->User_Model->userdetailall($questionDetails['user_id']);
                    $result = $this->User_Model->userdetailall($questionDetails['expert_id']);
                    $this->sendMailToExpertTurnOff($result[0],$dbee_data,'askquestion',$result2[0]['Name']);
                }
                else if($type=='movetoother' && !empty($questionDetails) && $dbee_data['expertuser']!='')
                {
                    $this->expert_model->updateQaStatus($commentid,'other');
                    $this->notification->commomInsert(11,11,$dbid,$questionDetails['user_id'],$questionDetails['expert_id'],'',$commentid);
                    $data['status']  = 'success';
                    $data['expertid']  = $questionDetails['expert_id'];
                    $data['message'] = 'Question has been moved successfully.';
                    $result2 = $this->User_Model->userdetailall($questionDetails['user_id']);
                    $result = $this->User_Model->userdetailall($questionDetails['expert_id']);
                    $this->sendMailToExpertTurnOff($result[0],$dbee_data,'askquestion',$result2[0]['Name']);
                }
                else if($type=='movetohide' && !empty($questionDetails) && $dbee_data['expertuser']!='')
                {
                    $this->expert_model->updateQaStatus($commentid,'removed');
                    if($questionDetails['expert_id']!=$this->_userid)
                        $this->notification->commomInsert(11,11,$dbid,$questionDetails['user_id'],$questionDetails['expert_id'],'',$commentid);
                    $data['status']  = 'hides';
                }
                else if(empty($questionDetails))
                {
                    $data['status']  = 'error';
                    $data['removed']  = true;
                    $data['message'] = 'Sorry this question has been deleted by the user.';
                    $data['commentid'] = $commentid;
                }
            }
        } else {
            
            $data['status']  = 'error';
            $data['message'] = 'Question has not been moved successfully.';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    
    public function dbeeinviteAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $dbid = (int) $this->_request->getPost('dbid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbid);
            $expertCount = $this->myhome_obj->checkeexpertStatus($dbid);
            
            $data['social'] = $this->expert->checkinviteexpert($dbid);
            $data['plateform'] = $this->expert->getInviteUser($dbid);

            $totalCount =  (int)(count($data['social']) + count($data['plateform']));
            $error = false;

            if($totalCount==1 && $this->allowmultipleexperts==1)
            {
                $data['status']  = 'error';
                $data['content'] = "Sorry, you can't invite more user(s) on this post.";
                $error = true;

            }
            if($this->allowmultipleexperts==1 && $expertCount==0 && $error== false)
            {
                $user = $this->_request->getPost('user_concat_val');
                $where = array('act_typeId'=>$dbid,'act_type'=>39,'act_message'=>44);
                $this->myclientdetails->deleteMaster('tblactivity',$where);
                $this->notification->commomInsert(39,44,$dbid,$this->_userid,$user,'','');
                $data['status']  = 'success';
            }
            else if($this->allowmultipleexperts==3 && $expertCount<=4 && $error== false)
            {
                $userList = explode(',', $this->_request->getPost('user_concat_val'));
                $userListCount = count(explode(',', $this->_request->getPost('user_concat_val')));
                $totalP = ($userListCount+$totalCount+expertCount);
                if($totalP<=4)
                {
                    foreach ($userList as $value)
                    {
                        if($this->_userid!=$value)
                        {
                            $where = array('act_typeId'=>$dbid,'act_ownerid'=>$value,'act_type'=>39,'act_message'=>44);
                            $this->myclientdetails->deleteMaster('tblactivity',$where);
                            $this->notification->commomInsert(39,44,$dbid,$this->_userid,$value,'','');
                        }
                    }
                    $data['status']  = 'success';
                }
                else if($totalCount==4)
                {
                    $data['status']  = 'error';
                    $data['content'] = "Sorry, you can't invite more user(s) on this post.";
                }
                else
                {
                    $data['status']  = 'error';
                    $data['content'] = "Sorry, you can only invite ".(4-$totalCount)." more user(s) on this post.";
                }
                
            }
            else if($this->allowmultipleexperts==1 && $error== false)
            {
                $data['status']  = 'error';
                $data['expertRemoved']  = true;
                $data['content'] = "Sorry your invitation was not submitted because the expert has been joined.";
            }
            else if($this->allowmultipleexperts==3 && $error== false)
            {
                $data['status']  = 'error';
                $data['expertRemoved']  = true;
                $data['content'] = "Sorry your invitation was not submitted because maximum four expert can be join.";
            }
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Question has not been moved successfully.';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function pendingquestionAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {   
            $page_number = (int)$this->_request->getPost('page');
            $dbeeid = (int) $this->_request->getPost('dbid');
            $qaid = (int) $this->_request->getPost('qaid');
            $data['more'] = true;
           
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            $expertlist = explode(',', $dbee_data['expertuser']);
            
            if($page_number!='' && $dbeeid!=0)
            {
                if($dbee_data['User']==$this->_userid && in_array($this->_userid,$expertlist))
                {
                    $data['ownerISexpert'] = true;
                    $qaResult = $this->expert_model->getPendingQuestion($dbeeid,$this->_userid,$page_number,'ownerISexpert');
                }
                else if(in_array($this->_userid,$expertlist))
                {
                    $qaResult = $this->expert_model->getPendingQuestion($dbeeid,$this->_userid,$page_number,'expertISNOTowner');
                    $data['expertISNOTowner'] = true;
                }
                else if($this->_userid==$dbee_data['User'])
                {
                    $qaResult = $this->expert_model->getPendingQuestion($dbeeid,$dbee_data['expertuser'],$page_number,'ownerISNOTexpert');
                    $data['ownerISNOTexpert'] = true;
                }
            }
            else if($qaid!='')
                $qaResult = $this->expert_model->getSingleQuestion($qaid);

            if(!empty($qaResult) && $page_number!='')
            {
                $return .= $this->view->partial('partials/pendingqa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$expertlist,'ownerid'=>$dbee_data['User'],'User_Model'=>$this->User_Model,'allowmultipleexperts'=>$this->allowmultipleexperts));
                
                $data['page'] = $page_number+1;
            }
            else if($page_number=='' && $qaid!='')
            {
                $return = $this->view->partial('partials/pendingqa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$expertlist,'ownerid'=>$dbee_data['User'],'User_Model'=>$this->User_Model,'allowmultipleexperts'=>$this->allowmultipleexperts));
            }else{
                $data['more'] = false;
                $return .='';
            }
            $data['status']  = 'success';
            $data['content'] = $return;
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }

     public function promotedquestionAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {   
            $page_number = (int)$this->_request->getPost('page');
            $qaid = (int) $this->_request->getPost('qaid');
            $callfrom = $this->_request->getPost('callfrom');
            $data['more'] = true;
            if($page_number!='')
            {
                $qaResult = $this->expert_model->getPomotedQuestion($this->_userid,$page_number);
                $data['page'] = $page_number+1;
                $singleRow = 'no';
            }
            else if($qaid!='' && $callfrom =='normal')
            {
                $qaResult = $this->expert_model->getPomotedQuestion($this->_userid,'',$qaid);
                $singleRow = 'yes';
            }
            else if($qaid!='' && $callfrom =='socket')
            {
                $qaResult = $this->expert_model->getPomotedQuestion($this->_userid,'',$qaid);
                $singleRow = 'no';
            }
            if(!empty($qaResult))
            {
                $return .= $this->view->partial('partials/promotedqa.phtml', array('qaResult'=>$qaResult,'common_model'=>$this->common_model,'comment_model'=>$this->comment_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'User_Model'=>$this->User_Model,'singleRow'=>$singleRow));
            }
            else
            {
                $data['more'] = false;
                $return ='';
            }
            $data['status']  = 'success';
            $data['content'] = $return;
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }

    public function myquestionsAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {   
            $page_number = (int)$this->_request->getPost('page');
            $dbeeid = (int) $this->_request->getPost('dbid');
            $qaid = (int) $this->_request->getPost('qaid');
            $data['more'] = true;
            // relative model call

            $this->comment_model = new Application_Model_Comment();
            $this->common_model  = new Application_Model_Commonfunctionality();
            $this->expert_model = new Application_Model_Expert();
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);

            if($page_number!='' && $dbeeid!=0)
                $qaResult = $this->expert_model->getMyQuestion($dbeeid, $this->_userid, $page_number);
            else if($qaid!='')
                $qaResult = $this->expert_model->getSingleQuestion($qaid);

            if(!empty($qaResult) && $page_number!='')
            {
                $return .= $this->view->partial('partials/qa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'expertText'=>$this->expertText));
                $data['page'] = $page_number+1;
            }
            else if($page_number=='' && $qaid!='')
            {
                $return = $this->view->partial('partials/qa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'expertText'=>$this->expertText));
            }else{
                $return .='';
                $data['more'] = false;
            }
            $data['status']  = 'success';
            $data['content'] = $return;
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function makeqatabAction()
    {
        $eventModel  = new Application_Model_Event();
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {   
            
            $currentdate = date('Y-m-d H:i:s', time());
            $dbeeid = (int) $this->_request->getPost('dbid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            // relative model call

            $requestInvitation = $this->expert->checkinviteexpert($dbeeid);
            $requestDbeeInvitation = $this->expert->getInviteUser($dbeeid);
            $expertArray = $this->myhome_obj->showDBeeExpert($dbeeid);
            if(!empty($requestInvitation))
            {
                 $data['expertLinkPopup'] = false;
                 $data['expertLink'] = 1;
            }
            else if(!empty($requestDbeeInvitation))
            {
                $data['expertLinkPopup'] = false;
                $data['expertLink'] = 2;
            }
            else if(strtotime($currentdate)>strtotime($dbee_data['qaendschedule']))
            {
                $data['expertLinkPopup'] = false;
                $data['expertLink'] = 3;
            }
            else if($dbee_data['User']==$this->_userid && $this->_userid==adminID && count($expertArray)==0)
            {
                $data['expertLink'] = $this->showExperLinkDisplayBlock($dbeeid,$dbee_data['Type'],$expertArray);
                $data['expertLinkPopup'] = true;
            }
            else if(count($expertArray)>0)
            {
                 $data['expertLinkPopup'] = false;
                 $data['expertLink'] = 4;
            }
            
            $data['Type'] = $dbee_data['Type'];

            $expertlist = explode(',', $dbee_data['expertuser']);
            
            $this->expert_model = new Application_Model_Expert();

            $myquestion = count($this->expert_model->getMyQuestion($dbeeid,$this->_userid,1));
            
            if(in_array($this->_userid,$expertlist) || $this->_userid==$dbee_data['User'])
                $livequestion = count($this->expert_model->getAllExpertQuestion($dbeeid,1));
            else
                $livequestion = 0;

            $pendingquestion = 0;

            $makelivequestion = count($this->expert_model->getLiveExpertQuestion($dbeeid,1));

            if($dbee_data['User']==$this->_userid && in_array($this->_userid,$expertlist))
            {
                $pendingquestion = count($this->expert_model->getPendingQuestion($dbeeid,$dbee_data['expertuser'],1,'ownerISexpert'));
                $data['ownerISexpert'] = true;
            }
            else if(in_array($this->_userid,$expertlist))
            {
                $pendingquestion = count($this->expert_model->getPendingQuestion($dbeeid,$this->_userid,1,'expertISNOTowner'));
                $data['expertISNOTowner'] = true;
            }
            else if($this->_userid==$dbee_data['User'])
            {
                $pendingquestion = count($this->expert_model->getPendingQuestion($dbeeid,$dbee_data['expertuser'],1,'ownerISNOTexpert'));
                $data['ownerISNOTexpert'] = true;
            }
            $totalCount = $myquestion+$pendingquestion+$livequestion+$makelivequestion;

            if($totalCount!=0 && !empty($dbee_data))
            {
                $return = $this->view->partial('partials/qatab.phtml', array('dbeeid' => $dbeeid,'makelivequestion'=>$makelivequestion,'myquestion'=>$myquestion,'livequestion'=>$livequestion,'pendingquestion'=>$pendingquestion,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'expertlist'=>$expertlist,'allowmultipleexperts'=>$this->allowmultipleexperts));
                $data['status']  = 'success';
                $data['content'] = $return;
                $data['attendee'] = true;
            }else
            {
                $TotalComments = $this->dbeeCommentobj->totacomment($dbeeid);
                $data['status']  = 'error';
                $data['TotalComments']  = $TotalComments;
                if ($this->_userid != '' && $dbee_data['events'] != '' && $dbee_data['Type'] == 9 && $dbee_data['events']!= 0) 
                {
                    $eventResult = $eventModel->getEvent($dbee_data['events']);
                    $memberData  = $eventModel->checkEventMember($this->_userid, $dbee_data['events']);
                    if (empty($memberData) && $eventResult['event_type'] == 2) 
                    {
                        $data['contentpart'] = "<div class='dbee-reply-wrapper surveyComplete' style='width:100%;'>Click here to become an attendee</div><div class='next-line'></div>";
                        $data['attendee'] = false;
                    }
                }
            }
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }
    public function showExperLinkDisplayBlock($dbeeid,$type,$expertArray)
    {
        $content .= '<a href="javascript:void(0);" class="invitexport" dbid="' . $dbeeid . '"  type="2" style="font-weight:normal" >Invite from platform</a> | ';
       
        if((count($expertArray)==0))
            $content .= '<a href="javascript:void(0);" class="makeownexpert" dbid="' . $dbeeid . '" 
            type="1" style="font-weight:normal" >Make me ' . $this->expertText . '</a>';

        return $content;
    }
    public function livequestionAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {
            $dbeeid  = (int) $this->_request->getPost('dbid');
            $page = (int) $this->_request->getPost('page');
            $qaid = (int) $this->_request->getPost('qaid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            $data['more'] = true;
            // relative model call
            $expertlist = explode(',', $dbee_data['expertuser']);
            $this->comment_model = new Application_Model_Comment();
            $this->common_model  = new Application_Model_Commonfunctionality();
            $this->expert_model = new Application_Model_Expert();
            if($page!='' && $dbeeid!=0 && $this->allowmultipleexperts==3 && in_array($this->_userid,$expertlist))
                $qaResult = $this->expert_model->getAllExpertQuestion($dbeeid, $page,$this->_userid);
            else if($page!='' && $dbeeid!=0)
                $qaResult = $this->expert_model->getAllExpertQuestion($dbeeid, $page);
            else if($qaid!='')
                $qaResult = $this->expert_model->getPushQuestion($dbeeid,$qaid);

            if(!empty($qaResult) && $page!='')
            {
                $return .= $this->view->partial('partials/liveqa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'liveqa'=>false));
                $data['page'] = $page+1;
            }
            else if($page=='' && $qaid!='')
            {
                $return = $this->view->partial('partials/liveqa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'liveqa'=>false));
            }
            else 
            {
                $return .= '';
                $data['nomore'] = false;
            }
           
            $data['status']  = 'success';
            $data['content'] = $return;
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }


    public function makelivequestionAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true) 
        {
            $dbeeid  = (int) $this->_request->getPost('dbid');
            $page = (int) $this->_request->getPost('page');
            $qaid = (int) $this->_request->getPost('qaid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            $data['more'] = true;
            // relative model call
            $this->comment_model = new Application_Model_Comment();
            $this->common_model  = new Application_Model_Commonfunctionality();
            $this->expert_model = new Application_Model_Expert();
            
            if($page!='' && $dbeeid!=0)
                $qaResult = $this->expert_model->getLiveExpertQuestion($dbeeid, $page);
            else if($qaid!='')
                $qaResult = $this->expert_model->getPushQuestion($dbeeid,$qaid);

            if(!empty($qaResult) && $page!='')
            {
                $return .= $this->view->partial('partials/liveqa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'liveqa'=>true));
                $data['page'] = $page+1;
            }
            else if($page=='' && $qaid!='')
            {
                $return = $this->view->partial('partials/liveqa.phtml', array('dbeeid' => $dbeeid,'qaResult'=>$qaResult,'comment_model'=>$this->comment_model,'common_model'=>$this->common_model,'expert_model'=>$this->expert_model,'myclientdetails'=>$this->myclientdetails,'loginUserId'=>$this->_userid,'expertid'=>$dbee_data['expertuser'],'ownerid'=>$dbee_data['User'],'liveqa'=>true));
            }
            else 
            {
                $return .= '';
                $data['nomore'] = false;
            }
           
            $data['status']  = 'success';
            $data['content'] = $return;
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }




    public function removeanswerAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->islogin()==true)
        {
            $parentid = (int)$this->_request->getPost('parentid');
            $answer_ids = (int)$this->_request->getPost('answer_ids');
            $dbid = (int)$this->_request->getPost('dbid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbid);
            $this->Expert_Model->deleteAnswer($this->_userid,$answer_ids);
            $this->Expert_Model->UndoQaStatus($parentid,$this->_userid);
            $data['status']  = 'success';
            $data['expertid']  = $dbee_data['expertuser'];
            $data['message'] = "Your answer has been deleted";            
        }else{

            $data['status']  = 'error';
            $data['content'] = "Some thing went wrong here please try again";
        } 

        return $response->setBody(Zend_Json::encode($data));
    }

    /**
     *  make own expert
     */
    public function makeownAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $redirection_name_space = new Zend_Session_Namespace('User_Session');
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) {
           
            $acceptExpertRequest = $this->_request->getPost('acceptExpertRequest');
            $dbid                = (int)$this->_request->getPost('dbid');
            $dbowners = (int)$this->_request->getPost('dbowners');
            $row = $this->myhome_obj->getdbeedetail($dbid);
            // for token validation and cross side domain validation
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));

            $UserDbeeGroupDbeeInviteRestriced = $this->UserDbeeGroupDbeeInviteRestriced($row);
            if ($validate==false) 
            {
                $data['status']  = 'error';
                $data['message'] = 'Something went wrong please try again';
            }
            if ($dbowners != $this->_userid && $UserDbeeGroupDbeeInviteRestriced != $this->_userid && empty($row)) 
            {
                $data['status']  = 'error';
                $data['message'] = 'You are not owners of this ' . POST_NAME . '';
            } 
            else if ($acceptExpertRequest == true) 
            {
                // check someone user joined
                $tokenArray = $this->expert->checkExpertStatus($dbid);
                if ($tokenArray == 0) 
                {
                    $userinfo = array(
                        'userid' => $this->_userid,
                        'dbid' => $dbid,
                        'status' => '1',
                        'currentdate' => date('Y-m-d H:i:s'),
                        'clientID' => clientID
                     );
                    $checkexpert = $this->expert->insertexpert($userinfo);
                    $updateData = array(
                        'used' => 1
                    );
                    $this->expert->delinviteexpert($updateData, $dbid);
                   
                    $dataExpert = array('expertuser'=>$this->_userid);
                    $where = array('DbeeID'=>$dbid);
                    $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
                    $this->sendNotificationAfterExpert($dbid,'join');
                    $this->HideAskButton($row['expertAskQues'],$dbid);

                    if ($row['GroupID'] != 0) 
                    {
                        $JoinDate  = date('Y-m-d H:i:s');
                        $groupname = $this->groupModel->grouprow($row['GroupID']);
                        $groupData = array(
                            'GroupID' => $row['GroupID'],
                            'Owner' => $groupname['User'],
                            'User' => $this->_userid,
                            'JoinDate' => $JoinDate,
                            'SentBy' => 'Owner',
                            'Status' => 1,
                            'clientID' => clientID
                        );
                        $this->groupModel->insertingroupmem($groupData);
                        $data['groupPost'] = 1;
                    }
                    
                    if ($row['events'] != 0) 
                    {
                        $dataArray = array('event_id' => $row['events'], 'member_id' => $this->_userid, 'status' => 1, 'clientID' => clientID);
                        $this->myclientdetails->insertdata_global('tblEventmember',$dataArray);
                        $data['eventPost'] = 1;
                    }

                    $data['used'] = 'used'; 
                } else
                    $data['used'] = 'unused';
            }
            
            $data['status'] = 'success';
            $data['expertid'] = $this->_userid;
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function checkexistexpertAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $dbeeid = (int) $this->_request->getPost('dbid');
            
            $data['social'] = $this->expert->checkinviteexpert($dbeeid);
            $data['plateform'] = $this->expert->getInviteUser($dbeeid);

            $profHTML = '<h2>Invited '.$this->expertText.'</h2>';
            
            foreach ($data['plateform'] as $key => $value) 
            {   
                $profHTML .= "<div class='userFatchList boxFlowers' title = '". $this->myclientdetails->customDecoding($value['full_name'])."' >
                <label class='labelCheckbox'><input type='checkbox' data-type = 'dbee' value='".$value['UserID']."' class='inviteuser-search' name='socialUser'>
                <div class='follower-box'><img  width='48' height='48'   src='".IMGPATH."/users/small/".$value['ProfilePic']."' border='0'>
                     <br><span class ='oneline'>". $this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname'])."</span>
                </div></label></div>";
            }

            foreach ($data['social'] as $key => $value) 
            {   
                if($value['type']=='facebook'){
                    $photo = 'https://graph.facebook.com/"' . $value["socialid"] . '"/picture';
                    $span = '<span class="followerIcon"><i class="fa fa-facebook-square"></i></span>';
                }
                else{
                    $photo = $value["photo"];
                    $span = '<span class="followerIcon"><i class="fa fa-twitter-square"></i></span>';
                }

                $profHTML .= "<div class='userFatchList boxFlowers' title = '". $value['name']."'  >
                <label class='labelCheckbox'><input type='checkbox' data-type ='".$value['type']."' value='".$value['socialid']."' class='inviteuser-search' name='socialUser'>
                <div class='follower-box'><img  width='48' height='48'  src='".$photo."' border='0'>
                     <br><span class ='oneline'>". $value['name']."</span>".$span."
                </div></label></div>";
            }

            $data['status'] = 'success';
            $data['profHTML'] = $profHTML;
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));

    }

    public function cancelpendingAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $dbeeid = (int) $this->_request->getPost('dbid');
            $userDbee = $this->_request->getPost('userDbee');
            $socialUser = $this->_request->getPost('socialUser');
            
            if($userDbee!='')
                $this->expert->deleteInviteRecord($userDbee,'dbee',$dbeeid);
            else if($socialUser!='')
                $this->expert->deleteInviteRecord($socialUser,'social',$dbeeid);

            $data['status'] = 'success';

        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));

    }
    public function makedbeeexpertAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $redirection_name_space = new Zend_Session_Namespace('User_Session');
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $acceptExpertRequest = $this->_request->getPost('acceptExpertRequest');
            $dbid                = (int)$this->_request->getPost('dbid');
            $row = $this->myhome_obj->getdbeedetail($dbid);
            $UserDbeeGroupDbeeInviteRestriced = $this->UserDbeeGroupDbeeInviteRestriced($row);
            if ($dbowners != $this->_userid && $UserDbeeGroupDbeeInviteRestriced != $this->_userid && empty($row)) 
            {
                $data['status']  = 'error';
                $data['message'] = 'You are not owners of this ' . POST_NAME . '';
            } 
            else if ($acceptExpertRequest == true) 
            {
                // check someone user joined
                $where = array('act_typeId'=>$dbid,'act_ownerid'=>$this->_userid,'act_type'=>39,'act_message'=>44);
                $result = $this->myclientdetails->getAllMasterfromtable('tblactivity',array('*'),$where);

                $where1 = array('act_typeId'=>$dbid,'act_ownerid'=>$this->_userid,'act_type'=>39,'act_message'=>50);
                $result1 = $this->myclientdetails->getAllMasterfromtable('tblactivity',array('*'),$where1);

                if(count($result)!=0 || count($result1)!=0)
                {
                    $expertCount = $this->myhome_obj->checkeexpertStatus($dbid);
                    if ($this->allowmultipleexperts==1 && $expertCount==0) 
                    {
                        $this->removeOwnExpert($row['expertuser'],$row['UserID'],$dbid);
                        $userinfo = array(
                            'userid' => $this->_userid,
                            'dbid' => $dbid,
                            'status' => '1',
                            'currentdate' => date('Y-m-d H:i:s'),
                            'clientID' => clientID
                         );
                        
                        $this->expert->insertexpert($userinfo);

                        // make event attendies and group member 

                        if ($row['GroupID'] != 0) 
                        {
                            $JoinDate  = date('Y-m-d H:i:s');
                            $groupname = $this->groupModel->grouprow($row['GroupID']);
                            $groupData = array(
                                'GroupID' => $row['GroupID'],
                                'Owner' => $groupname['User'],
                                'User' => $this->_userid,
                                'JoinDate' => $JoinDate,
                                'SentBy' => 'Owner',
                                'Status' => 1,
                                'clientID' => clientID
                            );
                            $this->groupModel->insertingroupmem($groupData);
                            $data['groupPost'] = 1;
                        }
                        
                        if ($row['events'] != 0) 
                        {
                            $dataArray = array('event_id' => $row['events'], 'member_id' => $this->_userid, 'status' => 1, 'clientID' => clientID);
                            $this->myclientdetails->insertdata_global('tblEventmember',$dataArray);
                            $data['eventPost'] = 1;
                        }
                        
                        // make event attendies and group member 

                        // start update expert column
                        $dataExpert = array('expertuser'=>$this->_userid);
                        $where = array('DbeeID'=>$dbid);
                        $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
                        // end update expert column

                        $this->sendNotificationAfterExpert($dbid,'join'); // send mail 

                        // delete existing notification
                        
                        $this->expert->deleteExpertInvitation($dbid);
                        $where = array('act_typeId'=>$dbid,'act_type'=>39,'act_message'=>44);
                        $where1 = array('act_typeId'=>$dbid,'act_type'=>39,'act_message'=>50);
                        $this->myclientdetails->deleteMaster('tblactivity',$where);
                        $this->myclientdetails->deleteMaster('tblactivity',$where1);

                        $this->HideAskButton($row['expertAskQues'],$dbid);
                        // delete existing notification

                        $data['used'] = 'used';                    
                        $data['dbeelink'] = '<a class="btn btn-green btn-mini" style="border-radius:5px;" href="'.BASE_URL.'/dbee/'.$row['dburl'].'">Click here to go to '.POST_NAME.'</a>';                    
                    }
                    else if($this->allowmultipleexperts==3 && $expertCount<=4)
                    {
                        $this->removeOwnExpert($row['expertuser'],$row['UserID'],$dbid);
                        $userinfo = array(
                            'userid' => $this->_userid,
                            'dbid' => $dbid,
                            'status' => '1',
                            'currentdate' => date('Y-m-d H:i:s'),
                            'clientID' => clientID
                         );
                        
                        $this->expert->insertexpert($userinfo);
                        
                        // make event attendies and group member 

                        if ($row['GroupID'] != 0) 
                        {
                            $JoinDate  = date('Y-m-d H:i:s');
                            $groupname = $this->groupModel->grouprow($row['GroupID']);
                            $groupData = array(
                                'GroupID' => $row['GroupID'],
                                'Owner' => $groupname['User'],
                                'User' => $this->_userid,
                                'JoinDate' => $JoinDate,
                                'SentBy' => 'Owner',
                                'Status' => 1,
                                'clientID' => clientID
                            );
                            $this->groupModel->insertingroupmem($groupData);
                            $data['groupPost'] = 1;
                        }
                        
                        if ($row['events'] != 0) 
                        {
                            $dataArray = array('event_id' => $row['events'], 'member_id' => $this->_userid, 'status' => 1, 'clientID' => clientID);
                            $this->myclientdetails->insertdata_global('tblEventmember',$dataArray);
                            $data['eventPost'] = 1;
                        }
                        
                        // make event attendies and group member 
                        

                        // start update expert column
                        $where = array('DbeeID'=>$dbid);

                        if($row['expertuser']==0)
                            $dataExpert = array('expertuser'=>$this->_userid);
                        else
                        {
                            $expertlist = explode(',', $row['expertuser']);
                            $expertlist[] = $this->_userid;
                            $expertlistUpdated = implode(',', $expertlist);
                            $dataExpert = array('expertuser' => $expertlistUpdated);
                        }
                        // expertAskQues
                        $this->HideAskButton($row['expertAskQues'],$dbid);

                        $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
                        // end update expert column

                        $this->sendNotificationAfterExpert($dbid,'join'); // mail send

                        // delete user notification
                        $where = array('act_typeId'=>$dbid,'act_ownerid'=>$this->_userid,'act_type'=>39,'act_message'=>44);
                        $this->myclientdetails->deleteMaster('tblactivity',$where);
                        $where1 = array('act_typeId'=>$dbid,'act_ownerid'=>$this->_userid,'act_type'=>39,'act_message'=>50);
                        $this->myclientdetails->deleteMaster('tblactivity',$where1);
                        // delete user notification
                        $data['expertid'] = $this->_userid;
                        $data['used'] = 'used';                    
                        $data['dbeelink'] = '<a class="btn btn-green btn-mini" style="border-radius:5px;" href="'.BASE_URL.'/dbee/'.$row['dburl'].'">Click here to go to '.POST_NAME.'</a>';   

                    } else
                        $data['used'] = 'unused';
                    }else
                    {
                        $data['status'] = 'error';
                        $data['message'] = 'Sorry this invitation has been cancelled.';
                    }
            }
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function removeOwnExpert($expertlist,$expertid,$dbid)
    {
        $expertlist = array_filter(array_diff($expertlist, array($expertid)));
                
        if(!empty($expertlist))
            $expertString = implode(',', $expertlist);
        else
            $expertString = 0;

        $expertAskQueslist2 = array_filter(array_diff($expertAskQueslist, array($expertid)));
        //print_r($expertAskQueslist2); die;
        if(!empty($expertAskQueslist2))
            $expertString2 = implode(',', $expertAskQueslist2);
        else
            $expertString2 = '';

        $dataExpert = array('expertuser'=>$expertString,'expertAskQues'=>$expertString2);

        $where = array('DbeeID'=>$dbid);
        $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);

         $where = array('dbid'=>$dbid,'userid'=>$expertid);
        $this->myclientdetails->deleteMaster('tblexpert',$where);
    }

    public function HideAskButton($expertAskQues,$dbeeID)
    {
        if($expertAskQues=='')
            $dataExpert = array('expertAskQues'=>$this->_userid);
        else
        {
            $expertlist = explode(',', $expertAskQues);
            $expertlist[] = $this->_userid;
            $expertlistUpdated = implode(',', $expertlist);
            $dataExpert = array('expertAskQues' => $expertlistUpdated);
        }
        $where = array('DbeeID'=>$dbeeID);
        $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
    }

    public function sendNotificationAfterExpert($db,$type)
    {
        // send mail to all commented users on this dbee
        if($type=='join')
            $messageID = 16;
        else
            $messageID = 17;
        $commentuser = $this->dbeeCommentobj->getcommentuser($db, $this->_userid);
        if (!empty($commentuser)) 
        {
            foreach ($commentuser as $row):
                if($this->_userid!==$row['UserID'])
                    $this->notification->commomInsert(11,$messageID,$db,$this->_userid,$row['UserID'],'','');
            endforeach;
        }
        // send mail to all commented users on this dbee
    }
    public function rejectdbeeAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = (int) $this->_request->getPost('id');
            $where = array('act_typeId'=>$id,'act_ownerid'=>$this->_userid,'act_type'=>39,'act_message'=>44);
            $this->myclientdetails->deleteMaster('tblactivity',$where);
            //  event by social network code end
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function videorequestAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $act_id = (int) $this->_request->getPost('act_id');
            $type = $this->_request->getPost('type');

            $data1 = array('status'=>1);

            $where  = array(
                    'act_id' => $act_id
                );
            $actDetails = $this->myclientdetails->getRowMasterfromtable('tblactivity', array(
                'act_userId','act_typeId'
            ), $where);

            if(!empty($actDetails) && $type=='accept')
            {
                $result = $this->User_Model->userdetailall($actDetails['act_userId']); // for sending mail get user details
                
                $this->expert->updatejoinrequser($data1,$actDetails['act_userId'],$actDetails['act_typeId']);
                
                $this->notification->commomInsert(40, 46, $actDetails['act_typeId'], $this->_userid, $actDetails['act_userId']);
                
                $dbee_data = $this->Myhome_Model->getDbeeDetails($actDetails['act_typeId']);

                $dburls =  '<a href="'.BASE_URL.'/dbee/'.$dbee_data['dburl'].'">'.$dbee_data['Text'].'</a>';

                $this->sendmailaccept($result[0],$dburls);

                $where = array('act_id'=>$act_id);

                $this->myclientdetails->deleteMaster('tblactivity',$where);
            }
            else if($type=='reject')
            {
                $where = array('userID'=>$actDetails['act_userId'],'dbeeID'=>$actDetails['act_typeId']);
                $this->myclientdetails->deleteMaster('tblDbeeJoinedUser',$where);
                $where = array('act_id'=>$act_id);
                $this->myclientdetails->deleteMaster('tblactivity',$where);

                $this->notification->commomInsert(40,55,$actDetails['act_typeId'],$this->_userid,$actDetails['act_userId']);
            }
            //  event by social network code end
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function sendmailaccept($data,$dburls)
    {
        /****for email ****/      

        $EmailTemplateArray = array('uEmail'  => $data['Email'],
                                    'uName'   => $data['Name'],
                                    'lname'   => $data['lname'],
                                    'db_url'  => $dburls,
                                    'case'      => 5);
        $this->dbeeComparetemplateOne($EmailTemplateArray); 
        /****for email ****/        
    }

    public function rejectvideoattendiesAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = (int) $this->_request->getPost('id');
            $where = array('act_typeId'=>$id,'act_ownerid'=>$this->_userid,'act_type'=>41,'act_message'=>47);
            $this->myclientdetails->deleteMaster('tblactivity',$where);
            //  event by social network code end
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function acceptvideoattendiesAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = (int) $this->_request->getPost('dbid');
            $result = $this->Myhome_Model->checkUserOrNotJoined($this->_userid, $id);
            $dbee_data = $this->Myhome_Model->getDbeeDetails($id);
            
            if(empty($result) && !empty($dbee_data))
            {
                $dataArray['userID']    = (int) $this->_userid;
                $dataArray['dbeeID']    = $id;
                $dataArray['status']    = 1;
                $dataArray['timestamp'] = date('Y-m-d H:i:s');
                $dataArray['clientID']  = clientID;
                $this->myclientdetails->insertdata_global('tblDbeeJoinedUser', $dataArray);
                
                if($dbee_data['eventtype']==0)
                {
                    $type = 16;
                    $type2 = 25;
                }
                else if($dbee_data['eventtype']==1)
                {
                    $type = 45;
                    $type2 = 27;
                }
                else if($dbee_data['eventtype']==2){
                    $type = 18;
                    $type2 = 27;
                }

                $this->notification->commomInsert($type, $type2, $dataArray['dbeeID'], $this->_userid, adminID);
                $where = array('act_typeId'=>$id,'act_ownerid'=>$this->_userid,'act_type'=>41,'act_message'=>47);
                $this->myclientdetails->deleteMaster('tblactivity',$where);
                $data['dbeelink'] = '<a class="btn btn-green btn-mini" style="border-radius:5px;" href="'.BASE_URL.'/dbee/'.$dbee_data['dburl'].'">Click here to go to '.POST_NAME.'</a>';                    
            }
            //  event by social network code end
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    /**
     * remove expert
     */
     public function removeAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter                 = new Zend_Filter_StripTags();
        $response               = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $dbid = (int) $this->_request->getPost('dbid');
            $expertid = (int) $this->_request->getPost('expertid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbid);
            $expertlist = explode(',', $dbee_data['expertuser']);
            $expertAskQueslist = explode(',', $dbee_data['expertAskQues']);

            if(in_array($expertid,$expertlist) || $dbee_data['User']==$this->_userid)
            {
                
                $expertlist = array_filter(array_diff($expertlist, array($expertid)));
                
                if(!empty($expertlist))
                    $expertString = implode(',', $expertlist);
                else
                    $expertString = 0;

                $expertAskQueslist2 = array_filter(array_diff($expertAskQueslist, array($expertid)));
                //print_r($expertAskQueslist2); die;
                if(!empty($expertAskQueslist2))
                    $expertString2 = implode(',', $expertAskQueslist2);
                else
                    $expertString2 = '';

                $dataExpert = array('expertuser'=>$expertString,'expertAskQues'=>$expertString2);

                $where = array('DbeeID'=>$dbid);
                $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
                unset($where);

                $where = array('dbid'=>$dbid,'userid'=>$expertid);
                $this->myclientdetails->deleteMaster('tblexpert',$where);
                $this->expert_model->deleteQuestion($this->_userid,$dbid);

                $data['status']  = 'success';
                $data['expertid']  = $expertid;
                
                if($this->_userid!=$expertid)
                    $this->notification->commomInsert(3,45,$dbid,$this->_userid,$expertid,'','');
            }
            if($dbee_data['User']==$this->_userid)
            {                 
                $data['dbowners'] = true;
                $result = $this->User_Model->userdetailall($expertid);
                $this->sendMailToExpertTurnOff($result[0],$dbee_data,'remove');
            }
            else
                $data['dbowners'] = false;
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    /**
     *  check user dbee group dbee invite restricted or not 
     */
    public function UserDbeeGroupDbeeInviteRestriced($result)
    {
        if ($result['GroupID'] != '') {
            $groupResult = $this->groupModel->groupuserdetail($result['GroupID']);
            if ($groupResult[0]['Invitetoexpert'] == 1)
                return $groupResult[0]['UserID'];
            else
                return false;
        } else
            return false;
    }
    /**
     *  for sent mail after remove expert
     */
     public function sendMailToExpertRemovedOwner($data, $title)
    {
        $EmailTemplateArray = array(
            'Email' => $data['Email'],
            'Name' => $data['Name'],
            'title' => $title,
            'case' => 12
        );
        $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateArray);
    }

    public function shwoInviteExpertWithoutSocialLink()
    {
        
        return '<div id="invite-group-members-expert" style="z-index:100;">
               <div class="clearfix"> </div>
                <ul id="invitetabs" class="tabHeader tabLinks" style="margin-top:20px;">
                    <li><a href="#" rel="followers-list" class="active">Followers</a></li>
                    <li><a href="#" rel="following-list">Following</a></li>
                    <li><a href="#" rel="search-list">Search</a></li>
                 </ul>
              
               <div class="tabcontainer tabContainerWrapper" style="padding:10px;">
                    <div id="passform" class="postTypeContent">
                  <div id="followers-list" class="tabcontent" style="display:block"></div>
                  <div id="following-list" class="tabcontent"></div>
                  <div id="search-list" class="tabcontent">
                        <div class="formRow singleRow">
                          <div class="formField" style="margin-bottom:0;">                             
                     <div id="searchUserBox">
                        <form name="searchusers" id="searchusers" onSubmit="searchusers(); return false;">
                           <div class="inputAppend">
                                <input type="text" id="keyword" class="textfieldSearch"  value="" placeholder="search users to invite">
                                <a href="javascript:void(0);"  class="GoInvite btn btn-yellow"><i class="searchIconWhite"> </i></a>
                            </div>
                        </form>
                     </div>
                            </div>
                          </div>
                         <div id="search-invite-list" class="formRow" style="display:none;"><div class="formField"></div></div>
                     <br style="clear:both; font-size:1px">
                  </div>
               </div>
               </div>
               <input type="hidden" id="groupid">
               <input type="hidden" id="total-followers">
               <input type="hidden" id="total-following">
               <input type="hidden" id="total-search">
               <input type="hidden" id="total-users-toinvite">
               <input type="hidden" id="from">
            </div>
            <div id="invite-selected-div" style="display:none;">
               <div id="invitetogroup-header" style="float:left; font-size:14px; margin-bottom:10px;">Invite people you are following and people who are following you, and search for new people to add to your groups.</div>
               <div class="next-line"></div>
               <div class="maindb-wrapper-border" style="padding:10px;">
                  <div id="selected-users-label" style="font-size:12px; margin-bottom:10px;">Users selected by you to invite to groups.</div>
                  <div class="next-line"></div>
                  <div id="invite-selected"></div>
                  <div class="next-line"></div>
                  <div style="float:right; margin-top:35px;"><a href="javascript:void(0);" onClick="backtoselection();">back to selection</a></div>
                  <br style="clear:both; font-size:1px">
               </div>
            </div>
            <div id="invite-message"></div></div>';
    }
    
    
    public function inviteAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $Text = $this->shwoInviteExpertWithoutSocialLink(); 
            $data['content']              = $Text;
            $data['status']               = 'success';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function acceptexpertrequestAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            if (isset($this->session_name_space->token) && isset($this->session_name_space->dbeeid)) 
            {    
                $dbeeid = $this->session_name_space->dbeeid;
                $row = $this->myhome_obj->getdbeedetail($dbeeid);
                
                if ($row['GroupID'] != 0) 
                {
                    $JoinDate  = date('Y-m-d H:i:s');
                    $groupname = $this->groupModel->grouprow($row['GroupID']);
                    $groupData = array(
                        'GroupID' => $row['GroupID'],
                        'Owner' => $groupname['User'],
                        'User' => $this->_userid,
                        'JoinDate' => $JoinDate,
                        'SentBy' => 'Owner',
                        'Status' => 1,
                        'clientID' => clientID
                    );
                    $this->groupModel->insertingroupmem($groupData);
                    $data['groupPost'] = 1;
                }
                
                if ($row['events'] != 0) 
                {
                    $dataArray = array('event_id' => $row['events'], 'member_id' => $this->_userid, 'status' => 1, 'clientID' => clientID);
                    $this->myclientdetails->insertdata_global('tblEventmember',$dataArray);
                    $data['eventPost'] = 1;
                }
                // check someone user joined 
                $expertCount = $this->myhome_obj->checkeexpertStatus($dbeeid);
                
                if ($expertCount == 0 && allowmultipleexperts == 1) 
                {
                    $this->removeOwnExpert($row['expertuser'],$row['UserID'],$dbeeid);
                    $userinfo   = array(
                    'userid' => $this->_userid,
                    'dbid' => $dbeeid,
                    'status' => '1',
                    'currentdate' => date('Y-m-d H:i:s'),
                    'clientID' => clientID
                    );
                    $this->expert->insertexpert($userinfo);
                    $this->expert->deleteExpertInvitation($dbeeid);

                    if($row['Type']==6)
                    {
                        $dataArray['userID']    = (int) $this->_userid;
                        $dataArray['dbeeID']    = (int) $dbeeid;
                        $dataArray['status']    = '1';
                        $dataArray['timestamp'] = date('Y-m-d H:i:s');
                        $this->myclientdetails->insertdata_global('tblDbeeJoinedUser',$dataArray); 
                    }
                    // update dbee table expertuser
                    $this->sendNotificationAfterExpert($dbeeid,'join');
                    $dataExpert = array('expertuser'=>$this->_userid);
                    $where = array('DbeeID'=>$dbeeid);
                    $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
                    $where = array('act_typeId'=>$dbeeid,'act_type'=>39,'act_message'=>44);
                    $this->myclientdetails->deleteMaster('tblactivity',$where);
                    $data['used'] = 'used';
                    $this->HideAskButton($row['expertAskQues'],$dbeeid);
                }
                else if($expertCount <= 4 && allowmultipleexperts == 3) 
                {
                    $this->removeOwnExpert($row['expertuser'],$row['UserID'],$dbeeid);
                     $userinfo   = array(
                        'userid' => $this->_userid,
                        'dbid' => $dbeeid,
                        'status' => '1',
                        'currentdate' => date('Y-m-d H:i:s'),
                        'clientID' => clientID
                    );
                    $this->expert->insertexpert($userinfo);
                    $this->sendNotificationAfterExpert($dbeeid,'join');
                    if($row['Type']==6)
                    {
                        $dataArray['userID']    = (int) $this->_userid;
                        $dataArray['dbeeID']    = (int) $dbeeid;
                        $dataArray['status']    = '1';
                        $dataArray['timestamp'] = date('Y-m-d H:i:s');
                        $this->myclientdetails->insertdata_global('tblDbeeJoinedUser',$dataArray); 
                    }
                    if($row['expertuser']==0)
                        $dataExpert = array('expertuser'=>$this->_userid);
                    else
                    {
                        $expertlist = explode(',', $row['expertuser']);
                        $expertlist[] = $this->_userid;
                        $expertlistUpdated = implode(',', $expertlist);
                        $dataExpert = array('expertuser' => $expertlistUpdated);
                    }
                    // expertAskQues
                    $this->HideAskButton($row['expertAskQues'],$dbeeid);
                    $data['used'] = 'used';
                    //$dataExpert = array('expertuser'=>$this->_userid);
                    $where = array('DbeeID'=>$dbeeid);
                    $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);

                }
                else
                    $data['used'] = 'unused';
                
                $this->session_name_space->unsetAll();
            }
            $data['status'] = 'success';
            $data['expertid'] = $this->_userid;
            $data['redirect'] = BASE_URL.'/dbee/'.$row['dburl'];
        } 
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    
    
    public function rejectexpertrequestAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            if (isset($this->session_name_space->token) && isset($this->session_name_space->dbeeid)) 
            {
                // update dbee table expertuser
                $dataExpert =array('expertuser'=>0);
                $where = array('DbeeID'=>$this->session_name_space->dbeeid);
                $this->myclientdetails->updateMaster('tblDbees',$dataExpert,$where);
                $this->myhome_obj->delinviteexpert($this->session_name_space->token);
                $this->session_name_space->unsetAll();
                
            }
            $data['redirect'] = BASE_URL;
            $data['status']   = 'success';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
}
