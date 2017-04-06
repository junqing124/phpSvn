<?php
require('../include/common.inc.php');
$val = $db->execute_none_query("CREATE TABLE `svn_system_status` (
  `sss_id` INT NOT NULL AUTO_INCREMENT,
  `sss_last_update` INT(11) NULL,
  `sss_last_version` INT(10) NULL COMMENT '最后更新的版本',
  PRIMARY KEY (`sss_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
");
$db->execute_none_query( 'insert into svn_system_status(sss_id) value(1)' );

if( $val )
{
	echo 'svn_system_status create ok!';
}else
{
	echo '<span style="color:red">';
	echo 'svn_system_status create faild!';
	echo $db->get_db_error();
	echo '</span>';
}
echo '<br>';
$val = $db->execute_none_query("CREATE TABLE `svn_revision_list` (
  `srl_id` INT NOT NULL AUTO_INCREMENT,
  `srl_revision` INT(8) NULL,
  `srl_author` VARCHAR(100) NULL,
  `srl_commit_time` INT(10) NULL,
  `srl_msg` VARCHAR(300) NULL,
  `srl_add_time` INT(11) NULL,
  `srl_status` TINYINT(1) NULL DEFAULT 1 COMMENT '1 未审 2已审',
  `srl_sh_time` TINYINT NULL COMMENT '审核次数',
  `srl_sh_user` VARCHAR(100) NULL COMMENT '审核人',
  `srl_sh_faild_msg` VARCHAR(200) NULL,
  PRIMARY KEY (`srl_id`),
  UNIQUE INDEX `uidx_revision` (`srl_revision` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
");

if( $val )
{
	echo 'svn_revision_list create ok!';
}else
{
	echo '<span style="color:red">';
	echo 'svn_revision_list create faild!';
	echo $db->get_db_error();
	echo '</span>';
}
echo '<br>';
$val = $db->execute_none_query("CREATE TABLE `svn_user` (
  `su_id` INT NOT NULL AUTO_INCREMENT,
  `su_name` VARCHAR(45) NULL,
  `su_password` VARCHAR(45) NULL,
  `su_add_time` INT(10) NULL,
  `su_can_sh_code` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`su_id`),
  UNIQUE INDEX `uidx_name` (`su_name` ASC));
");
$db->execute_none_query("insert into svn_user(su_name,su_password) values('admin','" . jiami('admin') . "')");
if( $val )
{
	echo 'svn_user create ok!';
}else
{
	echo '<span style="color:red">';
	echo 'svn_revision_list create faild!';
	echo $db->get_db_error();
	echo '</span>';
}
echo '<br>';

$cls_data_rl = new cls_data('svn_revision_list');
//第一次先解析
$command = "/usr/bin/cd /home/www";
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