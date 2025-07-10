<?php
session_start();
require_once('kcaptcha-master/KCAPTCHA.php');

use KCAPTCHA\KCAPTCHA;

$captcha = new KCAPTCHA();
$_SESSION['captcha_keystring'] = $captcha->getKeyString();
$captcha->captcha();
?>