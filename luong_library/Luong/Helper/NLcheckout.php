<?php

class Luong_Helper_NLcheckout {

    private $nganluong_url; // trang checkout ngân lượng https://www.nganluong.vn/checkout.php
    private $merchant_site_code; //merchant id đăng ký tại ngân lượng
    private $secure_pass; // pass khi đăng ký merchant site

    function __construct($nganluong_url, $merchant_site_code, $secure_pass) {
        $this->nganluong_url = $nganluong_url;
        $this->merchant_site_code = $merchant_site_code;
        $this->secure_pass = $secure_pass;
    }

    public function buildCheckoutUrlNew($return_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity = 1, $tax = 0, $discount = 0, $fee_cal = 0, $fee_shipping = 0, $order_description = '', $buyer_info = '', $affiliate_code = '') {
        $arr_param = array(
            'merchant_site_code' => strval($this->merchant_site_code),
            'return_url' => strval(strtolower($return_url)),
            'receiver' => strval($receiver), //tài khoản ngân lượng
            'transaction_info' => strval($transaction_info),
            'order_code' => strval($order_code),
            'price' => strval($price),
            'currency' => strval($currency), //hỗ trợ 2 loại tiền tệ currency usd,vnd
            'quantity' => strval($quantity), //số lượng mặc định 1
            'tax' => strval($tax),
            'discount' => strval($discount),
            'fee_cal' => strval($fee_cal),
            'fee_shipping' => strval($fee_shipping),
            'order_description' => strval($order_description),
            'buyer_info' => strval($buyer_info), //Họ tên người mua *|* Địa chỉ Email *|* Điện thoại *|* Địa chỉ nhận hàng format có dạng
            'affiliate_code' => strval($affiliate_code)
        );
        $secure_code = '';
        $secure_code = implode(' ', $arr_param) . ' ' . $this->secure_pass;
        $arr_param['secure_code'] = md5($secure_code);
        /* */
        $redirect_url = $this->nganluong_url;
        if (strpos($redirect_url, '?') === false) {
            $redirect_url .= '?';
        } else if (substr($redirect_url, strlen($redirect_url) - 1, 1) != '?' && strpos($redirect_url, '&') === false) {
            $redirect_url .= '&';
        }

        /* */
        $url = '';
        $url = 'currency=' . $arr_param['currency'];
        unset($arr_param['currency']);
        foreach ($arr_param as $key => $value) {
            $value = urlencode($value);
            if ($url == '') {
                $url .= $key . '=' . $value;
            } else {
                $url .= '&' . $key . '=' . $value;
            }
        }

        return $redirect_url . $url;
    }

    //Hàm xây dựng url, trong đó có tham số mã hóa (còn gọi là public key)
    public function buildCheckoutUrl($return_url, $receiver, $transaction_info, $order_code, $price) {

        // Mảng các tham số chuyển tới nganluong.vn
        $arr_param = array(
            'merchant_site_code' => strval($this->merchant_site_code),
            'return_url' => strtolower(urlencode($return_url)),
            'receiver' => strval($receiver),
            'transaction_info' => strval($transaction_info),
            'order_code' => strval($order_code),
            'price' => strval($price)
        );
        $secure_code = '';
        $secure_code = implode(' ', $arr_param) . ' ' . $this->secure_pass;
        $arr_param['secure_code'] = md5($secure_code);

        /* Bước 2. Kiểm tra  biến $redirect_url xem có '?' không, nếu không có thì bổ sung vào */
        $redirect_url = $this->nganluong_url;
        if (strpos($redirect_url, '?') === false) {
            $redirect_url .= '?';
        } else if (substr($redirect_url, strlen($redirect_url) - 1, 1) != '?' && strpos($redirect_url, '&') === false) {
            // Nếu biến $redirect_url có '?' nhưng không kết thúc bằng '?' và có chứa dấu '&' thì bổ sung vào cuối
            $redirect_url .= '&';
        }

        /* Bước 3. tạo url */
        $url = '';
        foreach ($arr_param as $key => $value) {
            if ($key != 'return_url')
                $value = urlencode($value);

            if ($url == '')
                $url .= $key . '=' . $value;
            else
                $url .= '&' . $key . '=' . $value;
        }

        return $redirect_url . $url;
    }

    /* Hàm thực hiện xác minh tính đúng đắn của các tham số trả về từ nganluong.vn */

    public function verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code) {
        // Tạo mã xác thực từ chủ web
        $str = '';
        $str .= ' ' . strval($transaction_info);
        $str .= ' ' . strval($order_code);
        $str .= ' ' . strval($price);
        $str .= ' ' . strval($payment_id);
        $str .= ' ' . strval($payment_type);
        $str .= ' ' . strval($error_text);
        $str .= ' ' . strval($this->merchant_site_code);
        $str .= ' ' . strval($this->secure_pass);

        // Mã hóa các tham số
        $verify_secure_code = '';
        $verify_secure_code = md5($str);

        // Xác thực mã của chủ web với mã trả về từ nganluong.vn
        if ($verify_secure_code === $secure_code)
            return true;

        return false;
    }

}

//Call function buildCheckoutUrlNew để build url checkout sang ngân lượng
//Call  function verifyPaymentUrl để xác nhận thông tin khi khách hàng thanh toán thành công ngân lượng chả về tham số dùng để update đơn hàng, action khác...
?>