<?php

class Widgets_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initConfig() {

        try {
        	
           $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
           
        } catch (Exception $e) {

            echo $e->getMessage();
            die;
        }
        Zend_Registry::set('config', $config);
    }
    
	
}