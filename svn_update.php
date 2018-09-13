<?php
require('include/common.inc.php');
session_start();
require('yz.php');
require_once( 'menu.php' );

#$command = "sh o";
#$out = shell_exec($command);
#p_r( $out );
#exit;
#exit('closed');
//chown('/home/www/.svn/tmp', 'apache');
$command = "chown -R apache:apache /home/www/.svn/tmp";
$out = shell_exec($command);

$command = "cd /home/www;svn cleanup;";
$out = shell_exec($command);

$command = "svn cleanup /home/www --username huanghuanjun --password hy8420123 --no-auth-cache";
$out = shell_exec($command);
//p_r( $out );
//echo '<hr>';

$command = "svn update /home/www --username huanghuanjun --password hy8420123 --no-auth-cache";
$out = shell_exec($command);
p_r( $out );
echo '<hr>';

$out = get_html( 'http://10.10.30.121:81/svn_code/svn_update.php' );
#echo '143';
p_r( $out );
echo '<hr>';

$out = get_html( 'http://10.10.30.120/svn_code/svn_update.php' );
#echo '122';
p_r( $out );
echo '<hr>';

$out = get_html( 'http://10.10.30.122/svn_code/svn_update.php' );
#echo '146';
p_r( $out );
echo '<hr>';

$out = get_html( 'http://10.10.30.123/svn_code/svn_update.php' );
//echo '168';
p_r( $out );
echo '<hr>';

$out = get_html( 'http://10.10.30.124/svn_code/svn_update.php' );
//echo '170';
p_r( $out );
echo '<hr>';

$out = get_html( 'http://192.168.6.183/svn_code/svn_update.php' );
//echo '170';
p_r( $out );
echo '<hr>';

$out = get_html( 'http://192.168.6.190/svn_code/svn_update.php' );
//echo '170';
p_r( $out );
echo '<hr>';
