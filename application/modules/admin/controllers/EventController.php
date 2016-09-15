<?php

class Admin_EventController extends IsadminController
{

	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */	

    public function init()
    {
	     parent::init();
       $this->event = new Admin_Model_Event();
    }

    public function indexAction()
    {
    
      //$this->view->result = $this->event->getAllEvent(); 
	  }
    /**
     *  edit advert
     */
	   public function jsondataAction()
      {
      	 $data = array();
          $postData = array();
          $this->_helper->layout()->disableLayout();
          $this->_helper->viewRenderer->setNoRender(true);
          $response = $this->getResponse();
          $response->setHeader('Content-type', 'application/json', true);
          if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
          { 
             $result = $this->event->getAllEvent(); 
             foreach ($result as $value) 
             {
                $data[] =  array('id' => $value['id'],
                'title'=> $value['title'],
                'start'=> date("Y-m-d H:i:s",strtotime($value['start'])),
                'end'=> date("Y-m-d H:i:s",strtotime($value['end'])),
                'className'=> 'label-important');
             }
          }
          return $response->setBody(Zend_Json::encode($data));
  	}

    public function splashAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {

          //return $response->setBody(Zend_Json::encode($_FILES['file']));
          if($_FILES['file']['size']>10485760)
          {
            $data['status']  = 'error';
            $data['message'] = 'Please upload file less than 10 MB';
            return $response->setBody(Zend_Json::encode($data));
          }

          if($_FILES['file']['name'])
          {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
            $bgname  = strtolower(time().mt_rand(1,79632).'.'.$ext);
            @copy($_FILES['file']['tmp_name'], Front_Public_Path.'event/' .$bgname);
            $dataArray['bgimage'] = $bgname;
            $dataArray['content'] = '<input type="hidden" name="bghidden" id="bghidden" value="'.$bgname.'" >';
          }
        } 
        return $response->setBody(Zend_Json::encode($dataArray));
    }
    /**
     *  insert advert data and return json data
     */
    public function addAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {   
          $eventid = $this->_request->getPost('eventid');
          $eventlogo = $this->_request->getPost('logohidden');
          $eventbackground = $this->_request->getPost('bghidden');
          $dataArray['title'] = $this->_request->getPost('eventTitle');
          
          $promoted = $this->_request->getPost('promoted');
          
          if(isset($promoted) && $promoted==1)
            $dataArray['promoted'] = $promoted;
          else
            $dataArray['promoted'] = 0;
   
          $dataArray['description'] = $this->_request->getPost('eventDescription');
          $dataArray['start'] = date("Y-m-d H:i:s",strtotime($this->_request->getPost('startdate')));
          $dataArray['end'] = date("Y-m-d H:i:s",strtotime($this->_request->getPost('enddate')));
          $dataArray['bgcolor'] = $this->_request->getPost('eventbgcolor');
          $dataArray['timezoneevent'] = $this->_request->getPost('timezoneevent');
          $dataArray['address'] = $this->_request->getPost('eventadress');
          $address = $dataArray['address']; // Google HQ
          if($dataArray['start']=="" || $dataArray['end']=="")
          {
            $data['status']  = 'error';
            $data['message'] = 'Please enter start and end date';
            return $response->setBody(Zend_Json::encode($data));
          }
          if($address!='')
          {

            $address = preg_replace('#\s+#',',',trim($address));
            $prepAddr = str_replace(' ','+',$address);
            $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
            $output= json_decode($geocode);
            $dataArray['latitude'] = $output->results[0]->geometry->location->lat;
            $dataArray['longitude'] = $output->results[0]->geometry->location->lng;
          }else{
            $dataArray['latitude'] = '';
            $dataArray['longitude'] = '';
          }
           $dataArray['datetime'] = date("Y-m-d H:i:s");
           $dataArray['clientID'] = clientID;


          if(!empty($_FILES) && $_FILES['eventlogo']['size']>10485760)
          {
            $data['status']  = 'error';
            $data['message'] = 'Please upload file less than 10 MB';
            return $response->setBody(Zend_Json::encode($data));
          }
         
          if(!empty($_FILES) && $_FILES['eventlogo']['name'])
          {
            $ext = pathinfo($_FILES['eventlogo']['name'], PATHINFO_EXTENSION); 
            $logoname  = strtolower(time().mt_rand(1,79632).'.'.$ext);
            @copy($_FILES['eventlogo']['tmp_name'], Front_Public_Path.'event/' .$logoname);
            $dataArray['logo'] = $logoname;
          }else if($eventlogo!='')
            $dataArray['logo'] = $eventlogo;
          else
            $dataArray['logo'] = '';
          

          if($eventbackground!='')
            $dataArray['bgimage'] = $eventbackground;
          else
            $dataArray['bgimage'] = '';

           $customFieldsEventLabel = $this->_request->getPost('customFieldsEventLabel');
           $customFieldsEventValue = $this->_request->getPost('customFieldsEventValue');
           $countValue = count($customFieldsEventLabel);
         
           if($eventid!='')
           {
              $where['id'] = $eventid;
              $id = $eventid;
              $data['edit']  = 'success';
              $this->myclientdetails->updateMaster('tblEvent',$dataArray,$where);
           }else
           {
              $dataArray['status'] = $this->_request->getPost('status');
              $dataArray['type'] = $this->_request->getPost('type');
              $eventid = $id = $this->event->eventInsert($dataArray);
               $eventArray = array('event_id' => $id, 'member_id' => adminID, 'status' => 1, 'clientID' => clientID);
                $this->myclientdetails->insertdata_global('tblEventmember',$eventArray);
              $data['add']  = 'success';
           }

           if($countValue>0 && $customFieldsEventLabel!='')
           {
             $this->event->eventmetaDelete($eventid);
             for ($i=0;$i< $countValue;$i++) 
             {
                if($customFieldsEventLabel[$i]!='' && $customFieldsEventValue[$i]!='')
                {
                  $MetaArray['label'] = $customFieldsEventLabel[$i];
                  $MetaArray['value'] = $customFieldsEventValue[$i];
                  $MetaArray['event_id'] = $eventid;
                  $MetaArray['clientID'] = clientID;
                  $id = $this->event->eventmetaInsert($MetaArray);
                }
             }
           }
           $data['title']  = $dataArray['title'];
           $data['start']  = date("d-m-Y H:i:s",strtotime($dataArray['start']));
           $data['end']  = date("d-m-Y H:i:s",strtotime($dataArray['end']));
           $data['id']  = $id;
           $data['status']  = 'success';
           $data['message'] = 'Event saved successfully';
        } 
        return $response->setBody(Zend_Json::encode($data));
    }

    public function editAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {   
           $id = $this->_request->getPost('id');
           $start =  date("Y-m-d H:i:s",strtotime($this->_request->getPost('start')));
           $end =  date("Y-m-d H:i:s",strtotime($this->_request->getPost('end')));
           if($end!='')
            $data['end'] = $end;
           else
            $data['start'] = $start;
           $where['id'] = $id;
           $where['clientID'] = clientID;
           $this->myclientdetails->updateMaster('tblEvent',$data,$where);
           $data['status']  = 'success';
           $data['message'] = 'Event saved successfully';
        } 
        return $response->setBody(Zend_Json::encode($data));
    }

    
    public function geteventdetailsAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {   
           $id = $this->_request->getPost('id');
           $result = $this->event->getEvent($id);
           $data['start']  = date("d-m-Y H:i:s",strtotime($result['start']));
           $data['end']  = date("d-m-Y H:i:s",strtotime($result['end']));
           $data['id']  = $result['id'];
           $data['title']  = $result['title'];
           $data['description']  = $result['description'];
           $data['adress']  = $result['address'];
           $data['bgcolor']  = $result['bgcolor'];
           $data['logo']  = $result['logo'];
           $data['bgimage']  = $result['bgimage'];
           $data['type']  = $result['type'];
           $data['promoted']  = $result['promoted'];
           $data['timezoneevent']  = $result['timezoneevent'];
           $data['metaData'] = $this->event->getAllMetaDataEvent($id);
           $data['status']  = 'success';
        } 
        return $response->setBody(Zend_Json::encode($data));
    }
    /**
     *  publish advert
     */
    public function publishAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
           $id = $this->_request->getPost('id');
           $status = $this->_request->getPost('status');
           if($status==1)
              $message = 'Event published successfully';
           else
              $message = 'Event unpublished successfully';
            
           $data['status'] = $status;
           $where['id'] = $id;
           $where['clientID'] = clientID;
           $this->myclientdetails->updateMaster('tblEvent',$data,$where);

          $data['status']  = 'success';
          $data['message'] = $message;
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
    }


    /**
     *  promoted event
     */
    public function promoteAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
           
           $id = $this->_request->getPost('id');
           $promote = $this->_request->getPost('status');
           
           $data['promoted'] = $promote;
           $where['id'] = $id;
           $where['clientID'] = clientID;
           $this->myclientdetails->updateMaster('tblEvent',$data,$where);
           $data['status']  = 'success';
           $data['message'] = ($promote==1)?'Promoted successfully':'Unpromote successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function renderhtmlAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $result = $this->event->getAllPublishEvent(); 
            $content ='';

            $facebook_connect_data = Zend_Json::decode($this->session_data['facebook_connect_data']);
            $twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);

            $facebook = $facebook_connect_data['access_token'];
            $twitter = $twitter_connect_data['twitter_access_token'];
            $linkedin = $linkedin_connect_data['oauth_verifier'];
            if(count($result))
            { $active = 'active';
             foreach ($result as $value) 
             {    
                
                $content .='<li id="remove_'.$value['id'].'" class="eventListing '.$active.'">
                      <h2>
                        <i class="fa fa-calendar"></i> <div class="oneline">'.$value['title'].' </div>
                       </h2>';
                       $content .='<div class="eventDetails">';
                         if($value['latitude']!='' && $value['longitude']!='')
                        $content .='<div class="googleMap" id="map_'.$value['id'].'"></div>';

                        if($value['description']!="")
                          $content .=' <div class="scoredData">
                                    <div class="formRow">
                                          <label class="label">Description:</label>
                                          <div class="field">'.$value['description'].'</div>
                                        </div>';                 
                 
                          $content .=' <div class="formRow">
                                          <label class="label">Start date:</label>
                                          <div class="field">'.date("d-m-Y H:i:s",strtotime($value['start'])).' '.$this->myclientdetails->timezoneevent($value['timezoneevent']).'</div>
                                        </div>';
                          $content .=' <div class="formRow">
                                          <label class="label">End date:</label>
                                          <div class="field">'.date("d-m-Y H:i:s",strtotime($value['end'])).' '.$this->myclientdetails->timezoneevent($value['timezoneevent']).'</div>
                                        </div>';

                          if($value['address']!="")
                            $content .=' <div class="formRow">
                                          <label class="label">Address:</label>
                                          <div class="field">'.nl2br($value['address']).'</div>
                                        </div>';

                          $content .=' <div class="formRow">
                                          <label class="label">Attendees:</label>
                                          <div class="field">'.count($this->event->checkEventMember2($value['id'])).'</div>
                                        </div>';
                         
                          $content .=' <div class="formRow">
                                          <label class="label">Posts:</label>
                                          <div class="field">'.count($this->event->getEventdbee($value['id'])).'</div>
                                        </div>';
                         
                          $data['metaData'] = $this->event->getAllMetaDataEvent($value['id']);
                          foreach ($data['metaData'] as $valuedata) 
                          {
                              $content .=' <div class="formRow">
                              <label class="label">'.$valuedata['label'].':</label>
                              <div class="field">'.$valuedata['value'].'</div>
                              </div>';
                          }
                        $content .='</div>';
                      if($value['type']==3 && $value['status']==1)
                      {
                          $content .='<div class="dbSocialRt inviteEventSocial">
                          <div class="spdbltbox">
                          <div class="inPeople"> Invite from</div>
                          <div class="spcPeopleIcon">';
                          if($facebook==false)
                          $content .='<a href="'.BASE_URL.'/admin/social/facebook?eventid='.$value['id'].'&type=event" class="dbfbIcon"><span class="fa fa-facebook-square"></span></a>';
                          else
                          $content .='<a href="javascript:void(0);" class="dbfbIcon socialfriends" data-uniqueValue ="'.$value['id'].'"  data-for ="event" data-type="facebook" data-title="'.$value['title'].'"><span class="fa fa-facebook-square"></span></a>';

                          if($twitter==false)
                          $content  .=  '<a href="'.BASE_URL.'/admin/social/twitter?eventid='.$value['id'].'&type=event" class="dbtwitterIcon"><span class="fa fa-twitter-square"></span></a>';
                          else
                          $content  .=  '<a href="javascript:void(0);" class="dbtwitterIcon socialfriends" data-uniqueValue ="'.$value['id'].'"  data-for ="event" data-type="twitter" data-title="'.$value['title'].'" ><span class="fa fa-twitter-square"></span></a>';

                          $content  .=  '<a href="javascript:void(0);" class="dbsocialIcon socialfriends" data-uniqueValue ="'.$value['id'].'"  data-for ="event" data-type="dbee" data-title="'.$value['title'].'" ></a>';
                          $content.='</div></div></div>';
                       }

                      $content .='</div><div class="listBtnsWrp">
                                  <a class="deleteevent" href="javascript:void(0);" data-id="'.$value['id'].'">
                                    <span class="fa-stack">
                                      <i class="fa fa-square fa-stack-2x"></i>
                                      <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                                    </span>
                                   </a>
                                    <a class="editevent " href="javascript:void(0);" data-id="'.$value['id'].'"><i class="fa fa-pencil-square fa-2x"></i></a>';
                      $disable = '';
                      
                      if($value['status']==1)
                      {
                          $content .=' <a class="btn btn-danger btn-mini publishevent unpublish btnEqlSize" href="javascript:void(0);" data-status="'.$value['status'].'" data-id="'.$value['id'].'">UnPublish</a>';
                      }
                      else
                      {
                        $content .=' <a class="btn btn-green btn-mini publishevent publish btnEqlSize" href="javascript:void(0);" data-status="'.$value['status'].'" data-id="'.$value['id'].'">Publish</a>';
                        $disable = 'disabled';
                      }
                      
                      if($value['promoted']==1)
                      {
                          $content .=' <a class="btn btn-danger btn-mini promotedevent  '.$disable.'" href="javascript:void(0);" data-promoted="'.$value['promoted'].'" data-id="'.$value['id'].'">UnPromote</a>';
                      }
                      else
                      {
                         $content .=' <a class="btn btn-green btn-mini promotedevent  '.$disable.'" href="javascript:void(0);" data-promoted="'.$value['promoted'].'" data-id="'.$value['id'].'">Promote</a>';
                      }


                      if($value['status']==1)
                          $content .=' <a class="'.$disable.' crtPostevent addonpost eventdbpost btn btn-yellow btn-mini" href="javascript:void(0);" data-type="postinevent" id="eventdbpost" data-id="'.$value['id'].'">Create Post</a>';
                        else
                          $content .=' <a class="'.$disable.' crtPostevent addonpost eventdbpost btn btn-yellow btn-mini" href="javascript:void(0);" data-type="postinevent" id="eventdbpost" data-id="'.$value['id'].'">Create Post</a>';
                    
                  $content .='</div></li>';
                  if($value['latitude']!='' && $value['longitude']!='')
                  {     
                      $geoData['latitude'] = $value['latitude'];
                      $geoData['longitude'] = $value['longitude'];
                      $geoDataFinal['mapjson']=json_encode($geoData);
                      $geoDataFinal['mapid']=$value['id'];
                      $data['json'][]=$geoDataFinal;
                  }
              $active = '';
             }
           }else{
            $content = '<li class="eventListing noRecord"><h2>No events found.</h2></li>';
           }
            if(count($result)>10)
              $pageContent = ' <div id="vewpagebottom" style="text-align:center;padding: 20px 2%;font-weight:bold;font-size:16px;clear:both;"><a id="viewmoreevent" offset="10">View More </a></div>';
            else
              $pageContent ='';
            
            $data['pagingContent'] = $pageContent;
            $data['status']  = 'success';
            $data['content'] = $content;
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function renderhtmlunpublishAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $result = $this->event->getAllUnPublishEvent(); 
            $content ='';
            if(count($result))
            {
              $active = 'active';
             foreach ($result as $value) 
             {    
                $content .='<li id="remove_'.$value['id'].'" class="eventListing '.$active.'">
                      <h2>
                        <i class="fa fa-calendar"></i> <div class="oneline">'.$value['title'].' </div>
                       </h2>';
                       $content .='<div class="eventDetails">';
                         if($value['latitude']!='' && $value['longitude']!='')
                        $content .='<div class="googleMap" id="map_'.$value['id'].'"></div>';

                        if($value['description']!="")
                          $content .=' <div class="scoredData">
                                    <div class="formRow">
                                          <label class="label">Description:</label>
                                          <div class="field">'.$value['description'].'</div>
                                        </div>';                 
                 
                          $content .=' <div class="formRow">
                                          <label class="label">Start date:</label>
                                          <div class="field">'.date("d-m-Y H:i:s",strtotime($value['start'])).' '.$this->myclientdetails->timezoneevent($value['timezoneevent']).'</div>
                                        </div>';
                          $content .=' <div class="formRow">
                                          <label class="label">End date:</label>
                                          <div class="field">'.date("d-m-Y H:i:s",strtotime($value['end'])).' '.$this->myclientdetails->timezoneevent($value['timezoneevent']).'</div>
                                        </div>';

                          if($value['address']!="")
                            $content .=' <div class="formRow">
                                          <label class="label">Address:</label>
                                          <div class="field">'.$value['address'].'</div>
                                        </div>';
                         
                         $content .=' <div class="formRow">
                                          <label class="label">Attendees:</label>
                                          <div class="field">'.count($this->event->checkEventMember2($value['id'])).'</div>
                                        </div>';
                         
                          $content .=' <div class="formRow">
                                          <label class="label">Posts:</label>
                                          <div class="field">'.count($this->event->getEventdbee($value['id'])).'</div>
                                        </div>';
                                        
                          $data['metaData'] = $this->event->getAllMetaDataEvent($value['id']);
                          foreach ($data['metaData'] as $valuedata) 
                          {
                              $content .=' <div class="formRow">
                              <label class="label">'.$valuedata['label'].':</label>
                              <div class="field">'.$valuedata['value'].'</div>
                              </div>';
                          }
                        $content .='</div>';
                        $content .='</div><div class="listBtnsWrp">
                                  <a class="deleteevent" href="javascript:void(0);" data-id="'.$value['id'].'">
                                    <span class="fa-stack">
                                      <i class="fa fa-square fa-stack-2x"></i>
                                      <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                                    </span>
                                   </a>
                                    <a class="editevent " href="javascript:void(0);" data-id="'.$value['id'].'"><i class="fa fa-pencil-square fa-2x"></i></a>';
                    
                  $content .='</div></li>';
                  if($value['latitude']!='' && $value['longitude']!='')
                  {     
                      $geoData['latitude'] = $value['latitude'];
                      $geoData['longitude'] = $value['longitude'];
                      $geoDataFinal['mapjson']=json_encode($geoData);
                      $geoDataFinal['mapid']=$value['id'];
                      $data['json'][]=$geoDataFinal;
                  }
             $active = '';
             }
            }else{
              $content = '<li class="eventListing noRecord"> <h2>No events found.</h2></li>';
            }
            if(count($result)>10)
              $pageContent = ' <div id="vewpagebottom" style="text-align:center;padding: 20px 2%;font-weight:bold;font-size:16px;clear:both;"><a id="viewmoreevent" offset="10">View More </a></div>';
            else
              $pageContent ='';
            $data['status']  = 'success';
            $data['content'] = $content;
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    /**
     *  publish advert
     */
    public function deleteAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $id = $this->_request->getPost('id');
            $where['id'] = $id;
            $this->myclientdetails->deleteMaster('tblEvent',$where);
            $data['status']  = 'success';
            $data['message'] = 'deleted successfully';
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'has not been deleted';
        }
        return $response->setBody(Zend_Json::encode($data));
    }


     public function showdbpostformAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $EventID = $this->_request->getPost('eventid');
        $deshObj= new Admin_Model_Deshboard();
        $content.=$deshObj->postdbform($EventID,$this->_userid);
        $data['content'] = $content;
        return $response->setBody(Zend_Json::encode($data));
    }
}