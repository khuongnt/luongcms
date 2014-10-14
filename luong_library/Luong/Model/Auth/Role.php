<?php

class Model_Auth_Role extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'auth_role',
            'referenceMap' => array(),
            'dependentTables' => array(
                'Model_Auth_UserRole',
            ),
        ));
    }

}
