<?php
require('/home/www/svn_code/include/common.inc.php');

$command = "svn log -l 1 --xml {$svn_root} --username {$svn_user} --password {$svn_password} --no-auth-cache";
$out = shell_exec($command);
$out_list = simplexml_load_string( $out );
$result_list = $out_list->logentry;
if( $result_list )
{
	$cls_logfx  = new cls_logfx($result_list);
	$list = $cls_logfx->formate_version_log();
	$last_version = $list[0]['revision'];
	if( $last_version )
	{
		$cls_data_status = new cls_data('svn_system_status');
		$status_info = $cls_data_status->select_one_ex( array( 'where'=>'sss_id=1' ) );
		$last_update_version = $status_info['sss_last_version'];
		$space = $last_version - $last_update_version + 2;
		
		$command = "svn log -l {$space} --xml {$svn_root} --username {$svn_user} --password {$svn_password} --no-auth-cache";
		$out = shell_exec($command);
		$out_list = simplexml_load_string( $out );
		$result_list = $out_list->logentry;
		if( $result_list )
		{
			$cls_data_rl = new cls_data('svn_revision_list');
			$cls_logfx  = new cls_logfx($result_list);
			$list = $cls_logfx->formate_version_log();
			$max_revision = 0;
			foreach( $list as $info )
			{
				$cls_data_rl->insert_ex( array( 'srl_revision'=>$info['revision'], 'srl_author'=>$info['author'], 'srl_commit_time'=>strtotime( $info['date'] ), 'srl_msg'=>$info['msg'], 'srl_add_time'=>time(), ) );
				$info['revision'] > $max_revision ? $max_revision=$info['revision'] : 0;
			}

			$cls_data_rl = new cls_data('svn_system_status');
			$cls_data_rl->update_one( array( 'sss_last_update'=> time(), 'sss_last_version'=> $max_revision ), "sss_id=1" );
			echo '完成';
		}
	}
}