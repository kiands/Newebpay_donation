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
	
	// 导入配置文件
	$ini = parse_ini_file("info_config.ini");
	//引用ini参数
	$mer_key = $ini["mer_key"];
	$mer_iv = $ini["mer_iv"];
	$TradeInfo = $_POST["TradeInfo"];
	
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
	
	$TradeInfoArray =  create_aes_decrypt($TradeInfo, $mer_key, $mer_iv);
	
	$Status = $_POST["Status"];
	$MerchantID = $_POST["MerchantID"];
	$TradeSha = $_POST["TradeSha"];
	$Amt = $TradeInfoArray["Amt"];
	$TradeNo = $TradeInfoArray["TradeNo"];
	$MerchantOrderNo = $TradeInfoArray["MerchantOrderNo"];
	$EscrowBank = $TradeInfoArray["EscrowBank"];
	$PayTime = $TradeInfoArray["PayTime"];
	
	// 连接mysql
	$conn = new mysqli($ini["servername"],$ini["username"],$ini["password"],$ini["dbname"]) or die("Connection Failed<br/>");
	// 存储数据的语法
	$sql = "UPDATE `donation` SET Status='$Status',TradeNo='$TradeNo',EscrowBank='$EscrowBank',PayTime='$PayTime' WHERE MerchantOrderNo='$MerchantOrderNo';";
	// 初步存储数据
	$result = $conn->query($sql);
	// 关闭连接
	$conn->close();
