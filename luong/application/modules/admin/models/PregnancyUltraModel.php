<?php

class Admin_Models_PregnancyUltraModel extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'schema' => 'app_me_be',
            'name' => 'ultrasonic',
        ));
    }

    public function getAllUltra() {
        $sql = $this->getAdapter()->select()
                ->from(array('n' => $this->_name), array('*'), $this->_schema)
//                ->join(array('t' => 'topic'), 't.id=n.topic_id', array('name'), $this->_schema)
//                ->where('s.code=?', $code)
//                ->where('s.status=?', 1)
        ;
        return $this->getAdapter()->fetchAll($sql);
    }

}
