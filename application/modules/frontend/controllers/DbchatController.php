<?php
class DbchatController extends IsController
{
		public function init()
		{
			parent::init();
            $cat  = new Application_Model_Category();
            $this->view->cat = $cat->getallcategory();
			$storage 	= new Zend_Auth_Storage_Session();
			$auth        =  Zend_Auth::getInstance();
			if($auth->hasIdentity())
			{
				$data	  	= $storage->read();
				$this->_userid = $data['UserID'];
			}
			else
				$this->_helper->redirector->gotosimple('index','index',true);

		}
	    
	    public function exchangefilesAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{
				
				$ds = DIRECTORY_SEPARATOR;
		
				$storeFolder 	= './images/dbsecurechat';   //2.
	
				if (!empty($_FILES))
				{
					$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
					$picture	=	strtolower(time().'.'.$ext);
					
					if(copy($_FILES['file']['tmp_name'], './images/dbsecurechat/' .$picture))
					{
						echo $picture.'~'.$_FILES['file']['name'];
					}
					exit;
				}
				else
				{
					echo "error";
				}
			}

			exit;
		}
		public function chatfileunlinkAction()
		{
			$request =	$this->getRequest()->getParams();	
		
			echo "=>".unlink('./images/dbsecurechat/'.trim($request['serverFileName_']));
			exit;
		}
	
}
