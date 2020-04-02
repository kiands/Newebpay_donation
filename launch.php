<?php
	$strs="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
	$random=substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),6);
	$OrderNo = $random.'at'.time();
	
	echo"
	<form name='Newebpay' method='post' action='http://www.pt.com/ebpay.php'>
		sis_bro_name:<input type='text' name='sis_bro_name' value=''>
		<br></br>
		Email:<input type='text' name='Email' value=''></input>
		<br></br>
		amount:<input type='text' name='amount' value=''>
		<br></br>
		<!--OrderNo:--><input type='hidden' name='OrderNo' value='$OrderNo'>
		<br></br>
		<input type='submit' value='Submit'>
	</form>";
