<?php

abstract class Login_Model_DbTable_MultiDBAdapters extends Zend_Db_Table {
    function My_Model_DbTable_MultiDBAdapters($config = null) {
        if (isset($this->_use_adapter)) {
            $dbAdapters = Zend_Registry::get('dbAdapters');
            $config = ($dbAdapters[$this->_use_adapter]);
        }
        return parent::__construct($config);
    }
}
?>