<?php

class Luong_Controller_Default_Action extends Zend_Controller_Action {

    protected $_user = NULL;
    protected $_errors;
    protected $_success;
    protected $_auth;

    public function init() {
        $this->_auth = Zend_Auth::getInstance();
        $this->_auth->setStorage(new Zend_Auth_Storage_Session('Luong_default'));
        if ($this->_auth->hasIdentity()) {
            $this->_user = $this->_auth->getIdentity();
        }
        $detect = new Luong_Controller_Mobile_Detect();
        if ($detect->isMobile() == true) {
            $this->_helper->layout->setLayoutPath(LAYOUT_PATH . "/mobile/");
            $this->_helper->layout->setLayout('index');
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/default/views_mobile/scripts/');
        }
    }

    public function preDispatch() {
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $optionsModel = $dbname->luong_options;
        $Options = $optionsModel->findOne(array('_id' => new MongoId('542a49dad445bfac08000042')));

        $this->view->configSite = $Options;
        $this->view->module = $this->getRequest()->getModuleName();
        $this->view->controller = $this->getRequest()->getControllerName();
        $this->view->action = $this->getRequest()->getActionName();
        $this->view->user = $this->_user;
    }

}
