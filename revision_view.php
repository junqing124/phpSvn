<?php
require('include/common.inc.php');
session_start();
require('yz.php');
require_once( 'menu.php' );
if( $log_version )
{
	$command = "/usr/bin/cd /home/www";
	$command = "svn log -r {$log_version} -v {$svn_root} --username {$svn_user} --password {$svn_password} --no-auth-cache";
	//echo $command;
	$out = nl2br( shell_exec($command) );
	$list = explode( '<br />', $out );
	$list_result = array();
	foreach( $list as $key=> $str )
	{
		$str = trim( $str );
		if( 'M' == substr( $str, 0, 1 ) )
		{
			array_push( $list_result, array( 'action'=>'Modify', 'path'=>str_replace( 'M ', '', $str ) ) );
		}else if( 'A' == substr( $str, 0, 1 ) )
		{
			array_push( $list_result, array( 'action'=>'Add', 'path'=>str_replace( 'A ', '', $str ) ) );			
		}
	}
	$cls_data_rl = new cls_data( 'svn_revision_list' );
	$rl_info = $cls_data_rl->select_one_ex( array( 'where'=>"srl_revision={$log_version}" ) );
	$commit_date = date( 'Y-m-d H:i:s', $rl_info['srl_commit_time'] );
	echo "{$rl_info['srl_author']}[{$commit_date}]:{$rl_info['srl_msg']}";
	//p_r( $rl_info );
	echo '<table>';
	echo '<tr><td>Action</td><td>Path</td><td>View</td></tr>';
	foreach( $list_result as $info )
	{
		echo "<tr><td>{$info['action']}</td><td>{$info['path']}</td><td><a target='result' href='revision_view_detail.php?log_version={$log_version}&type={$info['action']}&path={$info['path']}'>Detail</a></td></tr>";
	}
	echo '</table>';
	echo "<iframe width=100% height=500 name='result'></iframe>";
	if( $admin_can_sh_code )
	{
		echo "<form target='frm' action='revision_action.php'><input type=hidden name=log_version value={$log_version}><input type=hidden name=action value=sh><label><input type=radio name='action_type' value=2>审核通过</label><label><input type=radio value=3 name='action_type'>审核不通过</label><input type=text name='sh_no_msg'><input type=submit value='提交'></form>";
		echo "<iframe width=100% frameborder=no height=50 name='frm'></iframe>";		
	}
}else
{
	show_msg('没选版本?',2);
}