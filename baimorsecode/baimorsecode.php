<?
//生成摩斯密码 $str:明文 $key:秘钥
function baimorsecode_create($str, $key = "5734")
{
	//加密
	$str = authcodex($str, $key);

	//对密文取md5,作为校验码
	$strmd5 = md5($str);
	$keymd5 = md5($key);

	$strmd5sub = substr($strmd5, 0, 2); //只保留前两位作为效验
	$keymd5sub = substr($keymd5, 0, 2); //只保留前两位作为效验


	$str = $strmd5sub . $keymd5sub . $str;

	//把字符串转成二进制字符
	$codestr = getstringtobytestr($str);

	$msmm = bytestrtomsmm($codestr);
	return $msmm;
}

//解析摩斯密码 $morsestr:摩斯密码 $key:秘钥   成功返还明文 失败返回false
function baimorsecode_decode($morsestr, $key = "5734")
{
	if (!checkmsmm($morsestr)) {
		return false;
	}

	//转成二进制
	$codestr = msmmtobytestr($morsestr);
	$ckey_length = 2; //校验码长度

	//转成字符串
	$str = bytestrtostring($codestr);

	$strmd5 = substr($str, 0,  $ckey_length);
	$keymd5 = substr($str, $ckey_length,  $ckey_length);
	$str = substr($str, $ckey_length + $ckey_length);

	$strmd5buf = md5($str);
	$strmd5buf = substr($strmd5buf, 0, 2); //只保留前两位作为效验

	if ($strmd5buf != $strmd5) {
		return false;
	}

	$keymd5buf = md5($key);
	$keymd5buf = substr($keymd5buf, 0, 2); //只保留前两位作为效验
	if ($keymd5buf != $keymd5) {
		return false;
	}

	$str = authcodejx($str, $key); //解密
	return $str;
}


//摩斯密码字符串完整性效验
function checkmsmm($msmm)
{
	$codestr = msmmtobytestr($msmm);
	$arr = str_split($codestr, 8);
	$len = count($arr);
	for ($i = 0; $i < $len;) {
		$code = $arr[$i];
		$ejz = base_convert($code, 2, 10);
		$a = getcharbytenum($ejz);
		if (empty($a)) {
			return false;
		}
		$i += $a;
	}

	return true;
}

//把二进制字符转成摩斯密码字符串
function bytestrtomsmm($codestr)
{
	return strtr($codestr, '01', '._');
}

//把摩斯密码字符串转成二进制字符
function msmmtobytestr($msmm)
{
	return strtr($msmm, '._', '01');
}


//把二进制字符转成字符串
function bytestrtostring($codestr)
{
	$arr = str_split($codestr, 8);
	$strcodearr = array();
	foreach ($arr as $code) {
		$ejz = base_convert($code, 2, 10);
		$strcodearr[] = $ejz;
	}

	$restr = "";
	foreach ($strcodearr as $code) {
		$restr .= chr($code);
	}
	return $restr;
}

//把字符串转成二进制字符
function getstringtobytestr($str)
{
	$restr = "";
	$strlen = strlen($str);
	for ($i = 0; $i < $strlen; $i++) {
		$c = $str[$i];
		$asciicode = ord($c);
		$ejz = base_convert($asciicode, 10, 2);
		$ejz = str_pad($ejz, 8, "0", STR_PAD_LEFT);
		$restr .= $ejz;
	}
	return $restr;
}

/**
 *单字节字符：如果最高位是 0,那么这是一个 ASCII 字符 占一个字节 示例：'A' → 01000001 (ASCII)
 *多字节字符：
 *如果第一个字节以 110 开头，则接下来的一个字节必须以 10 开头,占2个字节。
 *如果第一个字节以 1110 开头，则接下来的两个字节都必须以 10 开头,占3个字节。
 *如果第一个字节以 11110 开头，则接下来的三个字节都必须以 10 开头,占4个字节。
 *示例：'中' → 11100100 10111101 10100000 (UTF-8)
 */
//获取码点占几个字符
function getcharbytenum($code)
{
	if (($code & 0b10000000) === 0) {
		return 1; // 0xxxxxxx
	}
	if (($code & 0b11100000) === 0b11000000) {
		return 2; // 110xxxxx
	}
	if (($code & 0b11110000) === 0b11100000) {
		return 3; // 1110xxxx
	}
	if (($code & 0b11111000) === 0b11110000) {
		return 4; // 11110xxx
	}

	// 非法 UTF-8 首字节
	return false;
}

/*
加密
*/
function authcodex($string, $key = '')
{
	$string = "" . $string;
	$ckey_length = 4; // 随机密钥长度 取值 0-32;
	$key = md5($key ? $key : "57342172"); //如果有密匙，则md5密匙，如果没有则md5 57342172
	$sj =  substr(md5(microtime()), -$ckey_length); //随即四位字符串
	$kk = md5($key . $sj);
	$kk_length = strlen($kk);  
	$string_length = strlen($string);
	$r = "";
	for ($i = 0; $i < $string_length; $i++) {
		$r .= chr(ord($string[$i]) ^ ord($kk[$i % $kk_length]));
	}
	return $sj . base64_encode($r);
}

/*
解密
*/
function authcodejx($string, $key = '')
{
	$string = "" . $string;
	$ckey_length = 4; // 随机密钥长度 取值 0-32; 和加密函数要保持一致
	$key = md5($key ? $key : "57342172"); //如果有密匙，则md5密匙，如果没有则md5 57342172
	$sj = substr($string, 0, $ckey_length);
	$kk = md5($key . $sj);
	$kk_length = strlen($kk);
	$aa = substr($string, $ckey_length);
	$string = base64_decode($aa);
	$string_length = strlen($string);
	$r = "";
	for ($i = 0; $i < $string_length; $i++) {
		$r .= chr(ord($string[$i]) ^ ord($kk[$i % $kk_length]));
	}
	return $r;
}
