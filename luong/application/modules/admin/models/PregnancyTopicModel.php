<?php

class Admin_Models_PregnancyTopicModel extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'schema' => 'app_me_be',
            'name' => 'topic',
        ));
    }

    public function getAllTopic() {
    	$sql = "SELECT * FROM `" . $this->_schema . "`.`" . $this->_name . "`";        
    	return $this->getAdapter()->fetchAll($sql);
    }

}
