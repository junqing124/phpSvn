<?php
	require_once( 'include/common.inc.php' );
	session_start();
	require_once( WEB_CLASS . '/class.user.php' );
    $page_title = 'V2后台';
    require_once( 'yz.php' );
	$cls_user = new cls_user( );
	$cls_user-> logout();
	show_next( '', 'login.htm' );
?>