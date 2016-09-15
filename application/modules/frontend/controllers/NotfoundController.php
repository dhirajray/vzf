<?php
class NotfoundController extends IsloginController
{  

	public function init()
	{
		parent::init();
		
		if(INVALID_DOMAIN !='WRONG_URL' ){
            $this->_helper->redirector->gotosimple('index','index', true);
        }
	}

	public function indexAction()
	{
		echo "<div style='padding:5opx; border:1px solid #ccc;background:#ffffff;color:red;margin-top:200px;text-align:center'>create index.phtml file and set domain url or client not found message</div>";exit;
	}

	
}