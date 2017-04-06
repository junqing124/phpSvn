<?php
require('include/common.inc.php');
session_start();
require_once(WEB_CLASS . '/class.user.php');
$password = jiami( $password );
$cls_user = new cls_user($username, $password);
$is_admin = $cls_user->yz();
if ($is_admin['ack'] == 1)
{
	$cls_user->login();
	show_next('', 'index.php');
} else {
	show_next('', "login.htm", 1);
    exit;
}