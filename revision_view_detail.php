<?php
require('include/common.inc.php');
session_start();
require('yz.php');
require_once( 'menu.php' );
//echo $type;
//echo $path;
if( 'Modify' == $type )
{
	//echo 'a';
	$command = "svn log -l 2 --xml {$svn_root}/{$path} --username {$svn_user} --password {$svn_password} --no-auth-cache";
	$version_min = $log_version - 1000;
	$version_min = $version_min < 1 ? 1 : $version_min;
	$command = "svn log -r{$log_version}:{$version_min} -l 2 {$svn_root}/{$path} --username {$svn_user} --password {$svn_password} --no-auth-cache";
	$out = shell_exec($command);
	preg_match_all( '/r(.+) | /U', $out, $arr_result );
	$start_version = $arr_result[1][13] ? $arr_result[1][13] : $arr_result[1][12];
	$start_version = $start_version ? $start_version : $arr_result[1][14];
	$start_version = intval( $start_version );
	
	$command = "svn diff -r{$start_version}:{$arr_result[1][0]} {$svn_root}/{$path} --username {$svn_user} --password {$svn_password} --no-auth-cache";
	$out = shell_exec($command);
	$content = htmlentities( $out );
	echo '<pre>';
	echo $content;

}else if( 'Add' == $type )
{
	$out = file_get_contents( "{$svn_root}/{$path}" );
	$content = htmlentities( $out );
	echo '<pre>';
	echo $content;
}