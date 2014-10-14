<?php

class AjaxController extends Luong_Controller_Default_Action {

    public function init() {
        parent::init();
        $this->_helper->layout()->disableLayout();
    }

    public function districtAction() {
        $province = $this->_request->getParam('province');
        $districtDAO = new DistrictModel();
        $list = $districtDAO->fetchAll('province_id=' . $province);
        $this->view->list = $list;
    }

    public function orderAction() {
        $result = array(
            'status' => 999,
            'message' => '',
            'data' => array()
        );

        $configDAO = new ConfigModel();
        $SMGoodDAO = new SMGoodModel();
        try {
            $fullname = $this->_request->getParam('fullname');
            $mobile = $this->_request->getParam('mobile');
            $address = $this->_request->getParam('address');
            $content = $this->_request->getParam('content');
            $quantity = $this->_request->getParam('quantity');
            $email = $this->_request->getParam('email');

            if (empty($fullname)) {
                $result['message'] = 'Bạn hãy vui lòng nhập họ và tên.';
            } elseif (empty($mobile)) {
                $result['message'] = 'Bạn hãy vui lòng nhập số điện thoại.';
            } elseif (empty($email)) {
                $result['message'] = 'Bạn hãy vui lòng nhập địa chỉ email.';
            } elseif (empty($address)) {
                $result['message'] = 'Bạn hãy vui lòng nhập địa chỉ.';
            } elseif (empty($quantity)) {
                $result['message'] = 'Bạn hãy vui lòng nhập số lượng thẻ.';
            } else {
                $configSite = $configDAO->fetchRow('id=1');

                $good_code = 'YEARLY_BILLING';
                $good = $SMGoodDAO->getRowByCode($good_code);

                $token = Luong_Helper_Utils::getToken();
                $this->_helper->viewRenderer->setnorender(true);
                $url = Luong_Helper_Constant::URL_INIT_ORDER;
                $http = new Zend_Http_Client($url);
                $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                $http->setHeaders('token', $token);
                $http->request('POST');
                $http->setParameterPost(array(
                    'fullname' => $fullname,
                    'email' => $email,
                    'mobile' => $mobile,
                    'province_name' => '',
                    'district_name' => '',
                    'address' => $address,
                    'productId' => $good->id,
                    'quantity' => $quantity,
                    'payment_method' => 1,
                    'reference_code' => $this->getReferenceCode(),
                	'viewer_id' => empty($_COOKIE['viewer_id']) ? '' : $_COOKIE['viewer_id'],
                ));
                $request = $http->request();
                $response = json_decode($request->getBody());
                if ($response->status == 0) {
                    $result['status'] = $response->status;
                    $result['message'] = $response->message;
                    $result['data'] = $response->data;

                    $html = 'Họ và tên: ' . $fullname . '<br/>';
                    if (isset($quantity)) {
                        $html .= 'Số lượng: ' . $quantity . '<br/>';
                    }
                    $html .= 'Điện thoại: ' . $mobile . '<br/>';
                    $html .= 'Địa chỉ giao thẻ: ' . nl2br($address) . '<br/>';
                    if (isset($content)) {
                        $html .= 'Thông tin thêm: ' . nl2br($content) . '<br/>';
                    }

                    if (empty($response->data->code)) {
                        $subject = 'Đơn hàng từ khách hàng ' . $fullname;
                    } else {
                        $subject = 'Đơn hàng mới ' . $response->data->code;
                    }

                    $mail = new Zend_Mail('UTF-8');
                    $mail->setBodyHtml($html);
                    $mail->setFrom('noreply@edoctor.vn');
                    $mail->addTo('sale@edoctor.vn');
                    $notify_emails = explode(',', $configSite->email);
                    if (is_array($notify_emails)) {
                        foreach ($notify_emails AS $_notify_email) {
                            $mail->addCc($_notify_email);
                        }
                    }
                    $mail->setSubject($subject);
                    $mail->send();
                    $result['viewer_id'] = $_COOKIE['viewer_id'];
                } else {
                    $result['status'] = $response->status;
                    $result['message'] = $response->message;
                }
            }
        } catch (Exception $e) {
            $result['status'] = 999;
            $result['message'] = 'Có lỗi xảy ra, vui lòng thử lại hoặc liên hệ tổng đài 0903492113 để được hỗ trợ!';
        }
        echo json_encode($result);
    }
    
