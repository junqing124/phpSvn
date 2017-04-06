<?php

defined('IN_DCR') or exit('No permission.'); 

/**
 * 程序信息的类，比如获取一个编辑器 获取程序信息等全局工厂模式的类.
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0.3
 * @package class
 * @since 1.0.8
*/

class cls_app
{   
    /**
     * 获取一个编辑器 并且会输出这个编辑器 目前支持ckeditor kindeditor
     * @param string $editor_name 编辑器名
     * @param string $default_value 编辑内默认值
     * @param string $editor_width 编辑器宽 以px为单位
     * @param string $editor_height 编辑器高 以px为单位
     * @param string $daohang 菜单样式 1为简单 2为全部
     * @return true 返回true
     */
    public static function get_editor($editor_name, $default_value = '', $editor_width = '930', $editor_height = '500', $daohang = 1)
    {
        global $web_url, $web_editor;
        if( $web_editor == 'ckeditor' )
        {
            $editor_t = "<script src=\"" . $web_url . "/include/editor/$web_editor/ckeditor.js\" type=\"text/javascript\"></script>\r\n<script type=\"text/javascript\" src=\"" . $web_url . "/include/editor/$web_editor/ckfinder/ckfinder.js\"></script>\r\n<textarea id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea>\r\n<script type=\"text/javascript\">var editor = CKEDITOR.replace('" . $editor_name . "',{height:'" . $editor_height . "',width:'" . $editor_width . "'});CKFinder.SetupCKEditor(editor, \"" . $web_url . "/include/editor/$web_editor/ckfinder/\");</script>";
        }else if( $web_editor == 'kindeditor' )
        {
            //把宽度和高度换成cols和rows
            $cols = $editor_width / 7;
            $rows = $editor_height / 20;
            $editor_t = "<textarea cols='$cols' rows='$rows' id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea><script charset='utf-8' src=\"" . $web_url . "/include/editor/$web_editor/kindeditor-min.js\"></script><script>KE.show({id : '$editor_name'});</script>";
        }
        echo $editor_t;
    }
    
    /**
     * 获取一个编辑器 这个方法用于一个页面有2个编辑器这个是第二调用的.get_editor必须调用在这GetEditor_2前
     * @since 1.0.7
     * @param string $editor_name 编辑器名
     * @param string $default_value 编辑内默认值
     * @param string $editor_width 编辑器宽 以px为单位
     * @param string $editor_height 编辑器高 以px为单位
     * @param string $daohang 菜单样式 1为简单 2为全部
     * @return true 返回true
     */
    public static function get_editor_2($editor_name, $default_value = '', $editor_width = ' 930 ',  $editor_height = '500', $daohang = 1)
    {
        global $web_url, $web_editor;
        if($web_editor == 'ckeditor')
        {
            $editor_t = "<textarea id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea>\r\n<script type=\"text/javascript\">var editor = CKEDITOR.replace('" . $editor_name . "',{height:'" . $editor_height . "',width:'" . $editor_width . "'});CKFinder.SetupCKEditor(editor, \"" . $web_url . "/include/editor/$web_editor/ckfinder/\");</script>";
        }else if($web_editor == 'kindeditor')
        {
            //把宽度和高度换成cols和rows
            $cols = $editor_width / 7;
            $rows = $editor_height / 20;
            $editor_t = "<textarea cols='$cols' rows='$rows' id=\"" . $editor_name . "\" name=\"" . $editor_name . "\">" . $default_value . "</textarea><script> KindEditor.ready(function(K) {window.editor = K.create('#".$editor_name."');});</script>";

        }
        
        echo $editor_t;
    }
    
    /**
     * 获取一个数据库连接
     * @return DB 一个db实例
     */
    public static function get_db()
    {
        global $db_type,$db_host,$db_name,$db_pass,$db_table,$db_ut;
        
        return new cls_db($db_type,$db_host,$db_name,$db_pass,$db_table,$db_ut);
    }
    
    /**
     * 获取一个cls_data实例
     * @return Article 一个Article实例
     */    
    public static function get_data($table_name)
    {
        require_once(WEB_CLASS . '/class.data.php');
        $cls_data = new cls_data($table_name);
        
        return $cls_data;
    }
    
    /**
     * 获取一个Article实例 function GetArticle简写版
     * @return Article 一个Article实例
     */    
    public static function get_d($table_name)
    {
        return self::get_data($table_name);
    }
    
    /**
     * 函数GetNews，获取一个News实例
     * @return News 一个News实例
     */    
    public static function get_news()
    {
        require_once(WEB_CLASS . '/class.news.php');
        $cls_news = new cls_news();
        return $cls_news;
    }
    
    /**
     * 函数GetProduct，获取一个Product实例
     * @return Product 一个Product实例
     */    
    public static function get_product()
    {
        require_once(WEB_CLASS . '/class.product.php');
        $cls_pro = new cls_product();
        
        return $cls_pro;
    }
    
