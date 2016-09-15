<?php
class Admin_GetjsonresultController extends IsadminController
{
	private $options;
    public function init()
    {
	   parent::init();
    }
    public function indexAction()
    {
        $dataArray = array();    
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $query = $filter->filter($this->_request->getParam('q'));
            $type = (int)$this->_request->getParam('type');
            $event = new Admin_Model_Event();
            switch ($type) {
              case 4:
                $query = $this->myclientdetails->customEncoding($query,$search="true");
                $result = $this->user_model->searchUser($query,10);
                foreach ($result as $value) 
                {
                    $dataArray['id'] = $value['UserID'];
                    $dataArray['email'] = $this->myclientdetails->customDecoding($value['Email']);
                    $dataArray['url'] = IMGPATH.'/users/small/'.$value['ProfilePic'].'';
                    $dataArray['name'] = $this->myclientdetails->customDecoding($value['full_name']);
                    $data[] = $dataArray;
                }
                break;
              case 3:
                $result = $this->user_group->searchUser_group($query,10);
                foreach ($result as $value) 
                {
                    $dataArray['id'] = $value['ugid'];
                    $dataArray['name'] = $value['ugname'];
                    $data[] = $dataArray;
                }
                break;
              case 5:
                $result = $this->deshboard->getDbee($query,10);
                foreach ($result as $value) 
                {
                    $title = $value['Text'];
                    if ($value['Type'] == '5')
                        $title = $value['PollText'];
                    else if ($value['Type'] == '6')
                        $title = $value['surveyTitle'];
                    $dataArray['id'] = $value['id'];
                    $dataArray['name'] = $title;
                    $data[] = $dataArray;
                }
                break;
             case 6:
                $result = $this->group->searchGroup($query,10,1);
                foreach ($result as $value) {
                    $dataArray['id'] = $value['ID'];
                    $dataArray['name'] = $value['GroupName'];
                    $data[] = $dataArray;
                }
                break;
             case 7:
                $result = $event->getAllLikeEvent($query);
                foreach ($result as $value) {
                    $dataArray['id'] = $value['id'];
                    $dataArray['name'] = $value['title'];
                    $data[] = $dataArray;
                }
                break;
              default:
                $result = $this->group->searchGroup($query,10,0);
                foreach ($result as $value) {
                    $dataArray['id'] = $value['ID'];
                    $dataArray['name'] = $value['GroupName'];
                    $data[] = $dataArray;
                }
            } 
        }
        return $response->setBody(Zend_Json::encode($data));
    }
}