<?php
require('include/common.inc.php');
session_start();
require('yz.php');
if ( 'add' == $action )
{
	$val = $cls_user->insert_ex( array( 'su_name'=>$username, 'su_password'=>jiami($password), 'su_add_time'=> time(), 'su_can_sh_code'=> intval( $can_sh_code ) ) );
	if( $val )
	{
		show_msg( '添加成功', 1 );
	}else
	{
		show_msg( '添加失败', 2 );		
	}
} else if( 'edit' == $action ) 
{
	$info = array( 'su_can_sh_code'=> intval( $can_sh_code ) );
	if( $password )
	{
		$info['su_password'] = jiami( $password );
	}
	$val = $cls_user->update_one( $info, "su_name='{$username}'" );
	if( $val )
	{
		show_msg( '修改成功', 1 );
	}else
	{
		show_msg( '修改失败', 2 );		
	}
}