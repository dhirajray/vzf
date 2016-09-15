<?php

class UrlerrorController extends IsController

{
    public function init()
    {

    }
    
    public function indexAction()
    {

    }

    public function unvalidateurlAction(){
       $request = $this->getRequest()->getParams();
       $this->view->checkcontrolleraction = 'errorinurl';
    }


}



