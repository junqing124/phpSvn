<?php

defined('IN_DCR') or exit('No permission.'); 

/**
 * 数据库处理
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

class cls_db
{
    private $pdo;
    
    private $db_type;
    private $host;
    private $name;
    private $pass;
    private $table;
    private $ut;
    private $use_pconnect;
    private $conn;
    
    private $result;
    private $rs;    
    
    private $str_error; //错误信息
    
    /**
     * 构造函数
      * @param string $db_type 数据库类型
      * @param string $host 数据库地址
     * @param string $name 数据库用户名
     * @param string $pass 数据库密码
     * @param string $table 数据库名
     * @param string $ut 数据库编码
     * @param boolean $use_pconnect 是否使用长连接
     * @return resource 成功返回一个连接的resource
     */
    function __construct( $db_type, $db_host, $db_name, $db_pass, $db_table, $db_ut, $use_pconnect = 0 )
    {
        $this->db_type = $db_type;
        $this->host = $db_host;
        $this->name = $db_name;
        $this->pass = $db_pass;
        $this->table = $db_table;
        $this->ut = $db_ut;
        $this->use_pconnect = $use_pconnect;
        if( ! $this->conn )
        {
            $this->connect();
        }
    }
    
    /**
     * 函数connect 连接数据库的函数 这个方法为类自动调用，不用我们手动调用
     * @return null
     */
    function connect()
    {
        if($this->db_type == '1')
        {
            try
            {
                $db_path = WEB_DATA . '/' . $this->host;
                $this->pdo = new PDO("sqlite:" . $db_path);
            }catch( PDOException $e ) 
            {
                $this->show_error( '连接失败: ' . $e->getMessage() );
            }
        }
        else if($this->db_type == '2')
        {
            if(!$this->conn)
            {
            	if( $this->use_pconnect )
            	{
            		$func_name = 'mysqli_pconnect';
            	}
            	else
            	{
            		$func_name = 'mysqli_connect';
            	}
            	
            	$i=0;
            	do
            	{
            		$i++;
            		$func_name_connect = $func_name( $this->host, $this->name, $this->pass );
            		sleep(i*50);
            	}while($i<5 && !$func_name_connect);
            	if(!$func_name_connect)
            	{
            		die('连接数据库失败，请检查您的数据库配置');
            	}
            	$this->conn =$func_name_connect;
            }else
            {
            	return false;
            }
            mysqli_select_db( $this->conn, $this->table ) or $this->show_error( '选择数据库失败,请检查数据库[' . $this->table . ']是否创建');
            mysqli_query( $this->conn, "SET NAMES '$this->ut'" );
            mysqli_query( $this->conn, "SET character_set_client=binary" );
        }
    }
    

    /**
     * 返回数据连接的resource
     * @since 1.0.9
     * @return resource
     */
    public function get_conn()
    {
        return $this->conn;
    }

    /**
     * 设置数据库错误信息 这个方法不用外面调用 程序自己调用 version>=1.0.6
     * @param string $error 错误信息
     * @return true
     */
    private function set_db_error( $error )
    {
        $this->str_error = $error;
    }
    
    /**
     * 获取数据库错误信息 version>=1.0.6
     * @return string 错误信息
     */
     function get_db_error()
     {
         
         return $this->str_error;
     }
     
    /**
     * 全局处理sql语句,DB类的每个sql语句执行前要通过这个来处理下
     * @param string $sql 要处理的sql语句
     * @return string 返回处理后的sql语句
     */
    function option($sql)
    {
        global $db_tablepre;
	$db_tablepre='kd_';
        $sql = str_replace( '{tablepre}', $db_tablepre, $sql );//替换表名    
        $sql = str_replace( '@#@', $db_tablepre, $sql );//替换表名        
        $sql = $this->safe_sql( $sql );//安全处理sql
        
        return $sql;
    }
    
    /**
     * 执行$sql
     * <code>
     * <?php
     * cls_db->execute("select * from {tablepre}news");
     * ?>
      * </code>
     * @param string $sql 要执行的sql语句
     * @param int $result_type 返回记录集的类型 默认为MYSQL_ASSOC
     * @return array 返回执行结果的数组
     */
    function execute($sql, $result_type = MYSQLI_ASSOC)
    {
        $this->ping();
        $sql = $this->option($sql);
        if( !empty($sql) )
        {
            unset($this->result);
            if( $this->db_type == '1' )
            {
                $arr_t = $this->pdo->query($sql);
                if( $arr_t )
                {
                    $this->result = $arr_t->fetchAll();
                }else
                {
                    $error_info = $this->pdo->errorInfo();
                    $this->set_db_error( $error_info[2] );
                    $this->show_error( $error_info[2], $sql );
                }
                //$this->result=$arr_t;
                unset( $arr_t );
            }else if( $this->db_type == '2' )
            {
                if( $arr_t = mysqli_query( $this->conn, $sql  ) )
                {
                }else
                {
                    $this->set_db_error( mysqli_error($this->conn) );
                    $this->show_error( mysqli_error($this->conn), $sql, mysqli_errno( $this->conn ) );
                } 
                $arr = array();
                if($arr_t)
                {
                    while( $row = mysqli_fetch_array($arr_t, $result_type) )
                    {
                        $arr[] = $row;
                    }
                }
                $this->result = $arr;
                unset($arr_t);
                unset($arr);
            }
            
            return $this->result;
        }else
        {
            
            return false;
        }
    }
    
    /**
     * 函数execute_none_query,执行一个不要返回结果的$sql 如update insert
     * @param string $sql 要执行的sql语句
     * @return boolean 成功返回true 失败返回false;
     */
    function execute_none_query($sql)
    {
        $this->ping();
        $sql = $this->option($sql);
        if( ! empty( $sql ) )
        {
            if( $this->db_type == '1' )
            {
                $this->pdo->exec( $sql );
                if( $this->pdo->errorCode() == '00000' )
                {                    
                    return true;
                }else
                {
                    $error_info = $this->pdo->errorInfo();
                    $this->set_db_error( $error_info[2] );
                    $this->show_error( $error_info[2], $sql );
                    
                    return false;
                }
                
                return $err_no == 0;
            }else if( $this->db_type == '2' )
            {
                if( mysqli_query( $this->conn, $sql  ) )
                {
                    return true;
                }else
                {
                    $this->set_db_error( mysqli_error( $this-> conn ) );
                    $this->show_error( mysqli_error( $this->conn ), $sql, mysqli_errno( $this->conn ));
                }
            }
        }else
        {
            
            return false;
        }
    }
    
    /**
     * 执行返回一行结果
     * @param string $sql 要执行的sql语句
     * @param int $result_type 返回记录集的类型 默认为MYSQL_ASSOC
     * @return boolean 成功返回记录集 失败返回false
     */
    function get_one($sql, $result_type = MYSQL_ASSOC)
	{
        if( ! empty( $sql ) )
        {
            if(!preg_match("/limit$/i",$sql))
            {
                $sql = preg_replace("/;$/",'', trim($sql)) . " limit 0,1;";
            }
        }
        $this->execute($sql, $result_type);
        
        return current($this->result);
    }
    
    /**
     * 用来返回记录集 同时滚动到下一条
     * @return resource|boolean 成功则返回记录 失败返回false;
     */
    function next()
    {
        unset( $this->rs );
        if( !$this->result )
        {
            return false;
        }
        $rs = current( $this->result );
        if( is_array( $rs ) && count( $rs ) > 0 )
        {
            next( $this->result );
            $this->rs = $rs;
            
            return true;
        }else
        {
            
            return false;
        }
    }
    
    /**
     * 返回记录集中指定字段的值
     * @return string|boolean 成功返回指定字段值 失败返回false;
     */
    function f( $name )
    {
        if( is_array($this->rs) )
        {
            
            return $this->rs[$name];
        }else{
            
            return false;
        }
    }
    
    /**
     * 用来返回记录集的数组形式
     * @return array 成功数组 失败返回false;
     */
    function get_array()
    {
        
        return $this->result;
    }
    
    /**
     * 函数GetRsNum,用来返回记录集的记录数
     * @return int 成功记录数 失败返回0
     */
    function get_rs_num()
    {
        if(!$this->result)
        {
            
            return 0;
        }else{
            
            return count( $this->result );
        }
    }
    
    /**
     * 返回当前记录集的列字段名 比如select a,b from c 则返回数组array('a','b')
     * @return array 成功记录集的列字段名的array 失败返回0;
     */
    function get_col_array()
    {
        $arr_t = $this->result;
        foreach( $arr_t as $key=> $value )
        {
            array_push($arr_t, $key);    
            
        }
        
        return $arr_t;
    }
    
    /**
     * 获取指定表指定字段的值
     * @param string $table_name 表名
     * @param string $field_name 字段名
     * @param string $where_sql 条件
     * @return string 成功返回字段值 失败返回false;
     */
    function get_field_value( $table_name, $field_name, $where_sql = '' )
    {
        if( strlen( $where_sql ) > 0 )
        {
            $sql = "select {$field_name} from {$table_name} where {$where_sql}";
        }else
        {
            $sql = "select {$field_name} from {$table_name}";
        }
        $arr_t = $this->get_one( $sql, MYSQL_NUM );
       
        return $arr_t[0];
    }
    
    /**
     * 获取是不是有这个记录
     * @return boolean 成功返回true 失败返回false;
     */
    function has_rs($sql)
    {
        $t_arr = $this->get_one($sql, MYSQL_NUM);
        
        return is_array($t_arr);
    }
    
    /**
     * 取得上一步INSERT操作产生的insertid
     * @return int 成功则取得上一步INSERT操作产生的insertid 失败返回false;
     */
    function get_insert_id()
    {
        if($this->db_type == '1')
        {
            
            return $this->pdo->lastInsertId();
        }else if($this->db_type == '2')
        {
           // p_r( $this->conn );
            return @mysqli_insert_id( $this->conn );
        }
    }

    function get_affected_rows()
    {
        return mysqli_affected_rows( $this->conn );

    }
    
    /**
     * 返回一个表的所有列字段名
     * @param string $tableName 表名
     * @return array 成功返回所有列的列字段名的array 失败返回false;
     */
    function get_table_col($table_name)
    {
        $sql = "show columns from $table_name";
        $result = mysqli_query($sql, $this->conn);
        $t_arr = array();
        while( $rs = mysqli_fetch_array($result) )
        {
            $t_arr[] = $rs['Field'];
        }
        
        return $t_arr;
    }

    function ping()
    {
        if( ! mysqli_ping( $this->conn ) )
        {
            $this->close_db();
            $this->connect();
        }
        return true;
    }
    /**
     * 返回当前数据库的版本
     * @return string 成功则返回数据库版本，失败返回false
     */
    function get_version()
    {
        $version = mysqli_query("SELECT VERSION();", $this->conn);
        $row = mysqli_fetch_array($version);
        $mysql_versions = explode( '.', trim($row[0]) );
        $mysql_version = $mysql_versions[0] . "." . $mysql_versions[1];
        
        return $mysql_version;
    }
    
    /**
     * 关闭当前数据库连接
     * @return boolean 返回true
     */
    function close_db()
    {
        @mysqli_close($this->conn);
        unset($this->conn);
    }   
   
    /**
     * 关闭当前数据库连接
     * @return boolean 返回true
     */
    function __destruct()
    {
        $this->close_db();
    } 

    /**
     * 函数show_error,显示数据库错误信息
     * @return true
     */
    function show_error( $msg, $sql = '', $error_id = 0 )
    {
        //$ignore_error_id 忽略不显示的错误
        global $ignore_error_id;
        if( $ignore_error_id && in_array( $error_id, $ignore_error_id ) )
        {
            return false;
        }
        /*if( $error_id && ! in_array( $error_id, array( 1064, 1062 ) ) )
        {
            $cls_log = new cls_log();
            $cls_log->set_collection( 'log_error_mysql' );
            $cls_log->add_log( array(
                'lem_add_time'=> time(),
                'lem_sql'=> $sql,
                'lem_msg'=> $msg,
                'lem_error_id'=> $error_id,
            ) );
        }*/
        
        $msg_str = "<div style='width:70%; margin:0 auto 10px auto;background:#f5e2e2;border:1px red solid; font-size:12px;'><div style='font-size:12px;padding:5px; font-weight:bold; color:#FFF;color:red'>DCRCMS DB Error</div>";
        $msg_str .= "<div style='border:1px #f79797 solid;background:#fcf2f2; width:95%; margin:0 auto; margin-bottom:10px;padding:5px;'><ul style='list-style:none;color:green;line-height:22px;'><li><span style='color:red;'>错误页面:</span>".$this->get_cur_script()."</li>";
        if( !empty($sql) )
        {
            $msg_str .= "<li><span style='color:red;'>错误语句:</span>$sql</li>";
        }
        $msg_str .= "<li><span style='color:red;'>提示信息:</span>$msg</li>";
        $msg_str .= "</ul></div></div>";
        
        echo $msg_str;
    }
    
    /**
     * 获得当前的脚本文件名  来自DEDE修改
     * @return string 脚本文件名
     */
    function get_cur_script()
    {
        if(!empty($_SERVER["REQUEST_URI"]))
        {
            $script_name = $_SERVER["REQUEST_URI"];
            $nowurl = $script_name;
        }
        else
        {
            $script_name = $_SERVER["PHP_SELF"];
            if( empty($_SERVER["QUERY_STRING"]) )
            {
                $nowurl = $script_name;
            }
            else
			{
                $nowurl = $script_name . "?" . $_SERVER["QUERY_STRING"];
            }
        }
        
        return $nowurl;
    }
    
    /**
     * 语句过滤程序
     * @param string $db_string 要处理的sql语句
     * @param string $querytype 要处理的sql语句的类型
     * @return string 返回一个sql语句安全处理后的sql
     */
    function safe_sql( $db_string, $querytype = 'select' )
    {
        //var_dump($db_string);
        //完整的SQL检查
        if( empty( $db_string ) )
		{
            return false;
        }
        while ( true )
		{            
            $pos = strpos($db_string, '\'', $pos + 1);
            if ($pos === false)
            {
                break;
            }
            $clean .= substr($db_string, $old_pos, $pos - $old_pos);
            while ( true )
            {
                $pos1 = strpos($db_string, '\'', $pos + 1);
                $pos2 = strpos($db_string, '\\', $pos + 1);
                if ($pos1 === false)
                {
                    break;
                }
                elseif ($pos2 == false || $pos2 > $pos1)
                {
                    $pos = $pos1;
                    break;
                }
                $pos = $pos2 + 1;
            }
            $clean .= '$s$';
            $old_pos = $pos + 1;
        }
        $clean .= substr($db_string, $old_pos);
        $clean = trim( strtolower( preg_replace( array('~\s+~s' ), array(' '), $clean) ) );

        //老版本的Mysql并不支持union，常用的程序里也不使用union，但是一些黑客使用它，所以检查它
        if ( strpos( $clean, 'union' ) !== false && preg_match( '~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0 )
        {
            $fail = true;
            $error = "union detect";
        }

        //发布版本的程序可能比较少包括--,#这样的注释，但是黑客经常使用它们
        elseif (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false)
        {
            $fail = true;
            $error = "comment detect";
        }

        //这些函数不会被使用，但是黑客会用它来操作文件，down掉数据库
        elseif (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "slown down detect";
        }
        elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "slown down detect";
        }
        elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "file fun detect";
        }
        elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "file fun detect";
        }
        if ( !empty($fail) )
        {
            //echo $db_string;
            //fputs(fopen($log_file,'a+'),"$userIP||$getUrl||$db_string||$error\r\n");
            //echo $db_string;
            //exit("<font size='5' color='red'>Safe Alert: Request Error step 2!</font>");
            cls_app::log( $db_string );
        }
        else
        {
            return $db_string;
        }
    }
}
?>
