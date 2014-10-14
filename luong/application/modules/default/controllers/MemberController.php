<?php

class MemberController extends Luong_Controller_Default_Action {

    public function isMember() {
        return !empty($this->_user);
    }

    public function init() {
        parent::init();
        if (!$this->isMember()) {
            $this->logoutAction();
        }
    }

    public function indexAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Thành viên</a>
							    	</div>';
    }

    public function activeAction() {
        $this->view->breadcrumb = ' <div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Xác thực tài khoản</a>
							    	</div>';
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags ( );
            $mobile = $f->filter($this->_request->getParam('mobile'));
            $captcha = $f->filter($this->_request->getParam('captcha'));
            $step = $f->filter($this->_request->getParam('step'));
            $activecode = $f->filter($this->_request->getParam('activecode'));
            $securimage = new Securimage();
            if ($step == 1) {
                if (empty($mobile))
                    $this->_errors .= 'Bạn vui lòng nhập số điện thoại<br/>';
                if (empty($captcha))
                    $this->_errors .= 'Bạn vui lòng nhập mã xác thực';
            } else {
                if (empty($activecode))
                    $this->_errors .= 'Bạn vui lòng nhập mã kích hoạt<br/>';
                if (empty($captcha))
                    $this->_errors .= 'Bạn vui lòng nhập mã xác thực';
            }

            if (empty($this->_errors)) {
                $chekcaptcha = $securimage->check($captcha);
                if ($chekcaptcha == false) {
                    $this->_errors .= 'Mã xác thực không đúng';
                    $this->view->errors = $this->_errors;
                    $this->view->mobile = $mobile;
                    $this->view->captcha = $captcha;
                    $this->view->activecode = $activecode;
                    $this->view->step = $step;
                } else {
                    $token = Luong_Helper_Utils::getToken();
                    /* Bước kích hoạt */
                    if ($step == 1) {
                        $url = Luong_Helper_Constant::URL_ACTIVE;
                        $http = new Zend_Http_Client($url);
                        $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                        $http->setHeaders('token', $token);
                        $http->request('POST');
                        $http->setParameterPost(array(
                            'mobile' => $mobile,
                        ));
                        $response = $http->request();
                        $response = json_decode($response->getBody());
                        if ($response->status == 0) {
                            $this->view->success = "Gửi mã kích hoạt thành công, hãy kiểm tra điện thoại và điền mã kích hoạt.";
                            $this->view->step = 2;
                            $this->view->mobile = $mobile;
                        } else {
                            $this->view->step = 1;
                            $this->view->errors = $response->message;
                            $this->view->mobile = $mobile;
                        }
                    } elseif ($step == 2) {
                        $url = Luong_Helper_Constant::URL_CHECK_ACTIVE;
                        $httpCheck = new Zend_Http_Client($url);
                        $httpCheck->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                        $httpCheck->setHeaders('token', $token);
                        $httpCheck->request('POST');
                        $httpCheck->setParameterPost(array(
                            'mobile' => $mobile,
                            'code' => $activecode
                        ));
                        $response = $httpCheck->request();
                        $response = json_decode($response->getBody());
                        if ($response->status == 0) {
                            $this->view->success = 'Kích hoạt và reset mật khẩu thành công, xin lấy mật khẩu từ tin nhắn và <a href="/dang-nhap.htm">đăng nhập tại đây</a>';
                            $this->view->step = 3;
                        } else {
                            $this->view->step = 2;
                            $this->view->errors = $response->message;
                            $this->view->mobile = $mobile;
                        }
                    }
                }
            } else {
                $this->view->errors = $this->_errors;
                $this->view->mobile = $mobile;
                $this->view->activecode = $activecode;
                $this->view->step = $step;
            }
        }
    }

    public function captchaAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $img = new Securimage();
        $img->show();
    }

    public function updateProfileAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Cập nhập thông tin</a>
							    	</div>';
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $data['email'] = $f->filter($this->_request->getParam('email'));
            $data['mobile'] = $f->filter($this->_request->getParam('mobile'));
            $data['fullname'] = $f->filter($this->_request->getParam('fullname'));
            $data['address'] = $f->filter($this->_request->getParam('address'));
            if (empty($data['mobile']))
                $this->_errors->mobile_error = "Bạn vui lòng nhập số điện thoại";
            if (empty($data['fullname']))
                $this->_errors->fullname_error = "Bạn vui lòng nhập họ và tên";
            if (empty($data['address']))
                $this->_errors->address_error = "Bạn vui lòng nhập địa chỉ";

            if ($this->_errors) {
                $this->view->errors = $this->_errors;
                $this->view->email = $data['email'];
                $this->view->mobile = $data['mobile'];
                $this->view->fullname = $data['fullname'];
                $this->view->address = $data['address'];
            } else {
                $data['email'] = $this->_user->email;
                $access_token = $this->_user->accessToken;
                $user_id = $this->_user->id;
                $reponse = Luong_Helper_Utils::updateProfile($user_id, $access_token, $data);
                if ($reponse->status == 0) {
                    $data = new stdClass();
                    $data->id = $this->_user->id;
                    $data->endDate = $this->_user->endDate;
                    $data->firstName = $this->_user->firstName;
                    $data->lastName = $this->_user->lastName;
                    $data->gender = $this->_user->gender;
                    $data->job = $this->_user->job;
                    $data->staDate = $this->_user->staDate;
                    $data->activationCode = $this->_user->activationCode;
                    $data->accessToken = $this->_user->accessToken;
                    $data->role = "member";
                    $data->phoneNumber = $reponse->data->phoneNumber;
                    $data->address = $reponse->data->address;
                    $data->email = $reponse->data->email;
                    $data->mobileVerified = $reponse->data->mobileVerified;
                    $data->fullName = $reponse->data->fullName;
                    $this->_auth->getStorage()->write($data);

                    $this->view->notice = "Cập nhật thông tin thành công.";
                    $this->view->email = $reponse->data->email;
                    $this->view->fullname = $reponse->data->fullName;
                    $this->view->address = $reponse->data->address;
                    $this->view->mobile = $reponse->data->phoneNumber;
                } else {
                    $this->view->message = $reponse->message;
                }
            }
        } else {
            $this->view->email = $this->_user->email;
            $this->view->fullname = $this->_user->fullName;
            $this->view->address = $this->_user->address;
            $this->view->mobile = $this->_user->phoneNumber;
        }
    }

    public function callLogAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Lịch sử cuộc gọi</a>
							    	</div>';
        $http = new Zend_Http_Client(Luong_Helper_Constant::URL_CALL_LOG);
        $http->request('POST');
        $http->setParameterPost(array(
            'mobile' => $this->_user->phoneNumber,
        ));
        $response = $http->request();
        $response = json_decode($response->getBody());
        $this->view->data = $response->data;
    }

    public function changePasswordAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Thay đổi mật khẩu</a>
							    	</div>';
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $old_pass = $f->filter($this->_request->getParam('old_pass'));
            $new_pass = $f->filter($this->_request->getParam('new_pass'));
            $renew_pass = $f->filter($this->_request->getParam('renew_pass'));

            if (empty($old_pass))
                $this->_errors .= "Bạn vui lòng nhập mật khẩu cũ </br>";
            if (empty($new_pass))
                $this->_errors .= "Bạn vui lòng nhập mật khẩu mới </br>";
            if (empty($renew_pass))
                $this->_errors .= "Bạn vui lòng nhập lại mật khẩu mới</br>";
            if ($new_pass != $renew_pass)
                $this->_errors .= "Mật khẩu mới không khớp nhau</br>";

            if ($this->_errors):
                $this->view->errors = $this->_errors;
            else:
                $token = Luong_Helper_Utils::getToken();
                $url = Luong_Helper_Constant::URL_CHANGE_PASS;
                $http = new Zend_Http_Client();
                $http->setUri($url);
                $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                $http->setHeaders('token', $token);
                $http->request('POST');
                $http->setParameterPost(array(
                    'email' => $this->_user->email,
                    'oldPassword' => $old_pass,
                    'newPassword' => $new_pass,
                ));
                $response = $http->request();
                $response = json_decode($response->getBody());
                if ($response->status == 0) {
                    $this->_success .= 'Thay đổi mật khẩu thành công';
                    $this->view->success = $this->_success;
                } else {
                    $this->_errors .= $response->message;
                    $this->view->errors = $this->_errors;
                }
            endif;
        }
    }

    /* Đặt mật khẩu khi đăng nhập bằng social */

    public function setPasswordAction() {
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $password = $f->filter($this->_request->getParam('password'));
            $repassword = $f->filter($this->_request->getParam('repassword'));

            if (strlen($password) < 6)
                $this->_errors['password_error'] = "Mật khẩu phải có nhiều hơn 6 ký tự ";

            if (strlen($repassword) < 6)
                $this->_errors['repassword_error'] = "Mật khẩu phải có nhiều hơn 6 ký tự ";

            if ($password != $repassword)
                $this->_errors['repassword_error'] = "Hai mật khẩu không khớp nhau";

            if ($this->_errors) {
                $this->view->errors = $this->_errors;
            } else {
                $this->_redirect('/twopage.htm');
            }
        }
    }

    /*
     * Danh sach don hang
     * */

    public function listOrderAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
						    	<a class="icon home" href="/">home</a>
						    	<span class="icon divider"></span>
						    	<a href="#" class="active">Lịch sử đơn hàng</a>
						    	</div>';
        try {
            $token = Luong_Helper_Utils::getToken();
            $access_token = $this->_user->accessToken;
            $reponse = Luong_Helper_Utils::getListOrder($token, $access_token);
            $this->view->data = $reponse->data;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function logoutAction() {
        if ($this->_auth->getIdentity()) {
            $this->_auth->clearIdentity();
        }
        session_destroy();
        $this->_redirect('/');
    }

}

//END CLASS