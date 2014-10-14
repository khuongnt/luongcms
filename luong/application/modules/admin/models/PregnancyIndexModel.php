<?php

class Admin_Models_PregnancyIndexModel extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'schema' => 'app_me_be',
            'name' => 'index',
        ));
    }

    public function getAllIndex() {
    	$sql = "SELECT * FROM `" . $this->_schema . "`.`" . $this->_name . "`";        
    	return $this->getAdapter()->fetchAll($sql);
    }

}
