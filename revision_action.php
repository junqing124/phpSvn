<?php
require('include/common.inc.php');
session_start();
require('yz.php');
$cls_data_rl = new cls_data( 'svn_revision_list' );
if( 'sh' == $action )
{
	$info = array();
	if( 2 == $action_type )
	{
		$info = array( 'srl_status'=> 2, 'srl_sh_user'=> $admin_u, 'srl_sh_time'=> time() );
	}else if( 3 == $action_type )
	{
		$info = array( 'srl_status'=> 3, 'srl_sh_user'=> $admin_u, 'srl_sh_time'=> time(), 'srl_sh_faild_msg'=>$sh_no_msg );
	}
	//p_r( $info );
	$val = $cls_data_rl->update_one( $info, "srl_revision={$log_version}" );
	if( $val )
	{		
		echo 'ok';
	}else
	{
		echo $cls_data_rl->get_error();
		echo 'no';
	}
    echo '<script>window.parent.close();</script>';
}