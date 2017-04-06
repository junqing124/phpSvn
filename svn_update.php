<?php
require('include/common.inc.php');
session_start();
require('yz.php');
require_once( 'menu.php' );

//chown('/home/www/.svn/tmp', 'apache');
$command = "chown -R apache:apache /home/www/.svn/tmp";
$out = shell_exec($command);

$command = "svn cleanup /home/www --username huanghuanjun --password hy8420123 --no-auth-cache";
$out = shell_exec($command);
//p_r( $out );
//echo '<hr>';

$command = "svn update /home/www --username huanghuanjun --password hy8420123 --no-auth-cache";
$out = shell_exec($command);
p_r( $out );
echo '<hr>';

$out = get_html( 'http://192.168.6.143/svn_code/svn_update.php' );
p_r( $out );
echo '<hr>';

$out = get_html( 'http://192.168.6.122/svn_code/svn_update.php' );
p_r( $out );
echo '<hr>';
$out = get_html( 'http://192.168.6.146/svn_code/svn_update.php' );
p_r( $out );
echo '<hr>';