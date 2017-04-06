<?php
require('include/common.inc.php');
session_start();
require('yz.php');
require_once( 'menu.php' );
if( 'add' == $action )
{
	echo '添加用户';
}
if( 'edit' == $action )
{
	$user_info = $cls_user->select_one_ex( array( 'where'=>"su_name='{$username}'" ) );
	//p_r( $user_info );
	echo '修改用户';
}
?>
<form action="user_action.php">
<input type="hidden" name="action" value="<?php echo $action; ?>">
用户名:<?php if( 'edit'==$action ){echo $username;}?><input type="<?php if( 'add'==$action ){echo 'text';}else{echo 'hidden';} ?>" name="username" value="<?php if( 'edit'==$action ){echo $username;}?>">
密码:<input type="password" name="password">
<label><input type="checkbox" name="can_sh_code" <?php if( $user_info['su_can_sh_code'] ){ echo 'checked'; } ?>  value=1>能审核代码</label>
<input type="submit" value="<?php echo $action; ?>">
</form>