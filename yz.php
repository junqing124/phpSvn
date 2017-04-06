<?php
$admin_u = $_SESSION['admin_u'];
$admin_p = $_SESSION['admin_p'];
$admin_can_sh_code = $_SESSION['admin_can_sh_code'];
$admin_id = $_SESSION['admin_id'];
require_once(WEB_CLASS . '/class.user.php');
$cls_user = new cls_user($admin_u, $admin_p);
$is_admin = $cls_user->yz();

if ($is_admin['ack'] == 1)
{
	$cls_user->login();
} else {
	show_next('', "login.htm", 1);
    exit;
}