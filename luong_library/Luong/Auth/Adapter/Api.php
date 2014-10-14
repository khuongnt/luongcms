<?php

class Luong_Auth_Adapter_Api implements Zend_Auth_Adapter_Interface {

    protected $_username;
    protected $_password;
    protected $_role;

    public function __construct($username, $password, $role) {
        $this->_username = $username;
        $this->_password = $password;
        $this->_role = $role;
    }

    /**
     * setUsername() - set the username value
     *
     * @param  string $username
     * @return Luong_Auth_Adapter_Api Provides a fluent interface
     */
    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }

    /**
     * setPassword() - set the password value
     *
     * @param  string $password
     * @return Luong_Auth_Adapter_Api Provides a fluent interface
     */
    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    /**
     * setRole() - set the role value
     *
     * @param  string $role
     * @return Luong_Auth_Adapter_Api Provides a fluent interface
     */
    public function setRole($role) {
        $this->_role = $role;
        return $this;
    }

    public function authenticate() {
        if (empty($this->_username) || empty($this->_password) || empty($this->_role)) {
            throw new Zend_Auth_Adapter_Exception();
        }

        $token = Luong_Helper_Utils::getToken();
        $login = Luong_Helper_Utils::userLogin($token, $this->_username, $this->_password);
        if ($login->status == 0) {
            if (!empty($login->data)) {
                $hasPermission = $this->hasPermission($login->data->roles, $this->_role);
                if ($hasPermission) {
                    return new Luong_Auth_Result(
                            Luong_Auth_Result::SUCCESS, $this->_username, array('Login success'), $login
                    );
                } else {
                    return new Luong_Auth_Result(
                            Luong_Auth_Result::FAILURE_PERMISSION_DENIED, $this->_username, array('Permission denied'), $login
                    );
                }
            } else {
                return new Luong_Auth_Result(
                        Luong_Auth_Result::FAILURE, $this->_username, array('Invalid user data'), $login
                );
            }
        } else {
            switch ($login->status) {
                case 1:
                    return new Luong_Auth_Result(
                            Luong_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_username, array($login->message), $login
                    );
                case 3:
                    return new Luong_Auth_Result(
                            Luong_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_username, array($login->message), $login
                    );
                default:
                    return new Luong_Auth_Result(
                            Luong_Auth_Result::FAILURE, $this->_username, array($login->message), $login
                    );
            }
        }
    }

    public static function hasPermission($user_roles, $require_roles) {
        if (!is_array($require_roles)) {
            $require_roles = array($require_roles);
        }
        if (is_array($user_roles)) {
            foreach ($user_roles as $_user_role) {
                if (!empty($_user_role->name) && in_array($_user_role->name, $require_roles)) {
                    return true;
                }
            }
        }
        return false;
    }

}
