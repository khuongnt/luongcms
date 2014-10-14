<?php

class Admin_UserController extends Luong_Controller_Admin_Action {

    protected $_errors;
    protected $_contextPath;

    public function init() {
        parent::init();
        $this->_contextPath = '/admin/user';
        $this->_text = 'User';
        $this->view->contextPath = $this->_contextPath;
    }

    public function loginAction() {
        $this->_helper->layout->setLayout('login');
        try {
            if ($this->_request->isPost()) {
                $f = new Zend_Filter_StripTags();
                $username = $f->filter($this->_request->getParam('username'));
                $password = $f->filter($this->_request->getParam('password'));
                $remember = $f->filter($this->_request->getParam('remember'));
                $this->view->username = $username;
                $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
                $dbname = $mongo->luong;
                $userModel = $dbname->luong_users;
                $password = md5(md5($password));
                $login = $userModel->findOne(array('username' => $username, 'password' => $password ));
                if (!empty($login)) {
                    $a = new Stdclass();
                    $a->id = (string)$login['_id'];
                    $a->username = $login['username'];
                    $a->roles = 'admin';
                    $this->_auth->getStorage()->write($a);

                    require_once('Zend/Session/Namespace.php');
                    $session = new Zend_Session_Namespace('Luong_admin');
                    $session->setExpirationSeconds(7 * 24 * 3600);
                    if ($remember) {
                        Zend_Session::rememberMe();
                    }
                    $this->_redirect("/admin/index");
                } else {
                    $messages = $result->getMessages();
                    if (!empty($messages)) {
                        $this->view->error = $messages[0];
                    } else {
                        $this->view->error = $result->getData()->message;
                    }
                }
                /*
                $authAdapter = new Luong_Auth_Adapter_Api($username, $password, self::REQUIRE_ROLE);
                $result = $this->_auth->authenticate($authAdapter);
                
                if ($result->getCode() === Luong_Auth_Result::SUCCESS) {
                   
                } 
                */
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function registerAction() {
        $this->_helper->layout->setLayout('login');
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $userModel = $dbname->luong_users;
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $username = $f->filter($this->_request->getParam('username'));
            $password = $f->filter($this->_request->getParam('password'));
            $re_password = $f->filter($this->_request->getParam('re_password'));
            $check_user_exit = $userModel->findOne(array('username' => $username));

            if (empty($username)) {
                $error = "Bạn chưa nhập username";
            } elseif (empty($password)) {
                $error = "Bạn chưa nhập mật khẩu";
            } elseif (empty($re_password)) {
                $error = "Bạn chưa nhập lại mật khẩu";
            } elseif ($password !== $re_password) {
                $error = "Mật khẩu không trùng nhau!";
            } elseif (!empty($check_user_exit)) {
                $error = "Tài khoản đã được tạo trước đó!";
            }
            if (isset($error)) {
                $this->view->error = $error;
            } else {
                $newData = array(
                'username' => $username,
                'password' => md5(md5($password)),
                'slug' => Luong_Helper_Utils::cv2urltitle($username),
                'time' => time(),
                'date' => (int) date("ymd"),
                );
                $userModel->insert($newData);
                $this->_redirect($this->_contextPath . '?do=add');
            }
        }
    }
    public function indexAction() {
        $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
									<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
                                    <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
									<li>Thông tin tài khoản</li>
								</ul>';
        if ($this->_request->getParam('do') == 'add')
            $this->view->success = 'Thêm mới thành công';
        elseif ($this->_request->getParam('do') == 'update')
            $this->view->success = 'Thay đổi mật khẩu thành công';
    }

    public function changePassAction() {
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
									<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
                                    <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
									<li>Đổi mật khẩu</li>
								</ul>';
        if ($this->_request->isPost()) {
            $old_password = $this->_request->getParam('old_password');
            $new_password = $this->_request->getParam('new_password');
            $cf_password = $this->_request->getParam('cf_password');
            $userDAO = new UserModel();
            $old_password = md5(md5($old_password));
            $checkOldPass = $userDAO->checkUserByAccountAndPassword($this->_user->user_name, $old_password);
            if (!$checkOldPass)
                $this->_errors = "Mật khẩu cũ không đúng." . "<br/>";
            if ($new_password != $cf_password || empty($new_password))
                $this->_errors .= "Mật khẩu mới không khớp giống nhau." . "<br/>";

            if ($this->_errors) {
                $this->view->errors = $this->_errors;
            } else {
                $data['password'] = md5(md5($new_password));
                $userDAO->update($data, 'id=' . $this->_user->id);
                $this->_redirect($this->_contextPath . "?do=update");
            }
        }
    }

    public function logoutAction() {
        if ($this->_auth->getIdentity()) {
            $this->_auth->clearIdentity();
        }
        $this->_redirect('/admin/login.html');
    }

}
