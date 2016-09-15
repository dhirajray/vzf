<?php
/**
 * Login_Form_Login
 * 
 * @author Enrico Zimuel (enrico@zimuel.it)
 */
class Adminlogin_Form_Login extends Zend_Form
{   
	private $_timeout;
	
	public function __construct($options=null) {
		if (is_array($options)) {
			if (!empty($options['custom'])) {
				if (!empty($options['custom']['timeout'])) {
					$this->_timeout= $options['custom']['timeout'];
				}
				unset($options['custom']);
			}
		}	
		parent::__construct($options);
	}
	
    public function init ()
    {
    
         $time = time();
         $namespacexx = new Zend_Session_Namespace('zend_token_admin'); // default namespace 
         $namespacexx->times = $time;
         $salt = 'b79jsMaEzXMvCO2iWtzU2gT7rBoRmQzlvj5yNVgP4aGOrZ524pT5KoTDJ7vNiIN';
         $token = sha1($salt . $time);
        
        $this->addElement('text', 'username', array(
            'label'      => '',
            'required'   => true,
            'placeholder' => 'Username',
            'validators' => array('Alnum')
        ));
        $this->addElement('password', 'password',  array(
            'label'      => '',
            'required'   => true,
            'placeholder' => 'Password',
            'validators' => array('Alnum'),
        ));

        $this->addElement('hidden', 'token', array('value' => $token)) ;


        $this->addElement('submit','submit', array (
            'label'      => 'Send'
        ));

    }
}