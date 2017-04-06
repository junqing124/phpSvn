<?php
require('include/common.inc.php');
session_start();
require('yz.php');
if( 'admin' != $admin_u )
{
	show_msg( 'NO', 2 );
	exit;
}
require_once( 'menu.php' );
$cls_data_user = new cls_data( 'svn_user' );
$list = $cls_data_user-> select_ex();
echo "<a href='user_edit.php?action=add'>添加用户</a>";
echo '<table>';
echo '<tr><td>User</td><td>Options</td></tr>';
foreach( $list as $info )
{
	echo "<tr><td>{$info['su_name']}</td><td><a href='user_edit.php?action=edit&username={$info['su_name']}'>New Password</a></td></tr>";
}
echo '</table>';