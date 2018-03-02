<?php
require('../include/common.inc.php');

$create_sql_arr  = array( 
						'svn_system_status'=> array( 'create_sql'=> ' 	CREATE TABLE `svn_system_status` (
																		 `sss_id` int(11) NOT NULL AUTO_INCREMENT,
																		 `sss_last_update` int(11) DEFAULT NULL,
																		 `sss_last_version` int(10) DEFAULT NULL COMMENT \'最后更新的版本\',
																		 PRIMARY KEY (`sss_id`)
																		) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8',
														'addon_sql'=> 'insert into svn_system_status(sss_id) value(1);',
																	),
						'svn_revision_list'=> array( 'create_sql'=> 'CREATE TABLE `svn_revision_list` (
													 `srl_id` int(11) NOT NULL AUTO_INCREMENT,
													 `srl_revision` int(8) DEFAULT NULL,
													 `srl_author` varchar(100) DEFAULT NULL,
													 `srl_commit_time` int(10) DEFAULT NULL,
													 `srl_msg` varchar(300) DEFAULT NULL,
													 `srl_add_time` int(11) DEFAULT NULL,
													 `srl_status` tinyint(1) DEFAULT 1 COMMENT \'1 未审 2已审\',
													 `srl_sh_time` tinyint(4) DEFAULT NULL COMMENT \'审核次数\',
													 `srl_sh_user` varchar(100) DEFAULT NULL COMMENT \'审核人\',
													 `srl_sh_faild_msg` varchar(200) DEFAULT NULL,
													 PRIMARY KEY (`srl_id`),
													 UNIQUE KEY `uidx_revision` (`srl_revision`)
													) ENGINE=InnoDB AUTO_INCREMENT=60072 DEFAULT CHARSET=utf8',
														'addon_sql'=>'' ),
						'svn_user'=> array( 'create_sql'=> 'CREATE TABLE `svn_user` (
															  `su_id` INT NOT NULL AUTO_INCREMENT,
															  `su_name` VARCHAR(45) NULL,
															  `su_password` VARCHAR(45) NULL,
															  `su_add_time` INT(10) NULL,
															  `su_can_sh_code` TINYINT(1) NULL DEFAULT 0,
															  PRIMARY KEY (`su_id`),
															  UNIQUE INDEX `uidx_name` (`su_name` ASC));',
														'addon_sql'=>"insert into svn_user(su_name,su_password) values('admin','" . jiami('admin') . "')" ),
						'svn_config'=> array( 'create_sql'=> 'CREATE TABLE `svn_config` (
															 `config_id` tinyint(1) NOT NULL AUTO_INCREMENT,
															 `config_name` varchar(50) NOT NULL,
															 `config_value` varchar(1000) NOT NULL,
															 `config_add_time` int(11) NOT NULL,
															 PRIMARY KEY (`config_id`),
															 UNIQUE KEY `uidx_name` (`config_name`)
															) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8',
														'addon_sql'=>'' ) );
														

foreach( $create_sql_arr as $db_table_name=> $table_info )
{
	$val = $db->execute_none_query( $table_info['create_sql'] );

	if( $val )
	{
		echo $db_table_name . ' create ok!';
		if( $table_info['addon_sql'] )
		{
			$db->execute_none_query( $table_info['addon_sql'] );			
		}
	}else
	{
		echo '<span style="color:red">';
		echo $db_table_name . ' create faild!';
		echo $db->get_db_error();
		echo '</span>';
	}
	echo '<br>';
}

$cls_data_rl = new cls_data('svn_revision_list');
//第一次先解析
$command = "/usr/bin/cd {$svn_root}";
$command = "svn log --xml {$svn_root} --username {$svn_user} --password {$svn_password} --no-auth-cache";
$out = shell_exec($command);
$out_list = simplexml_load_string( $out );
$result_list = $out_list->logentry;
if( $result_list )
{
	$cls_logfx  = new cls_logfx($result_list);
	$list = $cls_logfx->formate_version_log();
	$max_revision = 0;
	foreach( $list as $info )
	{
		//p_r( $info );
		$cls_data_rl->insert_ex( array( 'srl_revision'=>$info['revision'], 'srl_author'=>$info['author'], 'srl_commit_time'=>strtotime( $info['date'] ), 'srl_msg'=>$info['msg'], 'srl_add_time'=>time(), ) );
		$info['revision'] > $max_revision ? $max_revision=$info['revision'] : 0;
	}
}
echo '完成对历史列表收集<br>';

$cls_data_rl = new cls_data('svn_system_status');
$cls_data_rl->update_one( array( 'sss_last_update'=> time(), 'sss_last_version'=> $max_revision ), "sss_id=1" );
echo '完成安装';