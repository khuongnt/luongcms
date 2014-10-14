<?php

class Model_Auth_User extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'auth_user',
            'referenceMap' => array(),
            'dependentTables' => array(
                'Model_Auth_UserRole',
                'Model_Sm_Staff',
            ),
            self::ROW_CLASS => 'Model_Auth_User_Row',
        ));
    }

    public static function newSalt($length = 16) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $salt;
    }

    public static function encryptPass($password, $salt = NULL) {
        if ($salt == NULL) {
            $salt = self::newSalt();
        }
        return base64_encode(hash_hmac('sha1', $password, $salt, TRUE));
    }

}
