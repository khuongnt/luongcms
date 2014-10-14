<?php

class Model_Sm_Good_Price extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_good_price',
            'dependentTables' => array(),
            'referenceMap' => array(),
        ));
    }

}
