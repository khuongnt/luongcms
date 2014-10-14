<?php

class Luong_Controller_Default_Action extends Zend_Controller_Action {

    protected $_user = NULL;
    protected $_errors;
    protected $_success;
    protected $_auth;

    public function getReferenceCode() {
        $refcode = '';
        if (preg_match("@^([a-z0-9]+)\.edoctor\.vn$@i", $_SERVER['HTTP_HOST'], $m) && $m[1] != 'www') {
            $refcode = $m[1];
        } elseif (!empty($_COOKIE['referer_code'])) {
            $refcode = $_COOKIE['referer_code'];
        } else {
            $f = new Zend_Filter_StripTags();
            $refcode = $f->filter($this->_request->getParam('refcode', ''));
        }
        return $refcode;
    }

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
        $is_subdomain = FALSE;
        if (!empty($_SERVER['HTTP_HOST']) && preg_match("@^([a-z0-9]+)\.edoctor\.vn$@i", $_SERVER['HTTP_HOST'], $m)) {
            $is_subdomain = TRUE;
            $new_refcode = $m[1];
        } else {
            $new_refcode = $this->_request->getParam('refcode');
        }
        if (!empty($new_refcode)) {
            $valid_refcode = false;
            $affiliateUserModel = new AffiliateUserModel();
            $userModel = new AuthUserModel();
            $affiliateUser = $affiliateUserModel->fetchRow(array('ref_code = ?' => $new_refcode));
            if (!empty($affiliateUser)) {
                $user = $userModel->fetchRow(array('id = ?' => $affiliateUser->user_id));
                if (!empty($user->id) && $user->status == 1) {
                    $valid_refcode = true;
                }
            }
            if ($valid_refcode) {
                setcookie('referer_code', $affiliateUser->ref_code, time() + 30 * 24 * 60 * 60, '/', 'edoctor.vn');
            } elseif ($is_subdomain) {
                $this->_redirect('http://edoctor.vn' . $_SERVER['REQUEST_URI']);
            }
        }

        //Luong_Helper_Tracking::start($this->getReferenceCode());

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
