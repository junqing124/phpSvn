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
$cls_data = new cls_data('svn_config');

$config_info = $cls_data->select_one_ex( array( 'where'=> "config_name='jsly'" ) );

if( 'modify' == $action )
{
	if( $config_info )
	{
		$cls_data->update_one( array( 'config_value'=> $jsly ), "config_name='jsly'" );
	}else
	{
		$cls_data->insert( array( 'config_value'=> $jsly, 'config_name'=> 'jsly', 'config_add_time'=> time() ) );
	}
	$config_info['config_value'] = $jsly;
}

?>
配置<br>
<form action='#' method='post'>
<input type='hidden' value='modify' name='action'>
拒审理由:(用,分隔)<br>
<textarea style="width:300px; height:200px;" name="jsly"><?php echo $config_info['config_value']; ?></textarea>
<br>
<input type="submit" value="修改">
</form>