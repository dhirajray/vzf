<?php

class Admin_Form_Albums extends Zend_Form
{
	public function __construct() 
    {
    	
		$title = new Zend_Form_Element_Text('title', array('disableLoadDefaultDecorators' => true));
        $title->addDecorator('ViewHelper')
	            ->setRequired(true);
		$this->addElement($title);
		
		$name = new Zend_Form_Element_Text('artist', array('disableLoadDefaultDecorators' => true));
        $name->addDecorator('ViewHelper')
	            ->setRequired(true);
		$this->addElement($name);
		
		$lastname = new Zend_Form_Element_Text('genre', array('disableLoadDefaultDecorators' => true));
        $lastname->addDecorator('ViewHelper')
	            ->setRequired(true);
		$this->addElement($lastname);
		
		$hash = new Zend_Form_Element_Hash('csrf');
		$hash->setErrorMessages(array('Cross-site request forgery protection'));
		$this->addElement($hash);
		
		$this->clearDecorators();
		$this->addDecorator('FormElements')->addDecorator('Form');
          
    }



}
