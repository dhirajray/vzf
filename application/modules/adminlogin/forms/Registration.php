<?php
/**
 * Login_Form_Login
 * 
 * @author Enrico Zimuel (enrico@zimuel.it)
 */
class Adminlogin_Form_Registration extends Zend_Form
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
        require_once 'includes/globalfileadmin.php';
       /* $this->addElement('hash', 'token', array(
             'timeout' => $this->_timeout
        ));*/
        
        $this->addElement('text', 'username', array(
            'placeholder'      => 'Username',
            'required'   => true,
            'validators' => array('Alnum')
        ));	 

        $this->addElement('text', 'email', array(  
            'placeholder' => 'Your email address:',  
            'required' => true,  
            'filters' => array('StringTrim'),  
            'validators' => array(  
                'EmailAddress',  
            )  
        ));  

        $passwordConfirmation = new App_Validate_PasswordConfirmation();
       
        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                $passwordConfirmation,
                array('Alnum'),
                array('StringLength', false, array(5, 100)),
            ),
            'class'      => 'input-text',
            'required'   => true,
            'placeholder'      => 'Password',
        ));
         
        $password_confirm = $this->addElement('password', 'password_confirm', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                $passwordConfirmation,
                array('Alnum'),
                array('StringLength', false, array(5, 100)),
            ),
            'class'      => 'input-text',
            'required'   => true,
            'placeholder'      => 'Confirm Password',
        ));

        $this->addElement('select','quetion', array( 
        'label' => 'Security Question', 'value' => 'male', 
        'multiOptions' => $securityquetion, ) );

        $this->addElement('text', 'answer', array(
            'placeholder'      => 'Security Answer',
            'required'   => true,
            'validators' => array(
                array('Alnum'),
                
            ),

        ));  

      $this->addElement('select','Gender', array( 
        'placeholder' => 'Gender', 'value' => 'male', 
        'multiOptions' => array(  'Male','Female','Transgender','Gender Fluid','No Response',), ) );
         

        $this->addElement('text', 'dob', array(
            'placeholder'      => 'DOB',
            'required'   => true,
            'placeholder' => 'DD-MM-YYYY',
            'validators' => array (
               array('date', false, array('dd-MM-yyyy'))
            ),
        )); 

       $this->addElement('file', 'attachment', array(
            'placeholder' => 'Profile Picture',
            'validators' => array(
                array('Count', false, 1),
                array('Size', false, 2097152),
                array('Extension', false, 'gif,jpg,png'),
                array('ImageSize', false, array('minwidth' => 100,
                                                'minheight' => 100,
                                                'maxwidth' => 1000,
                                                'maxheight' => 1000)))

        ));

        $this->addElement('submit','submit', array (
            'label'      => 'Send'
        ));

    }
}

class App_Validate_PasswordConfirmation extends Zend_Validate_Abstract {
 
    const NOT_MATCH = 'notMatch';
    
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );
    
    public function isValid($value, $context = null) {
        $value = (string) $value;
        $this->_setValue($value);
        
        if (is_array($context)) {
            if (
                isset($context['password_confirm']) && 
                ($value == $context['password_confirm'])) {
                    return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }
        
        $this->_error(self::NOT_MATCH);
        return false;
    }
}

