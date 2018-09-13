<?php
require('include/common.inc.php');
session_start();
require('yz.php');
if( 'admin' != $admin_u )
{
	show_msg( 'NO', 2 );
	exit;
}
$last_month_1 = strtotime( '-1 months' );
$last_month_2 = strtotime( '-2 months' );
$last_month_3 = strtotime( '-3 months' );
require_once( 'menu.php' );
$cls_data_rl = new cls_data( 'svn_revision_list' );
$list_srl = $cls_data_rl->select_ex( array( 'group'=> 'srl_author', 'where'=> 'srl_author!=""', 'col'=> "count(srl_id) as all_num,count(if(srl_add_time>{$last_month_1},true,null)) as all_num_1,count(if(srl_add_time>{$last_month_2},true,null)) as all_num_2,count(if(srl_add_time>{$last_month_3},true,null)) as all_num_3,srl_author" ) );
//echo $cls_data_rl->get_last_sql();
$list_srl = change_main_key( $list_srl, 'srl_author' );
echo '<table>';
echo '<tr><td>User</td><td>ALL</td><td>1 months</td><td>2 months</td><td>3 months</td></tr>';
$list_user = array_keys( $list_srl );
foreach( $list_user as $user_name )
{
	echo "<tr><td>{$user_name}</td><td>{$list_srl[$user_name]['all_num']}</td><td>{$list_srl[$user_name]['all_num_1']}</td><td>{$list_srl[$user_name]['all_num_2']}</td><td>{$list_srl[$user_name]['all_num_3']}</td></tr>";
}
echo '</table>';