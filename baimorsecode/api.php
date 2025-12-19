<?php
require_once("common.php");
$str = trim($_REQUEST["s"]);
$k = trim($_REQUEST["k"]);
$method = trim($_REQUEST["method"]);

if (strlen($str)<1) {
	$rearr = array(
		"code" => 0,
		"msg" => "参数错误"
	);
	returnjsondata($rearr);
}

if ($method == "morsecodeencode") {
	//加密
	$rearr = morsecodeencode($str, $k);
	returnjsondata($rearr);
} else if ($method == "morsecodedecode") {
	//解密
	$rearr = morsecodedecode($str, $k);
	returnjsondata($rearr);
} else {
	$rearr = array(
		"code" => 0,
		"msg" => "未找到指定接口"
	);
	returnjsondata($rearr);
}


function returnjsondata($rearr)
{
	$a=json_encode($rearr);
	echo $a;
	exit;
}
