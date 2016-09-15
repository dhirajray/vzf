<?php
class Admin_Form_Signin extends Zend_Form
{

    public function init()
    {
		$this->setName("signin");
        $this->setMethod('post');
        $this->setAction('');

        $this->clearDecorators()
        	->addDecorator('FormElements')
         	->addDecorator('Form')
        	->setElementDecorators(array(
	            array('ViewHelper'),
	            array('Label', 			array('separator' => ' ' )),
	            array('HtmlTag', 		array('tag' => 'p')
			),
        ));

        $this->addElement('text', 'username', array(
            'label'      	=> 'Usuario',
            'required'   	=> true,
            'filters'    	=> array('StringTrim', 'StripTags', 'StringToLower'),
			'validators' 	=> array( array('StringLength', false, array(2, 50))),
        ));

        $this->addElement('password', 'password', array(
            'label'      	=> 'Contraseña',
            'required'   	=> true,
        	'filters'    => array('StringTrim', 'StripTags'),
			'validators' => array( array('StringLength', false, array(3, 20))),
        ));

       	$this->addElement('submit', 'submit', array(
            'ignore'   => true,
			'label'		=> 'Accesar',
       		'class'		=> 'accesar',
			'decorators' 	=> array(
				array('ViewHelper'))
			
        ));

        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
			'decorators' 	=> array( array('ViewHelper') )
        ));
	
    }
	
}