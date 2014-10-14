<?php

class Model_Auth_UserRole extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'auth_user_role',
            'referenceMap' => array(
                'User' => array(
                    'columns' => array('user_id'),
                    'refTableClass' => 'Model_Auth_User',
                    'refColumns' => array('id'),
                ),
                'Role' => array(
                    'columns' => array('role_id'),
                    'refTableClass' => 'Model_Auth_Role',
                    'refColumns' => array('id'),
                ),
            ),
            'dependentTables' => array(),
        ));
    }

}
