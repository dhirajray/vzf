<?php
require_once 'Zend/Filter/Interface.php';
require_once 'minify/HTML.php';
require_once 'minify/CSS.php';
require_once 'JSMin.php';
 
class My_View_Filter_Minify implements Zend_Filter_Interface
{
    public function filter($value) 
    {
    	return Minify_HTML::minify($value, array(
    	    'cssMinifier' => array('Minify_CSS', 'minify'),
            'jsMinifier' => array('JSMin', 'minify')
    	));
    }
}