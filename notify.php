<?php
	/*
	PHP默认识别的数据类型是application/x-www.form-urlencoded标准的数据类型。
	因此，对型如text/xml或者soap或者application/octet-stream
	和application/json格式之类的内容无法解析，如果用$_POST数组来接收就会失败！
	此时可以使用$GLOBALS['HTTP_RAW_POST_DATA']或file_get_contents('php://input')来获取提交的数据.
	*/
	
	/*
	function initPostData() {
		if (empty($_POST) && false !== strpos($this->contentType(), 'application/json')) {
			$content = file_get_contents('php://input');
			$post = (array)json_decode($content, true);
		} else {
			$post = $_POST;
		}
		return $post;
	}
	*/
	$Status = $_POST["Status"];
	$MerchantID = $_POST["MerchantID"];
	$TradeInfo = $_POST["TradeInfo"];
	$TradeSha = $_POST["TradeSha"];
	$mer_key = '12345678901234567890123456789012';
	$mer_iv = '1234567890123456';
	
	function create_aes_decrypt($parameter, $mer_key, $mer_iv) {
		return strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC',$mer_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $mer_iv));
	}
	
	function strippadding($string) {
		$slast = ord(substr($string, -1));
		$slastc = chr($slast);
		$pcheck = substr($string, -$slast);
		if (preg_match("/$slastc{" . $slast . "}/", $string)) {
			$string = substr($string, 0, strlen($string) - $slast);
			return $string;
		} else {
			return false;
		}
	}
	
	$TradeInfoJson =  create_aes_decrypt($TradeInfo, $mer_key, $mer_iv);