    /**
     * @since 1.0.9
     * 写一个日志..... 如果不指定log_dir的话，则日志会产生于include/log
     *
     * @param string $string 日志内容
     * @param string $level_1mode  日志级别 默认为ERROR,级别分为DEBUG ERROR INFO NOTICE
     * @param string $log_dir  日志存放目录，默认为当前目录
     * @param string $log_file_name  日志文件名
     *
     * @return true
     *
     */
    public static function log($message, $level_mode = 'ERROR', $log_dir = '', $log_file_name = '')
    {
        $date_input   = date('Y-m-d H:i:s');

        if( !empty($log_dir) )
        {
            $log_dir = $log_dir;
        } else
        {
            $log_dir = WEB_LOG . '/common/';
        }
        if( ! file_exists( $log_dir ) )
        {
            @mkdir( $log_dir );
        }
        if( $log_file_name )
        {
            $file_name    = $log_dir . '/' . $log_file_name;
        }
        else
        {
            $file_name    = $log_dir . 'log_' . date('Y_m_d') . '.txt';
        }
        require_once( WEB_CLASS . '/class.file.php' );
        $cls_log_file = new cls_file($file_name);
        $logo_content = '[' . $date_input . ']--' . strtoupper($level_mode) . '--' . $message . "\r\n";
        $cls_log_file-> set_text( $logo_content );
        $cls_log_file-> write( false, 'a' );
        //unset($cls_log_file);
    }
    
    /**
     * 函数get_template，获取一个模板实
     * @param string $template_file_name 模板文件名
     * @return cls_template 返回cls_template实例
     */    
    public static function get_template( $template_file_name )
    {
        global $web_cache_time, $tpl_dir;
        require_once(WEB_CLASS . '/class.template.php');
        $cls_tpl = new cls_template( $template_file_name . '.htm', $web_cache_time );
        
        return $cls_tpl;
    }

    /**
     * 函数tpl，解析一个模板并显示出来
     * @param string $template_file_name 模板文件名,后面不要带.htm
     * @return 无返回值
     */   
    public static function tpl( $template_file_name )
    {
        $cls_tpl = cls_app:: get_template( $template_file_name );
        $cls_tpl->display();
    } 
	  
    public static function get_permissions( )
    {
        require_once(WEB_CLASS . '/class.permissions.php');
        $cls = new cls_permissions( );
        
        return $cls;
    }
	  
    public static function get_cache( $cache_file_name = '', $cache_limit_time = 0, $cache_dir = '' )
    {
        require_once(WEB_CLASS . '/class.cache.php');
        $cls = new cls_cache( $cache_file_name, $cache_limit_time, $cache_dir );
        
        return $cls;
    }
	  
    public static function get_user( $username = '', $password = '' )
    {
        require_once(WEB_CLASS . '/class.user.php');
        $cls = new cls_user( $username, $password );
        
        return $cls;
    }
	  
    public static function get_cls( $cls_name )
    {
        require_once(WEB_CLASS . '/class.' . $cls_name . '.php');
		$class_name = "cls_{$cls_name}";
        $cls = new $class_name;
        
        return $cls;
    }
	  
    public static function get_platform( $platform_name )
    {
        require_once(WEB_CLASS . '/platform/class.' . $platform_name . '.php');
		$class_name = "cls_{$platform_name}";
        $cls = new $class_name;
        
        return $cls;
    }

    /*
     * @param boolean $use_pconnect 是不是用长连接，因为发现有些地方会掉mysql，所以改这个
     * @return $db 连接好的v2 db
     */
	public static function get_v2_db( $use_pconnect = 0 )
	{
		//global $db;
		//return $db;
		global $db_v2_type, $db_v2_host, $db_v2_name, $db_v2_pass, $db_v2_table, $db_v2_ut;
		
		$db_v2 = new cls_db($db_v2_type, $db_v2_host, $db_v2_name, $db_v2_pass, $db_v2_table, $db_v2_ut, $use_pconnect);
        unset( $db_v2_type, $db_v2_host, $db_v2_name, $db_v2_pass, $db_v2_table, $db_v2_ut );
		return $db_v2;
	}


    /*
     * @param boolean $use_pconnect 是不是用长连接，因为发现有些地方会掉mysql，所以改这个
     * @return $db 连接好的v2 db
     */
    public static function get_slave_db( $rand_slave_id = 1, $slave_id = 0 )
    {
        global $db_slave_list;
        if( $rand_slave_id )
        {
            $slave_info = $db_slave_list[array_rand( $db_slave_list, 1 )];
        }
        if( $slave_id )
        {
            $slave_info = $db_slave_list[$slave_id];
        }
        $db_type = $slave_info['db_type'];
        $db_host = $slave_info['db_host'];
        $db_name = $slave_info['db_name'];
        $db_pass = $slave_info['db_pass'];
        $db_table = $slave_info['db_table'];
        $db_ut = $slave_info['db_ut'];
        $db = new cls_db( $db_type, $db_host, $db_name, $db_pass, $db_table, $db_ut );
        unset( $db_type, $db_host, $db_name, $db_pass, $db_table, $db_ut );
        return $db;
    }

    /*
     * @param boolean $use_pconnect 是不是用长连接，因为发现有些地方会掉mysql，所以改这个
     * @return $data 连接好的v2 data
     */
	public static function get_v2_data( $use_pconnect = 0 )
	{
		$db_v2 = self:: get_v2_db( $use_pconnect );               //对V2数据库建立连接
		$cls = self:: get_d( '@#@warehouse_type' );//参数无效
		$cls->set_db( $db_v2 );
		
		return $cls;
	}
	
	  
    public static function get_track( $track )
    {
        require_once(WEB_CLASS . '/track/class.' . $track . '.php');
		$class_name = "cls_{$track}";
        $cls = new $class_name( );
        return $cls;
    }
	
	public static function get_config( $config_name )
	{
		$cls_config = new cls_config();
		return $cls_config->get_config_value( $config_name );
	}
	public static function get_c( $config_name )
	{
		return self::get_config( $config_name );
	}
    
}

?>