    public function e15regisAction() {
    	try {
    		$configDAO = new ConfigModel();
    		$configSite = $configDAO->fetchRow('id=1');
    		$arrMail = explode(',', $configSite->email);
    		$fullname 	= $this->_request->getParam('fullname');
            $address   = $this->_request->getParam('address');
    		$mobile 	= $this->_request->getParam('mobile');
    		$email 		= $this->_request->getParam('email');
    		$html 		=  'Họ và tên: ' . $fullname . '<br/>';
            $html       .= 'Địa chỉ: ' . $address . '<br/>';
    		$html 		.= 'Điện thoại: ' . $mobile . '<br/>';
    		$html 		.= 'Email: ' . $email . '<br/>';
    		$mail 		= new Zend_Mail('UTF-8');
    		$mail->setBodyHtml($html);
    		$mail->setFrom('noreply@edoctor.vn');
    		$mail->addTo('sale@edoctor.vn');
    		if (is_array($arrMail)):
	    		foreach ($arrMail AS $item):
	    			$mail->addCc($item);
	    		endforeach;
    		endif;
    		$mail->setSubject('Đăng ký thẻ E15');
    		$mail->send();
    		exit();
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }

    public function contactAction() {
        try {
            $configDAO = new ConfigModel();
            $configSite = $configDAO->fetchRow('id=1');
            $arrMail = explode(',', $configSite->email);
            $SMGoodDAO = new SMGoodModel();
            $code = 'YEARLY_BILLING';
            $row = $SMGoodDAO->getRowByCode($code);
            $token = Luong_Helper_Utils::getToken();
       
            $this->_helper->viewRenderer->setnorender(true);
            $fullname = $this->_request->getParam('fullname');
            $mobile = $this->_request->getParam('mobile');
            $address = $this->_request->getParam('address');
            $content = $this->_request->getParam('content');
            $quantity = $this->_request->getParam('quantity');
            $email = $this->_request->getParam('email');
            $url = Luong_Helper_Constant::URL_INIT_ORDER;
            $http = new Zend_Http_Client($url);
            $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            $http->setHeaders('token', $token);
            $http->request('POST');
            $http->setParameterPost(array(
                'fullname' => $fullname,
                'email' => $email,
                'mobile' => $mobile,
                'province_name' => '',
                'district_name' => '',
                'address' => $address,
                'productId' => $row->id,
                'quantity' => $quantity,
                'payment_method' => 1,
                'reference_code' => $this->getReferenceCode(),
            	'viewer_id' => empty($_COOKIE['viewer_id']) ? '' : $_COOKIE['viewer_id'],
            ));
            $response = $http->request();
            $response = json_decode($response->getBody());

            $html = 'Họ và tên: ' . $fullname . '<br/>';
            if (isset($quantity)):
                $html .= 'Số lượng: ' . $quantity . '<br/>';
            endif;
            $html .= 'Điện thoại: ' . $mobile . '<br/>';
            $html .= 'Địa chỉ giao thẻ: ' . $address . '<br/>';
            $html .= 'Thông tin thêm: ' . $content . '<br/>';
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($html);
            $mail->setFrom('noreply@edoctor.vn');
            $mail->addTo('sale@edoctor.vn');
            if (is_array($arrMail)):
                foreach ($arrMail AS $item):
                    $mail->addCc($item);
                endforeach;
            endif;
            $mail->setSubject('Đặt mua thẻ từ khách hàng ' . $fullname);
            $mail->send();
            echo  'viewer_id '.$_COOKIE['viewer_id'];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addformAction() {
        $id = $this->_request->getParam('id');
        $this->view->id = $id + 1;
        $medical_specialty = new MedicalSpecialtyModel();
        $this->view->medical_specialty_list = $medical_specialty->getall();
    }

}

//END CLASS