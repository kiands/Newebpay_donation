<?php
	//接收launch回传的金额
	$amount = $_POST["amount"];
	$OrderNo = $_POST["OrderNo"];
	
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
	$mer_key = '12345678901234567890123456789012';
	$mer_iv = '1234567890123456';
	
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
		<!--RespondType:--><input type='hidden' name='RespondType' value='JSON'></input>
		<!--Version:--><input type='hidden' name='Version' value='1.5'></input>
		<!--LangType:--><input type='hidden' name='LangType' value='zh-tw'></input>
		<!--MerchantOrderNo:--><input type='hidden' name='MerchantOrderNo' value=$OrderNo></input>
		<!--Amt:--><input type='hidden' name='Amt' value=$amount></input>
		<!--ItemDesc:--><input type='hidden' name='ItemDesc' value='donation'></input>
		Email:<input type='text' name='Email' value=''></input>
		<br></br>
		<!--LoginType:--><input type='hidden' name='LoginType' value=0></input>
		<!--NotifyURL:--><input type='hidden' name='NotifyURL' value='http://www.pt.com/notify.php'></input>
		<input type='submit' value='Submit'>
	</form>";
