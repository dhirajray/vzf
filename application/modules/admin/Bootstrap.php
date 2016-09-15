<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initSession()
    {
        $this->bootstrap("frontController");
        $this->frontController->registerPlugin(new Custom_Controller_Plugin_Session()); 
    }
    
    protected function _initAcl()
    {
    	$helper = new Application_Model_Library_Acl_Backend();
		$helper->setRoles();
		$helper->setResources();
		$helper->setPrivilages();
		$helper->setAcl();
		$this->bootstrap("frontController");
		$this->frontController->registerPlugin(new Custom_Controller_Plugin_Acl());
		
    }

    protected function _initDB() {

        try {
            $config = Zend_Registry::get('config');
            $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
        } catch (Exception $e) {

            echo $e->getMessage();
            die;
        }
    }
	
}
