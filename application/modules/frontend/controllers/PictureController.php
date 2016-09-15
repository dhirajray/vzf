<?php
class PictureController extends IsController
{  
	public function init()
	{
         parent::init();
    }
	public function groupimageuploadAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getPost();
        $image_info = getimagesize($_FILES["file"]["tmp_name"]);
        $sizes = array(100 => 100);
        if ($image_info[0] < 5 && $image_info[1] < 5) 
        {
            echo "Please use valid image to upload";
            exit;
        }
        else if (!empty($_FILES)) 
        {
            $ext     = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $picture = strtolower(time() . '.' . $ext);
            $file = ABSIMGPATH."/users/".$picture; 
            $storeFolder    = ABSIMGPATH."/grouppics/";  
            if (copy($_FILES['file']['tmp_name'], $storeFolder.$picture)) 
            {                
                $commonobj = new Application_Model_Commonfunctionality();                
                foreach ($sizes as $w => $h) {
                 $files[] = $commonobj->resizeGroup($w, $h,$picture,$_FILES["file"]["tmp_name"]);
                }
                echo $picture;
            }

            exit;
        }
    }
    public function groupimageunlinkAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
            if ($validate == false)
            {
                $data['status'] = 'error';
                $data['content'] = 'Something went wrong please try again';
            }
            @unlink('./grouppics/' . trim($this->_request->getPost('serverFileName')));
            $data['status']  = 'success';
        }else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }


}