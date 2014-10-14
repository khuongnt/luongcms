<?php

class Luong_Helper_Utils {

	static public function CovertUnicodeString($str) {
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		$str = preg_replace("/(Đ)/", 'D', $str);
		return strtolower($str);
	}

	static public function cv2urltitle($text) {
		$text = str_replace(
				array(' ', '%', "/", "\\", '"', '?', '<', '>', "#", "^", "`", "'", "=", "!", ":", ",,", "..", "*", "&", "__", "▄"), array('-', '', '', '', '', '', '', '', '', '', '', '', '-', '', '-', '', '', '', "_", "", ""), $text);
		$text = str_replace(
				array('_quot;', '”', '“', ',', '.'), array('', '', '', '', ''), $text);
		$chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");
		$uni[0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �");
		$uni[1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �");
		$uni[2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ");
		$uni[3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ");
		$uni[4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");
		$uni[5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �");
		$uni[6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ");
		$uni[7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ");
		$uni[8] = array("í", "ì", "ị", "ỉ", "ĩ");
		$uni[9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ");
		$uni[10] = array("đ");
		$uni[11] = array("Đ");
		$uni[12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");
		$uni[13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");
		for ($i = 0; $i <= 13; $i++) {
			$text = str_replace($uni[$i], $chars[$i], $text);
		}
		return strtolower($text);
	}

	/**
	 * Param: mail address.
	 * Return: true, or false.
	 */
	static public function checkemail($email = "") {
		$e = "/^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,3}$/i";
		if (!preg_match($e, trim($email)))
			return false;
		else
			return true;
	}

	static public function substr($str, $lenght) {
		if (strlen($str) <= $lenght) {
			return $str;
		} else {
			if (strpos($str, " ", $lenght) > $lenght) {
				$new_lenght = strpos($str, " ", $lenght);
				$new_str = substr($str, 0, $new_lenght) . "";
				return $new_str;
			}
			$new_str = substr($str, 0, $lenght) . "";
			return $new_str;
		}
	}
	static public function curPageURL() {
		$pageURL = 'http';
//        if ($_SERVER["HTTPS"] == "on") {
//            $pageURL .= "s";
//        }
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	static public function curPageDomain() {
		$pageURL = 'http';
//        if ($_SERVER["HTTPS"] == "on") {
//            $pageURL .= "s";
//        }
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
		return $pageURL;
	}

	
	public static function add_querystring_var($url, $key, $value) {
		$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
		$url = substr($url, 0, -1);
		if (strpos($url, '?') === false) {
			return ($url . '?' . $key . '=' . $value);
		} else {
			return ($url . '&' . $key . '=' . $value);
		}
	}

	public static function remove_querystring_var($url, $key) {
		if (is_array($key)) {
			foreach ($key as $keys) {
				$url = preg_replace('/(.*)(\?|&)' . $keys . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
				$url = substr($url, 0, -1);
			}
		} else {
			$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
			$url = substr($url, 0, -1);
		}
		return ($url);
	}
}