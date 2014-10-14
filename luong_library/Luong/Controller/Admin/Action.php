<?php

class Luong_Controller_Admin_Action extends Zend_Controller_Action {

    const REQUIRE_ROLE = 'ROLE_ADMIN';

    protected $_user;
    protected $_auth;

    public function init() {
        $this->_auth = Zend_Auth::getInstance();
        $this->_auth->setStorage(new Zend_Auth_Storage_Session('Luong_admin'));

        $this->_helper->layout->setLayoutPath(LAYOUT_PATH . "/admin/");
        $this->_helper->layout->setLayout('index');
    }

    public function preDispatch() {
        if ($this->_auth->hasIdentity()) {
            $this->_user = $this->_auth->getIdentity();
           
            
            if (!$this->_user->id) {
                $this->_auth->clearIdentity();
                $this->_redirectLogin();
            }
            $this->view->user = $this->_user;
        } else {
            $this->_redirectLogin();
        }
    }

    private function _redirectLogin() {
        if ($this->getRequest()->getControllerName() != 'user' || $this->getRequest()->getActionName() != 'login') {
            $this->_redirect('/admin/login.html');
        }
    }

}
