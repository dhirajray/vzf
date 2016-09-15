<?php

class Application_Form_Dbupload extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add an email element
        $this->addElement('file', 'fileupload', array(
            'onchange'=> 'ajaxUpload("Dbupload","/myhome/dbupload","upload_area","GeneratingPreview","errrpr")',           
        ));      

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Upload Now',
        ));
 
    }
}
