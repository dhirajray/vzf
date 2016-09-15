<?php
class FavouriteController extends IsController
{
    public function init()
    {
        parent::init();
        $this->favourites = new Application_Model_Favourites();
    }
    /**
     *  make own expert
     */
    public function indexAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeID = (int)$this->_request->getPost('dbeeID'); // type cast into interger
             // for token validation and cross side domain validation
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
            if ($validate==false) 
            {
                $data['status']  = 'error';
                $data['message'] = 'Something went wrong please try again';
            }else if($dbeeID!=0)
            {
                // get follower info
                $getDbee = $this->Myhome_Model->getDbeeDetails($dbeeID);
                // check user is VIP OR not
                $favData = $this->favourites->checkfavdbee($dbeeID,$this->_userid);
                $data = array('clientID'=>clientID,'DbeeID'=>$dbeeID,'Owner'=>$getDbee['User'],'User'=>$this->_userid,'DateAdded' => date('Y-m-d H:i:s'));
                if(empty($favData)){ // added fav dbee
                    $this->favourites->addfav($data);
                    $this->notification->commomInsert('5','5',$dbeeID,$this->_userid,$getDbee['User']); // Insert for favorite activity
                    $data['status'] = 'success';
                    $data['message'] = 'Post added to your favourites.';
                }else{
                    $data['status'] = 'success';
                    $data['message'] = 'Already Post added to your favourites.';
                }
            }
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function listAction()
    { 
        $request = $this->getRequest();
       // $start = $this->getRequest()->getPost('start')?$this->getRequest()->getPost('start'):0;
       // $end = $this->getRequest()->getPost('end')?$this->getRequest()->getPost('end'):PAGE_NUM;  
        $start = $request->getpost('start', 0);
        $end   = $request->getpost('end', PAGE_NUM);    
        $this->view->favouritesdbees = $this->favourites->index($start,$end,$this->_userid);                   
        $this->view->start = $start;
        $this->view->startnew = $start+PAGE_NUM;
        $this->view->end = $start+PAGE_NUM; 
        $this->view->dbeenotavailmsg = $this->getRequest()->getPost('dbeenotavailmsg'); 
        $this->view->seemore = $this->getRequest()->getPost('seemore');
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
    
}
