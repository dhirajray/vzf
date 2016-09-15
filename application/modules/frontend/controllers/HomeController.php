<?php

class HomeController extends IsController

{

    public function init()

    {

    /*@@ Initialize action controller here */

		$namespace = new Zend_Session_Namespace(); 

		$request = $this->getRequest()->getParams();

		if($namespace->userid=='') 

		{ 

		    //$sessionerrormsg ='your session not created please try again!';

			//$this->_helper->redirector->gotosimple('index','Index',true,array('serrormsg'=>$sessionerrormsg));

			$this->_helper->redirector->gotosimple('index','Index',true);	

		}

    }   

	

    public function indexAction()

    {

		$request = $this->getRequest()->getParams();

		echo'<pre>';print_r($request);die('in home page index');

	}

}



?>

