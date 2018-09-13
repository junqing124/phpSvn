<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<a href="index.php">Revision List</a>&nbsp;
<?php  if( 'admin' == $admin_u ){ ?><a href="user_list.php">Users</a>&nbsp;<?php } ?>
<?php  if( 'admin' == $admin_u ){ ?><a href="setting.php">Config</a>&nbsp;<?php } ?>
<?php  if( 'admin' == $admin_u ){ ?><a href="statistics.php">Statistics</a>&nbsp;<?php } ?>
<a href="logout.php">Logout</a>&nbsp;
<a href="svn_update.php">Update SVN</a>&nbsp;
<hr>