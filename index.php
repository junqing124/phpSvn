<?php
require('include/common.inc.php');
session_start();
require('yz.php');
require_once( 'menu.php' );
if( ! $admin_can_sh_code )
{
	exit;
}
echo "<a href='?type=1'>未审核</a>&nbsp;&nbsp;";
echo "<a href='?type=2'>已审核</a>&nbsp;&nbsp;";
echo "<a href='?type=3'>审核不通过</a>";
?>
<script>
function open_all_a()
{
	$("a[name='a_link']").each(function( element )
	{
		this.click();
    })
}
</script>
<?php

$where_option = array();
$type = isset( $type ) ? $type : 1;
if( $type )
{
	array_push( $where_option, "srl_status={$type}" );
}
$list_count = 50;
$cls_data_rl = new cls_data( 'svn_revision_list' );
$page = isset ( $page ) ? ( int ) $page : 1;
$start = ($page - 1) * $list_count;
$list = $cls_data_rl-> select_ex( array('order'=>'srl_revision desc', 'limit'=>"$start,$list_count",'where'=>$where_option) );

if( 1 == $type )
{
	echo "<br>未审核列表";
}else if( 2== $type )
{
	echo "<br>已审核列表";	
}else if( 3== $type )
{
	echo "<br>审核不通过";	
}
echo ' <a href="javascript:open_all_a();void(0);">打开所有链接</a><table>';
echo '<tr><td>Version</td><td>Author</td><td>Commit Time</td><td>Message</td><td>Collect Time</td><td>Status</td><td>SH users</td><td>Options</td></tr>';
foreach( $list as $info )
{
	$status_str = '';
	switch( $info['srl_status'] )
	{
		case 1:
			$status_str = '未审核';
			break;
		case 2:
			$status_str = '已审核';
			break;
		case 3:
			$status_str = '审核不通过';
			break;
	}
	$date_commit_date = date( 'Y-m-d H:i:s', $info['srl_commit_time'] );
	$date_add_date = date( 'Y-m-d H:i:s', $info['srl_add_time'] );
	$faild_msg = $info['srl_sh_faild_msg'] ? "[{$info['srl_sh_faild_msg']}]" : '';
	echo "<tr><td>{$info['srl_revision']}</td><td>{$info['srl_author']}</td><td>{$date_commit_date}</td><td>{$info['srl_msg']}</td><td>{$date_add_date}</td><td>{$status_str}</td><td>{$info['srl_sh_user']}{$faild_msg}</td><td><a name='a_link' target='_blank' href='revision_view.php?log_version={$info['srl_revision']}'>View</a></td></tr>";
}
echo '</table>';

include WEB_CLASS . "/class.page.php";
$page_list = $cls_data_rl->select_ex ( array('where'=>$where_option) );
$rs_total = count($page_list);
$total_page = ceil ( $rs_total / $list_count );
$cls_page = new cls_page ( $page, $total_page, '{cur=当前页} {index=首页} {pre=上一页} {next=下一页} {end=最后页} {input_box}' );
$fenye = $cls_page->show_page ();
echo "共" . $rs_total . "条结果。" . $fenye;