<?php
class EventController extends IsController
{
    public function init()
    {
        parent::init();
    }
    public function splashdemoAction()
    {
        $this->preCall();
        $this->_helper->layout()->disableLayout();
        $this->view->bgimage = $this->_request->getParam('image');
        $this->view->title = $this->_request->getParam('eventTitle');
        $this->view->start = $this->_request->getParam('start');
        $this->view->end = $this->_request->getParam('end');
        $this->view->address = $this->_request->getParam('address');
        $commonmodel_obj = new Application_Model_Commonfunctionality();
        $this->view->timezoneevent = $commonmodel_obj->timezoneevent($this->_request->getParam('timezoneevent'));
    }

    public function splashAction()
    {
        $this->preCall();
        $this->_helper->layout()->disableLayout();
        $id = (int) $this->_request->getParam('id');
        if($id!='')
        {
            $this->view->eventModel = $eventModel = new Application_Model_Event();
            $result = $eventModel->getEvent($id);
            $this->view->eventResult = $result;
            if(empty($result[0]))
                $this->_redirect('/myhome');
            if($result[0]['bgimage']=='' && $result[0]['bgcolor']=='')
                $this->_redirect(BASE_URL.'/event/eventdetails/id/'.$id);
        }else{
            $this->_redirect(BASE_URL.'/event/eventdetails/id/'.$id);
        }
    }
    public function socialeventAction()
    {
        $data = array();
        $grouptypes = new Application_Model_Groups();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $eventSession = new Zend_Session_Namespace('Event_Session');
            //  event by socialnetwork code start
            $type = $eventSession->type; 
            $token = $eventSession->token;
            $eventid = $eventSession->eventid;
            $socialid = $eventSession->socialid;

            if ((isset($type) && !empty($type)) && (isset($token) && !empty($token)) && (isset($eventid) && !empty($eventid))) 
            {
                if ($this->_userid == '')
                    $data['login'] = false;
                else
                    $data['login'] = true;

                if ($type == 'facebook')
                    $checkType = 1;
                else if ($type == 'twitter')
                    $checkType = 2;
                else if ($type == 'linkedin')
                    $checkType = 3;
                $eventModel = new Application_Model_Event();
                $checkventsocial = $eventModel->checkEventSocial($eventid, $type, $token, $socialid);
                if($this->_userid)
                    $eventMember = $eventModel->checkEventMember($this->_userid,$eventid);
                if (!empty($eventMember))
                    $data['used'] = 1;
                else if ($checkventsocial==0) 
                    $data['wrong'] = 1;
                else if ($this->_userid != '' && $this->session_data['Socialtype'] != $checkType) 
                {
                    $data['logout'] = true;
                    $data['typelogin'] = $type;
                }
                else if ($this->_userid != '' && $this->session_data['Socialtype'] == $checkType && ($socialid != $this->session_data['Socialid'])) 
                {
                    $data['logout'] = true;
                    $data['typelogin'] = $type;
                }   
                else if ($this->session_data['Socialtype'] == $checkType && $this->_userid != '' && ($socialid == $this->session_data['Socialid']))
                    $data['accept']    = true;
                else if ($this->_userid == '')
                { 
                    $data['typelogin'] = $type;
                    $data['login'] = false;
                }
            }
            //  event by social network code end
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

     public function privateeventAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = (int) $this->_request->getPost('id');
            $type = $this->_request->getPost('type');
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
            if ($validate == false)
            {
                $data['status'] = 'error';
                $data['message'] = 'Something went wrong please try again';
            }
            else if($type=='join')
            {
                $eventModel = new Application_Model_Event();
                $eventMember = $eventModel->checkEventMember($this->_userid,$id);
                //print_r($eventMember);
                if (empty($eventMember))
                {
                    $dataArray = array('event_id' => $id, 'member_id' => $this->_userid, 'status' => 1, 'clientID' => clientID);
                    $this->myclientdetails->insertdata_global('tblEventmember',$dataArray);
                    $this->notification->commomInsert(32, 32, $id, $this->_userid, adminID);
                    $where = array('act_typeId'=>$id,'act_ownerid'=>$this->_userid,'act_type'=>36,'act_message'=>39);
                    $this->myclientdetails->deleteMaster('tblactivity',$where);
                    $data['status'] = 'success';
                    $data['message'] = 'You have successfully joined';
                }else
                {
                    $data['status'] = 'error';
                    $data['message'] = 'You have already joined';
                }
            }
            else if($type=='reject')
            {
                $where = array('act_typeId'=>$id,'act_ownerid'=>$this->_userid,'act_type'=>36,'act_message'=>39);
                $this->myclientdetails->deleteMaster('tblactivity',$where);
            }
            //  event by social network code end
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function inviteAction()
    {
        //$this->_helper->layout()->disableLayout();
        $this->view->id = $id = (int) $this->_request->getParam('id');
        if($id!='')
        {
            $redirection_session = new Zend_Session_Namespace('User_Session');
            $redirection_session->redirection = $this->curPageURL();
            $filter = new Zend_Filter_StripTags();
            $eventSession = new Zend_Session_Namespace('Event_Session');
            $type     = $filter->filter($this->_request->getParam('type'));
            $token    = $filter->filter($this->_request->getParam('token'));
            $socialid = $filter->filter($this->_request->getParam('authid'));
            if ((isset($type) && !empty($type)) && (isset($token) && !empty($token)) && (isset($id) && !empty($id))) 
            {
                $eventSession->type = $type;
                $eventSession->token = $token;
                $eventSession->eventid = $id;
                $eventSession->socialid = $socialid;
                $this->view->sociallogin =true;
            }

            $this->view->eventModel = $eventModel = new Application_Model_Event();
            $result = $eventModel->getEvent($id);
            $this->view->eventResult = $result;
            if(empty($result[0]))
                $this->_redirect('/myhome');
        }else{
            $this->_redirect('/myhome');
        }
    }
    public function aeventdetailsAction()
    { 
        $this->preCall();
        $data = array();
        $eventJOinButton = '';
        $request = $this->getRequest();       
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $filter   = new Zend_Filter_StripTags();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->preReturnCall()==true) 
        {          
            $id = (int) $request->getPost('id');
            $eventModel = new Application_Model_Event();
            $eventResult = $eventModel->getEvent($id);
            $metaData = $eventModel->getAllMetaDataEvent($id);
            $eventAddition = '';
            $postInEvent = '<div is-event="isevent" data-id="'.$id.'" data-EventType="'.$eventResult[0]['type'].'" class="pull-right btn btn-yellow postInGroup" >Post in this event</div>';
            if(!empty($metaData))
            {
                $eventAddition .= '<div class="whiteBox"><h2 id="attendiedCounts">Additional information</h2><div class="rboxContainer ps-container">';
                foreach ($metaData as $key => $value)
                    $eventAddition.= '<p style="color:#666;">'.$value['label'].': '.$value['value'].'</p>';
                $eventAddition.= '</div></div>';
            }
            $data['eventAddition'] = $eventAddition;
            
            if($this->_userid)
                $memberRow = $eventModel->checkEventMember($this->_userid,$id);
            else
                $memberRow = array();

            $eventJoinButton = '<a href="javascript:void(0);" event-type="'.$eventResult[0]['type'].'" event-id="'.$id.'" class="eventJoin pull-right btn btn-green">Confirm attendance</a>';
            
            if($this->session_data['role']==1 && !empty($memberRow))
                $eventJoinButton = '';
            
            
            if(!empty($memberRow) || $this->_userid=='')
                $eventJoinButton = '';

            if(($eventResult[0]['type']==2 && empty($memberRow)) || $this->_userid=='')
                $postInEvent = '';

            $logo = '';
            if($eventResult[0]['logo']!='')
            {
                $imageSize = @getimagesize($_SERVER['DOCUMENT_ROOT'].'/event/'.$eventResult[0]['logo']);
                if($imageSize[0]>750)
                    $logo = '<img src="'.BASE_URL_IMAGES.'/timthumb.php?src=/event/'.$eventResult[0]['logo'].'&amp;q=100&amp;w=755&amp" border="0" />';
                else
                    $logo = '<img src="/event/'.$eventResult[0]['logo'].'" border="0" />';
            }
          
            $data['content'] = '<div class="eventDetailsWrp">'.$logo.'
            <h2> '.$eventResult[0]['title'].'</h2>           
            <p>'.$eventResult[0]['description'].'</p>
             <span id="postInEvent">'.$postInEvent.'</span>'.$eventJoinButton.'</div>';
            $data['detail'] = '<div class="rbcRow">
                                    <i class="fa fa-map-marker pull-left fa-2x" style="text-align: center; width: 24px;"></i>
                                    <div class="dtl">
                                    '.nl2br($eventResult[0]['address']).' 
                                        <div class="enlargeMap"><a href="javascript:void(0);" >Enlarge map</a></div>
                                    </div>
                                </div>';
            $data['latitude'] = $eventResult[0]['latitude'];
            $data['longitude'] = $eventResult[0]['longitude'];
            $data['address'] = $eventResult[0]['address'];

            $commonmodel_obj = new Application_Model_Commonfunctionality();
            $data['EventDate'] = '<span> <i class="fa fa-calendar"></i><span class="startPad">Starts:</span> '.date('d M Y&\nb\sp;&\nb\sp;  h:i A',strtotime($value['start'])).'</span><span ><i class="fa fa-calendar invisible"></i><span class="startPad">Ends:</span> '.date('d M Y&\nb\sp;&\nb\sp;  h:i A',strtotime($value['end'])).'</span><span class="dateEnd">'.$commonmodel_obj->timezoneevent($eventResult[0]['timezoneevent']).'</span>';
            $result = $eventModel->getAllEventMember($id);
            $data['totalMember'] = $countMember = count($result);
            //$data['totalMember'] = 1;
            if($countMember>0) 
            {
              $dataMember='<ul class="slides"><li>';
              $k=1;

                foreach($result as $key=>$Row):
                  if($Row['usertype']==100 && $this->session_data['usertype']!=100 && isADMIN!=1) {
                    
                    $dataMember.='<a href="javascript:void(0);" title="VIP User">
                    <img src="'.IMGPATH.'/users/small/'.VIPUSERPIC.'" width="35" height="35" border="0" />
                    </a>';

                }
               else if($Row['hideuser']==1 && isADMIN!=1 && $Row['UserID']!=$this->session_data['UserID']) {
                    
                    $dataMember.='<a href="javascript:void(0);" title="anonymous">
                    <img src="'.IMGPATH.'/users/small/'.HIDEUSERPIC.'" width="35" height="35" border="0" />
                    </a>';

                }
                else
                {
                    $dataMember.='<a href="/user/' . $this->myclientdetails->customDecoding($Row['Username']) . '" title="' . $this->myclientdetails->customDecoding($Row['Name']). '">
                    <img src="'.IMGPATH.'/users/small/'.$Row['ProfilePic'].'" width="35" height="35" border="0" />
                    </a>';

                }
                    

                    if ($k % 5 == 0)
                        $dataMember.='</li><li>';
                    $k++;
                endforeach;
                $dataMember.='</li></ul>';
            }else{
                $dataMember.='<div class="noFound">This event currently has no attendees</div>';
            }
            $data['status'] = 'success';
            $data['member'] = $dataMember;

            return $response->setBody(Zend_Json::encode($data));
        }
        
    }
    public function loadeventdbeeAction()
    {
        $data = array();
        $eventJOinButton = '';
        $request = $this->getRequest();   
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $filter   = new Zend_Filter_StripTags();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->preReturnCall()==true) 
        {
            $return = '';
            $id = $this->_request->getPost('id');
            $lastID = $this->_request->getPost('lastID');
            $eventModel = new Application_Model_Event();
            $event_result = $eventModel->getEventRecord($id);
            $result = $eventModel->caltotaldbresult($id,$lastID);
            $count = count($result);
            
             if($this->_userid)
                $memberRow = $eventModel->checkEventMember($this->_userid,$id);
            else
                $memberRow = array();
            $dbArray = array();
            foreach($result as $key => $row)
            {
                $return.=$this->commonmodel_obj->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore,'',true);
                $dbArray[] = $row['DbeeID'];
            }
            if($count==0 && $event_result['type']!=1 && empty($memberRow))
            {
                 $return='<div class="noFound firstUserCmnt" id="noFound"><i class="fa fa-pencil-square-o fa-2x"></i> Please confirm your attendance to start a '.ucwords(POST_NAME).'.</div>';
                 $data['noFound'] = true;
            }
           else if($count==0 && $lastID=='')
           {
               $return = "<div class='noFound firstUserCmnt' id='noFound'><i class='fa fa-pencil-square-o fa-2x'></i>Click '".ucwords(POST_NAME)." in this event' button to start a new post.</div>";
               $data['noFound'] = true;
           }
           else if($count==0 && $lastID!='')
           {
               $return = "";
               $data['noFound'] = true;
           }
           $data['contents'] = $return;
           $data['dbArray'] = implode($dbArray, ',');
        }
        return $response->setBody(Zend_Json::encode($data));
    }
     public function loadsingleeventdbeeAction()
    {
        $data = array();
        $eventJOinButton = '';
        $request = $this->getRequest();   
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $filter   = new Zend_Filter_StripTags();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->preReturnCall()==true) 
        {
            $id = $this->_request->getPost('id');
            $eventModel = new Application_Model_Event();
            $result = $eventModel->dbresult($id);
            $data['contents']=$this->commonmodel_obj->displayLayoutDbs($result,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore,'',true);
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function eventdetailsAction()
    {
        $this->preCall();
        $request = $this->getRequest()->getParams();
        $eventModel = new Application_Model_Event();
        $id  = (int) $this->_request->getParam('id');
        if(is_numeric($id))
        {
           $this->session_name_space->redirection = '';  
           $eventResult = $eventModel->getEvent($id);
           if(!empty($eventResult) && $this->_userid)
                $memberRow = $eventModel->checkEventMember($this->_userid,$id);
            else
                $memberRow = array();
           if((!empty($eventResult[0]) && $eventResult[0]['type']!=3) || (!empty($memberRow) && $this->_userid!='' && $eventResult[0]['type']==3))
           {
              $this->view->eventResult = $eventResult;
              $this->view->id = $id;
           }else{
              $this->_helper->redirector->gotosimple('index','index',true);
           }
        }else{
            $this->_helper->redirector->gotosimple('index','index',true);
        }
    }
    public function confirmattendiesAction()
    {
        $data = array();
        $request = $this->getRequest();       
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $filter   = new Zend_Filter_StripTags();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->preReturnCall()==true) 
        {          
            $eventModel = new Application_Model_Event();
            $id = (int) $request->getPost('id');
            $type = (int) $request->getPost('type');
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
            if ($validate == false)
            {
                $data['status'] = 'error';
                $data['content'] = 'Something went wrong please try again';
            }else if(!$eventModel->checkEventMember($this->_userid,$id))
            {
                $eventResult = $eventModel->getEvent($id);
                $dataArray = array('event_id' => $id, 'member_id' => $this->_userid, 'status' => 1, 'clientID' => clientID);
                $this->myclientdetails->insertdata_global('tblEventmember',$dataArray);
                
                $this->notification->commomInsert(32, 32, $id, $this->_userid, adminID);
                // get all joined member of this event

                if($type==3)
                    $eventModel->deleteEventInvitation($eventSession->token);

                $result = $eventModel->getAllEventMember($id);
                $data['totalMember'] = $countMember = count($result);
                if($countMember>0) 
                {
                    $k =0;
                    $data['member']='<ul><li>';
                    foreach($result as $key=>$Row):
                    $userprofile5 = $this->commonmodel_obj->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');            
                        $data['member'].='<a href="#">
                        <img src="'.IMGPATH.'/users/small/'.$userprofile5.'" width="35" height="35" border="0" />
                        </a>';
                    if ($k % 5 == 0)
                        $dataMember.='</li><li>';
                    $k++;
                    endforeach;
                    $data['member'].='</li></ul>';
                }else{
                    $data['member'].='<div class="noFound" style="margin-top:28px">This event currently has no members</div>';
                }
                $data['detail'] = $eventResult;
                $data['status'] = 'success';
                $data['message'] = 'You have confirmed your attendance at this event';
            }else{
                $data['status'] = 'error';
                $data['message'] = 'You have already confirmed your attendance at this event';
            }
            return $response->setBody(Zend_Json::encode($data));
        }
    }

}