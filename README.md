# phpSvn
first version<br>
本系统用php管理svn代码审核及代码发布，目前只适用linux：<br>
要把本系统放到已经配置好svn的目录中<br>

1、先配置include/config.db.php<br>
2、运行install<br>
3、在计划任务里加上1小时一次执行的任务：php 根目录/crontab/get_revision_list.php (notice:get_revision_list.php里要改require路径)<br>
4、默认用户名和密码是admin admin<br>

错误排除

1、如果更新不成功，则查看下/var/log/http/下的error_log
2、如果报svn: E000013: Can't create temporary file from template '/home/www/.svn/tmp/svn-XXXXXX': Permission denied，则可以在计划任务中加:*/2 * * * * chown apache:apache /home/www/.svn/tmp(相关路径自动更改)
