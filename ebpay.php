<?php
	//接收launch回传的内容
	$name = $_POST["sis_bro_name"];
	$Email = $_POST["Email"];
	$amount = $_POST["amount"];
	$OrderNo = $_POST["OrderNo"];
	
	// 导入配置文件
	$ini = parse_ini_file("info_config.ini");
	// 连接mysql
	$conn = new mysqli($ini["servername"],$ini["username"],$ini["password"],$ini["dbname"]) or die("Connection Failed<br/>");
	// 存储数据的语法
	$sql = "INSERT INTO `donation`(`Name`, `Email`, `Amt`, `MerchantOrderNo`) VALUES ('{$name}', '{$Email}', '{$amount}', '{$OrderNo}');";
	// 初步存储数据
	$result = $conn->query($sql);
	// 关闭连接
	if($result){
		echo "<h1>Success! Press Submit to jump to Newebpay.</h1>";
		$conn->close();
	} else {
		echo "<h1>Oops, there's an error.</h1>";
		$conn->close();
	}
	
	//function create_mpg_aes_encrypt ($parameter = "" , $key = "", $iv = "") {
	function create_mpg_aes_encrypt ($parameter, $mer_key, $mer_iv) {
		$return_str = '';
		if (!empty($parameter)) {
			//將參數經過 URL ENCODED QUERY STRING
			$return_str = http_build_query($parameter);
		}
		return trim(bin2hex(openssl_encrypt(addpadding($return_str), 'aes-256-cbc', $mer_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $mer_iv)));
	}
	
	function addpadding($string, $blocksize = 32) {
		$len = strlen($string);
		$pad = $blocksize - ($len % $blocksize);
		//注意是.=
		$string .= str_repeat(chr($pad), $pad);
		return $string;
	}
	
	$trade_info_arr = ['MerchantID' => 3430112,'RespondType' => 'JSON','TimeStamp' => time(),'Version' => 1.5,'MerchantOrderNo' => $OrderNo,'Amt' => $amount,'ItemDesc' => 'donation'];
	//引用ini参数
	$mer_key = $ini["mer_key"];
	$mer_iv = $ini["mer_iv"];
	
	//交易資料經 AES 加密後取得 TradeInfo
	$TradeInfo = create_mpg_aes_encrypt($trade_info_arr, $mer_key, $mer_iv);
	//Shop's HashKey
	$HashKey = "HashKey="."";
	//Shop's HashIV
	$HashIV = "HashIV="."";
	$TradeSha = strtoupper(hash("sha256", "$HashKey.'&'.$TradeInfo.'&'.$HashIV"));
	
	//输出显式HTML表单
	echo"
	<form name='Newebpay' method='post' action='http://www.pt.com/check.php'>
		<!--MerchantID:--><input type='hidden' name='MerchantID' value=3430112></input>
		<!--TradeInfo:--><input type='hidden' name='TradeInfo' value=$TradeInfo></input>
		<!--TradeSha:--><input type='hidden' name='TradeSha' value=$TradeSha></input>
		<!--RespondType:--><input type='hidden' name='RespondType' value='String'></input>
		<!--Version:--><input type='hidden' name='Version' value='1.5'></input>
		<!--LangType:--><input type='hidden' name='LangType' value='zh-tw'></input>
		<!--MerchantOrderNo:--><input type='hidden' name='MerchantOrderNo' value=$OrderNo></input>
		<!--Amt:--><input type='hidden' name='Amt' value=$amount></input>
		<!--ItemDesc:--><input type='hidden' name='ItemDesc' value='donation'></input>
		<!--Email:--><input type='hidden' name='Email' value=$Email></input>
		<!--LoginType:--><input type='hidden' name='LoginType' value=0></input>
		<!--NotifyURL:--><input type='hidden' name='NotifyURL' value='http://www.pt.com/notify.php'></input>
		<input type='submit' value='Submit'>
	</form>";
	
	//测试AES加密的输出
	//echo trim(bin2hex(openssl_encrypt(addpadding(http_build_query($trade_info_arr)), 'AES-256-CBC', $mer_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $mer_iv)));
