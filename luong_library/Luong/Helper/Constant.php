<?php

class Luong_Helper_Constant {

	const SITE_URL = "http://edoctor.vn";
	const META_TITLE = "";
	const META_DESCRIPTION = "";
	const META_KEYWORDS = "";
	const SMART_STATUS = 0;
	const PAGE_ITEM = 20;
	const PAGE_ITEM_DEFAULT = 8;
	const LIMIT_SEARCH = 5;
	const LOG_INFO = "INFO";
	const LOG_ERROR = "ERROR";
	const LOG_INSERT = "INSERT";
	const LOG_UPDATE = "UPDATE";
	const LOG_DELETE = "DELETE";
	const LOG_PATH = "LOG_DIR";
	const USER_ADMIN = "ADMIN";
	const USER_MEMBER = "MEMBER";
	const URL_USER_LOGIN = 'http://api.edoctor.vn/user/auth/login';
	const URL_USER_INFO = 'http://api.edoctor.vn/consumer/user_info';
	const URL_CALL_LOG = 'http://api.edoctor.vn/consumer/user/call_logs';
	const URL_GET_TOKEN_KEY = 'http://api.edoctor.vn/initialize';
	const URL_LOGIN = 'http://api.edoctor.vn/consumer/auth/login';
	const URL_GETUSERINFO = 'http://account.edoctor.vn/getUserInfo';
	const URL_CHANGE_PASS = 'http://api.edoctor.vn/consumer/auth/change_password';
	const URL_ACTIVE = 'http://api.edoctor.vn/consumer/activation/request';
	const URL_CHECK_ACTIVE = 'http://api.edoctor.vn/consumer/activation/confirm';
	const URL_LOGIN_SOCIAL = 'http://api.edoctor.vn/consumer/auth/login_social';
	const URL_REGISTER = 'http://api.edoctor.vn/consumer/auth/register';
	const URL_UPDATE_PROFILE = 'http://api.edoctor.vn/consumer';
	const URL_INIT_ORDER = 'http://api.edoctor.vn/order/init';
	const URL_UPDATE_ORDER = 'http://api.edoctor.vn/order/update';
	const URL_COMPLETE_ORDER = 'http://api.edoctor.vn/order/complete';
	const URL_LIST_PRODUCTS = 'http://api.edoctor.vn/order/products';
	const URL_GET_PRODUCT = 'http://api.edoctor.vn/order/product';
	const URL_GET_LISTORDER = 'http://api.edoctor.vn/order/list';
	const URL_RECHARGE = 'http://api.edoctor.vn/utility/recharge';
	const API_KEY = 'ede9469e42fb6f06';
	const API_SECRERT = 'a0f3c1f207cda50454093be2c2cce40b';
	/* END API */

	/* SMS API */
	const URL_SMS = 'http://222.255.29.40/smsservice/send.asmx/SendText';
	const SMS_USERNAME = 'edoctor';
	const SMS_PASSWORD = '_edoctor!$';
	const SMS_SERVICE = 'EKH';
	const SMS_KEYWORD = 'EKH';

	/* Ngan luong */
	const MERCHANT_ID = "33927";
	const MERCHANT_PASS = "eDS!@#45";
	const URL_WS = "https://www.nganluong.vn/micro_checkout_api.php?wsdl";
	const EMAIL_RECEIVER = "sale@edoctor.vn";
	const NganLuong_Checkout_Url = "https://www.nganluong.vn/checkout.php";

	/*
	 * Facebook app test */
// 	const FB_APPID              = "1432528763677643";
// 	const FB_APPSECRERT         = "087934a22d897db8f6211610c1f3c4f1";
	/* Facebook */
	const FB_APPID = "1431977903732729";
	const FB_APPSECRERT = "7b9d1f73477357c8b378fa95afcb41fe";
	const ASK_SECREPT = "ASK@EDOCTOR.VN";
	const ASK_RETURN = "http://qna_dev.edoctor.vn/";
	const CHECK_LOGIN = "http://qna_dev.edoctor.vn/checkLogin.php";
	/* Login */
	const login_sso = "http://account.edoctor.vn/";

	/* Edoctor Site */
	const Luong_CARD = 'http://edoctor.vn/the-cham-soc-suc-khoe-edoctor.htm';

	public static $_order_status_code_map = array(
		'DONE' => 'Đã hoàn thành',
		'INIT' => 'Lỗi giao dịch',
		'PENDING' => 'Chờ xử lý',
		'CANCEL' => 'Hủy',
	);
	public static $_order_type_map = array(
		1 => 'Thanh toán tại nhà',
		2 => 'ATM - ngân hàng',
		3 => 'Ngân lượng',
	);
	
	public static $_order_reason_cancel = array(
		'Khách hàng không đồng ý thanh toán',
		'Khách hàng không xác nhận việc đặt hàng',
		'Không liên lạc được với khách hàng',
	);

}
