<?php
session_start();
error_reporting(0);//屏蔽所有错误 正式运营时用
date_default_timezone_set ("Asia/Shanghai");
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once(S_ROOT."baimorsecode.php");
require_once(S_ROOT."baimorsecodefunction.php");
?>