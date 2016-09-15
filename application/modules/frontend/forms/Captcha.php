
<?php 
class Application_Form_Captcha extends Zend_Form
{
	   public function init()
      {
    
		
		$publickey = '6LcXs-USAAAAAGKTFbaFLzobuQflYVOfs1E9CfuP';
		$privatekey = '6LcXs-USAAAAAM-omTRGKHTYF4LDwP5-ERejUufh';
        $recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
        $captcha = new Zend_Form_Element_Captcha('captcha',
            array(
                'captcha'       => 'ReCaptcha',
                'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
                'ignore' => true
                )
        );
        $this->addElement($captcha);
        //$this->addElement('text', 'data', array('label' => 'Some data'));
        //$this->addElement('submit', 'submit', array('label' => 'Submit'));
   }
	
}
