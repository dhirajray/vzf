<?php 
class Application_Form_Registration extends Zend_Form{
	
public $elementDecorators = array(
array('ViewHelper'),
 array('Label', array( )
	),
array('Errors'),
);
 
    public function init()
    {
    	 
	    $fullname = $this->createElement('text','fullname');
        $fullname->setAttrib('id','name')
                ->setOptions(array('style' => 'width:240px;'))
                 ->setDecorators($this->elementDecorators)
				 ->setAttrib('class', 'signup_input')
                 ->setRequired(true);
				 
        $username = $this->createElement('text','username');
        $username->setAttrib('id','username')
                 ->setOptions(array('size' => '45'))
                 ->setDecorators($this->elementDecorators) 
				 ->setAttrib('class', 'signup_input')
				 ->setOptions(array('style' => 'width:240px;'))
                 ->setRequired(true);
		
     	$email = $this->createElement('text','email');
        $email->setAttrib('id','email')
                 ->setOptions(array('size' => '35'))
                 ->setOptions(array('onblur' => 'checkEmail(this.value);'))
                 ->setOptions(array('style' => 'width:240px;'))
                 ->setDecorators($this->elementDecorators) 
				 ->setAttrib('class', 'signup_input')
                 ->setRequired(true);        
              
        $password = $this->createElement('password','password');
        $password->setAttrib('id','password')
                 ->setOptions(array('size' => '35'))
                 ->setOptions(array('style' => 'width:240px;', 'onkeyup'=>'passwordStrength(this.value)'))
                 ->setDecorators($this->elementDecorators) 
				 ->setAttrib('class', 'signup_input')
                 ->setRequired(true);
				 
		$gender = $this->createElement('text','gender');
        $gender->setAttrib('id','gender')
                 ->setOptions(array('size' => '35'))
                 ->setOptions(array('style' => 'width:240px;'))
                 ->setDecorators($this->elementDecorators) 
				 ->setAttrib('class', 'signup_input')
                 ->setRequired(true);
		
		$birthday = $this->createElement('text','birthday');
        $birthday->setAttrib('id','birthday')
                 ->setOptions(array('size' => '35'))
                 ->setOptions(array('style' => 'width:240px;'))
                 ->setDecorators($this->elementDecorators) 
				 ->setAttrib('class', 'signup_input')
                 ->setRequired(true);
        /*         
        $captcha = $this->addElement('captcha', 'captcha', array(
            'label'      => 'Please enter the 5 letters displayed below:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
		*/
		$publickey  = '6LeAAd4SAAAAAJ3BeWLfc2mmYZW75ImFYjAZK029'; 
		$privatekey = '6LeAAd4SAAAAANXcScRJ7eHz4j_cuLWZZuHzEMs8';
        $recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);

        $captcha = new Zend_Form_Element_Captcha('captcha',
            array(
                'captcha'       => 'ReCaptcha',
                'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
                'ignore' => true
                )
        );
		
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id','submit')
                 ->setOptions(array('size' => '35'))
                 ->setDecorators($this->elementDecorators) 
				 ->setAttrib('class', 'sign_up_submit')
                 ->setRequired(true);
		 
                
                //$userid,
        $this->addElements(array($fullname,$username,$email,$password,$gender,$birthday,$captcha,$submit));
    }
}
?>
