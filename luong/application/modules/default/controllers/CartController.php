<?php

class CartController extends Luong_Controller_Default_Action {

    public function addressAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $id = $this->_request->getParam('id');
        $addressDAO = new AddressModel();
        $row = $addressDAO->addressById($id);
        $_SESSION['info']['fullname'] = $row->fullname;
        $_SESSION['info']['mobile'] = $row->mobile;
        $_SESSION['info']['province'] = $row->province_id;
        $_SESSION['info']['province_name'] = $row->province_name;
        $_SESSION['info']['district'] = $row->district_id;
        $_SESSION['info']['district_name'] = $row->district_name;
        $_SESSION['info']['address'] = $row->address;
        $_SESSION['info']['address_id'] = $row->id;
    }

    public function newAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $qty = $this->_request->getParam('qty');
        $code = $this->_request->getParam('code');
        /*
         * Phần này cho phép mua nhiều loại thẻ trong 1 hoá đơn
         * 
          if(isset($_SESSION['cart'][$code]))
          {
          $qty = $_SESSION['cart'][$code] + $qty;
          } else {
          $qty;
          }
          $_SESSION['cart'][$code]=$qty;
         */
        if (isset($_SESSION['cart'][$code])) {
//             $qty = $_SESSION['cart'][$code] + $qty;
        	   $qty = $_SESSION['cart'][$code];
        } else {
            unset($_SESSION['cart']);
        }
        $_SESSION['cart'][$code] = $qty;
    }

    public function showCartAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $product_1 = $f->filter($this->_request->getParam('product_1'));
            $product_2 = $f->filter($this->_request->getParam('product_2'));
            $product_3 = $f->filter($this->_request->getParam('product_3'));
            $product_4 = $f->filter($this->_request->getParam('product_4'));
            $product_5 = $f->filter($this->_request->getParam('product_5'));
            if ($product_1)
                $_SESSION['cart'][1] = $product_1;
            if ($product_2)
                $_SESSION['cart'][2] = $product_2;
            if ($product_3)
                $_SESSION['cart'][3] = $product_3;
            if ($product_4)
                $_SESSION['cart'][4] = $product_4;
            if ($product_5)
                $_SESSION['cart'][5] = $product_5;
        }
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $value) {
                $item[] = $key;
            }
            $productIds = implode(",", $item);
            $token = Luong_Helper_Utils::getToken();
            $data = Luong_Helper_Utils::getListProduct($token, $productIds);
            $this->view->list = $data->data;
        }
    }

    public function editCartAction() {
        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $product_1 = $f->filter($this->_request->getParam('product_1'));
            $product_2 = $f->filter($this->_request->getParam('product_2'));
            $product_3 = $f->filter($this->_request->getParam('product_3'));
            $product_4 = $f->filter($this->_request->getParam('product_4'));
            $product_5 = $f->filter($this->_request->getParam('product_5'));
            if ($product_1)
                $_SESSION['cart'][1] = $product_1;
            if ($product_2)
                $_SESSION['cart'][2] = $product_2;
            if ($product_3)
                $_SESSION['cart'][3] = $product_3;
            if ($product_4)
                $_SESSION['cart'][4] = $product_4;
            if ($product_5)
                $_SESSION['cart'][5] = $product_5;
        }

        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $value) {
                $item[] = $key;
            }
            $productIds = implode(",", $item);
            $token = Luong_Helper_Utils::getToken();
            $data = Luong_Helper_Utils::getListProduct($token, $productIds);
            $this->view->list = $data->data;
        }
    }

    public function step1Action() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Thanh toán</a>
							    	</div>';
        if (empty($_SESSION['cart'])) {
        	$SMGoodDAO = new SMGoodModel();
        	$code = 'YEARLY_BILLING';
        	$row = $SMGoodDAO->getRowByCode($code);
        	$_SESSION['cart'][$row->id] = 1;
        	$_SESSION['info']['total'] = $row->price;
        	$this->_redirect('/onepage.htm');
        } else {
            if (isset($this->_user->email)) {
                $this->_redirect('/twopage.htm');
            }

            foreach ($_SESSION['cart'] as $key => $value) {
                $item[] = $key;
            }
            $productIds = implode(",", $item);
            $token = Luong_Helper_Utils::getToken();
            $data = Luong_Helper_Utils::getListProduct($token, $productIds);
            $this->view->list = $data->data;
        }
    }

    public function step2Action() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
								    	<a class="icon home" href="/">home</a>
								    	<span class="icon divider"></span>
								    	<a href="#" class="active">Thanh toán</a>
							    	</div>';
        $productDAO = new ProductModel();
        $provinceDAO = new ProvinceModel();
        $districtDAO = new DistrictModel();
        $addressDAO = new AddressModel();
        if (isset($this->_user->id)) {
            $_SESSION['info']['email'] = $this->_user->email;
            $listAddress = $addressDAO->listAddress($this->_user->id);
            $this->view->listAddress = $listAddress;
            if ($listAddress) {
                $this->view->authFlag = 1;
            }
        } else {
            $this->view->authFlag = 0;
            if (isset($_SESSION['info'])) {
                $province = empty($_SESSION['info']['province']) ? '' : $_SESSION['info']['province'];
                $this->view->fullname = empty($_SESSION['info']['fullname']) ? '' : $_SESSION['info']['fullname'];
                $this->view->mobile = empty($_SESSION['info']['mobile']) ? '' : $_SESSION['info']['mobile'];
                $this->view->email = empty($_SESSION['info']['email']) ? '' : $_SESSION['info']['email'];
                $this->view->province = empty($_SESSION['info']['province']) ? '' : $_SESSION['info']['province'];
                $this->view->district = empty($_SESSION['info']['district']) ? '' : $_SESSION['info']['district'];
                $this->view->address = empty($_SESSION['info']['address']) ? '' : $_SESSION['info']['address'];
            }
        }

        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $fullname = $f->filter($this->_request->getParam('fullname'));
            $mobile = $f->filter($this->_request->getParam('mobile'));
            $email = $f->filter($this->_request->getParam('email'));
            $province = $f->filter($this->_request->getParam('province'));
            $district = $f->filter($this->_request->getParam('district'));
            $address = $f->filter(stripcslashes($this->_request->getParam('address')));

            if (empty($fullname))
                $this->_errors->fullname_error = "Bạn vui lòng nhập đầy đủ họ và tên.";
            if ($mobile == "")
                $this->_errors->mobile_error = "Bạn vui lòng nhập số điện thoại.";
            if (empty($email))
                $this->_errors->email_error = "Bạn vui lòng nhập địa chỉ email.";
            else if (Luong_Helper_Utils::checkemail($email) == false)
                $this->_errors->email_error = "Địa chỉ email bạn nhập không đúng.";
            if ($province == 0)
                $this->_errors->province_error = "Bạn vui lòng nhập tỉnh/thành phố.";
            if ($district == 0)
                $this->_errors->district_error = "Bạn vui lòng chọn quận/huyện.";
            if ($address == "")
                $this->_errors->address_error = "Bạn vui lòng nhập địa chỉ.";

            if ($this->_errors) {
                $this->view->errors = $this->_errors;
                $this->view->fullname = $fullname;
                $this->view->mobile = $mobile;
                $this->view->email = $email;
                $this->view->province = $province;
                $this->view->district = $district;
                $this->view->address = $address;
            } else {
                if (isset($this->_user->id)) {
                    $data = array('fullname' => $fullname, 'mobile' => $mobile, 'district_id' => $district, 'address' => $address, 'user_id' => $this->_user->id);
                    $addressDAO->insert($data);
                    $lastId = $addressDAO->getAdapter()->lastInsertId();
                    $_SESSION['info']['address_id'] = $lastId;
                }
                $province_name = $provinceDAO->fetchRow('id=' . $province);
                $district_name = $districtDAO->fetchRow('id=' . $district);
                $_SESSION['info']['fullname'] = $fullname;
                $_SESSION['info']['mobile'] = $mobile;
                $_SESSION['info']['email'] = $email;
                $_SESSION['info']['province'] = $province;
                $_SESSION['info']['province_name'] = $province_name->name;
                $_SESSION['info']['district'] = $district;
                $_SESSION['info']['district_name'] = $district_name->name;
                $_SESSION['info']['address'] = $address;
                $this->_redirect('/threepage.htm');
            }
        }

        if (empty($_SESSION['cart'])) {
            $this->_redirect(Luong_Helper_Constant::Luong_CARD);
        } else {
            foreach ($_SESSION['cart'] as $key => $value) {
                $item[] = $key;
            }
            $productIds = implode(",", $item);
            $token = Luong_Helper_Utils::getToken();
            $data = Luong_Helper_Utils::getListProduct($token, $productIds);
            $this->view->list = $data->data;
        }

        $this->view->listProvince = $provinceDAO->listProvinceDefault();
        if (!empty($province)) {
            $this->view->listDistrict = $districtDAO->fetchAll('province_id=' . $province);
        }
    }

    public function step3Action() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Thanh toán</a>
							    	</div>';
        if (empty($_SESSION['cart'])) {
            $this->_redirect(Luong_Helper_Constant::Luong_CARD);
        } else {
            foreach ($_SESSION['cart'] as $key => $value) {
                $item[] = $key;
            }
            $productIds = implode(",", $item);
            $token = Luong_Helper_Utils::getToken();
            $data = Luong_Helper_Utils::getListProduct($token, $productIds);
            $this->view->list = $data->data;
        }

        if ($this->_request->isPost()) {
            $f = new Zend_Filter_StripTags();
            $payment_method = $f->filter($this->_request->getParam('phuongthuc'));
            $token = Luong_Helper_Utils::getToken();
            $arrayProId = array_keys($_SESSION['cart']);
            $arrayProQty = array_values($_SESSION['cart']);
            if (isset($_SESSION['info']['order_code'])) {
                $url = Luong_Helper_Constant::URL_UPDATE_ORDER;
                $http = new Zend_Http_Client($url);
                $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                $http->setHeaders('token', $token);
                $http->request('POST');
                $http->setParameterPost(array(
                    'fullname' => $_SESSION['info']['fullname'],
                    'email' => $_SESSION['info']['email'],
                    'mobile' => $_SESSION['info']['mobile'],
                    'province_name' => $_SESSION['info']['province_name'],
                    'district_name' => $_SESSION['info']['district_name'],
                    'address' => $_SESSION['info']['address'],
                    'productId' => $arrayProId[0],
                    'quantity' => $arrayProQty[0],
                    'payment_method' => $payment_method,
                    'order_code' => $_SESSION['info']['order_code'],
                    'reference_code' => empty($_COOKIE['referer_code']) ? '' : $_COOKIE['referer_code'],
                ));
                $response = $http->request();
                $response = json_decode($response->getBody());
            } else {
                $url = Luong_Helper_Constant::URL_INIT_ORDER;
                $http = new Zend_Http_Client($url);
                $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                $http->setHeaders('token', $token);
                if ($this->_user->accessToken)
                    $http->setHeaders('access-token', $this->_user->accessToken);
                $http->request('POST');
                $http->setParameterPost(array(
                    'fullname' => $_SESSION['info']['fullname'],
                    'email' => $_SESSION['info']['email'],
                    'mobile' => $_SESSION['info']['mobile'],
                    'province_name' => $_SESSION['info']['province_name'],
                    'district_name' => $_SESSION['info']['district_name'],
                    'address' => $_SESSION['info']['address'],
                    'productId' => $arrayProId[0],
                    'quantity' => $arrayProQty[0],
                    'payment_method' => $payment_method,
                    'reference_code' => empty($_COOKIE['referer_code']) ? '' : $_COOKIE['referer_code'],
                ));
                $response = $http->request();
                $response = json_decode($response->getBody());
            }
            if ($response->status == 0) {
                $_SESSION['info']['order_code'] = $response->data->code;
                if ($payment_method == 1) {
                    $this->_redirect('/thanh-toan-tien-mat.htm');
                } else if ($payment_method == 2) {
                    $this->_redirect('/thanh-toan-qua-ngan-hang.htm');
                } else if ($payment_method == 3) {
                    $return_url = Luong_Helper_Utils::curPageDomain() . "/thanh-toan-thanh-cong.htm";
                    $cancel_url = "/threepage.htm";
                    $receiver = Luong_Helper_Constant::EMAIL_RECEIVER;
                    $transaction_info = "";
                    $order_code = $_SESSION['info']['order_code'];
                    $price = $_SESSION['info']['total'];
                    $quantity = array_sum($_SESSION['cart']);
                    $nlCheckout = new Luong_Helper_NLcheckout(Luong_Helper_Constant::NganLuong_Checkout_Url, Luong_Helper_Constant::MERCHANT_ID, Luong_Helper_Constant::MERCHANT_PASS);
                    $url = $nlCheckout->buildCheckoutUrlNew($return_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity);
                    $this->_redirect($url);
                }
            } else {
                $this->view->error = $response->message;
                $this->view->payment_method = $payment_method;
            }
        }/* END IF POST */
    }

    /* End step3 */

    public function nlSuccessAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
								    	<a class="icon home" href="/">home</a>
								    	<span class="icon divider"></span>
								    	<a href="#" class="active">Thanh toán thành công</a>
							    	</div>';
        $nganluong_url = "https://www.nganluong.vn/checkout.php";
        $nlCheckout = new Luong_Helper_NLcheckout($nganluong_url, Luong_Helper_Constant::MERCHANT_ID, Luong_Helper_Constant::MERCHANT_PASS);
        $request = $this->_request->getParams();
        $f = new Zend_Filter_StripTags();
        $transaction_info = $f->filter($this->_request->getParam('transaction_info'));
        $order_code = $f->filter($this->_request->getParam('order_code'));
        $price = $f->filter($this->_request->getParam('price'));
        $payment_id = $f->filter($this->_request->getParam('payment_id'));
        $payment_type = $f->filter($this->_request->getParam('payment_type'));
        $error_text = $f->filter($this->_request->getParam('transaction_info'));
        $secure_code = $f->filter($this->_request->getParam('secure_code'));
        $token_nl = $f->filter($this->_request->getParam('token_nl'));
        $verifyResponseUrl = $nlCheckout->verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code);
        if ($verifyResponseUrl == 1) {
            unset($_SESSION['cart']);
            unset($_SESSION['info']['total']);
            unset($_SESSION['info']['step']);
            $token = Luong_Helper_Utils::getToken();
            $url = Luong_Helper_Constant::URL_COMPLETE_ORDER;
            $http = new Zend_Http_Client($url);
            $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            $http->setHeaders('token', $token);
            $http->request('POST');
            $http->setParameterPost(array(
                'order_code' => $order_code,
                'payment_id' => $payment_id,
                'secure_code' => $secure_code,
                'amount' => $price
            ));
            $response = $http->request();
            $response = json_decode($response->getBody());
            if ($response->status == 0) {
                $this->view->list = $response->data;
                $this->view->success = true;
                $this->view->code = $_SESSION['info']['order_code'];
                $this->view->email = $_SESSION['info']['email'];
            } else {
                $this->view->success = false;
            }
        }
    }

    public function cashSuccessAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Thông báo</a>
							    	</div>';
        if (isset($_SESSION['info']['email']))
            $_SESSION['complete']['email'] = $_SESSION['info']['email'];

        if (isset($_SESSION['info']['order_code']))
            $_SESSION['complete']['code'] = $_SESSION['info']['order_code'];

        $this->view->email = $_SESSION['complete']['email'];
        $this->view->code = $_SESSION['complete']['code'];
        unset($_SESSION['info']);
        unset($_SESSION['cart']);
    }

    public function bankSuccessAction() {
        $this->view->breadcrumb = '<div id="breadcrumb" class="inner">
							    	<a class="icon home" href="/">home</a>
							    	<span class="icon divider"></span>
							    	<a href="#" class="active">Thông báo</a>
							    	</div>';
        if (isset($_SESSION['info']['email']))
            $_SESSION['complete']['email'] = $_SESSION['info']['email'];

        if (isset($_SESSION['info']['order_code']))
            $_SESSION['complete']['code'] = $_SESSION['info']['order_code'];

        $this->view->email = $_SESSION['complete']['email'];
        $this->view->code = $_SESSION['complete']['code'];
        unset($_SESSION['info']);
        unset($_SESSION['cart']);
    }

}

//END CLASS