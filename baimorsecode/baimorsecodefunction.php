<?
//加密
function morsecodeencode($str, $key = "5734")
{
	$msmm = baimorsecode_create($str, $key);
	$rearr = array(
		"code" => 1,
		"msg" => $msmm
	);
	return $rearr;
}

//解密
function morsecodedecode($str, $key = "5734")
{
	$str = baimorsecode_decode($str, $key);
	if ($str === false) {
		$rearr = array(
			"code" => 0,
			"msg" => "解析失败"
		);
		return $rearr;
	}
	$rearr = array(
		"code" => 1,
		"msg" => $str
	);
	return $rearr;
}

function cs($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function csw($f,$somecontent){
	$handle = fopen($f, 'a');
    fwrite($handle, $somecontent);
    fclose($handle);
}