<?php

defined('IN_DCR') or exit('No permission.'); 

/**
 * Data数据类 数据库的处理类，一般数据库处理的类都以为这个父类
 * 这个类中有更新、插入、删除文档 还有获取数据等
 * ===========================================================
 * 版权所有 (C) 2005-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0
 * @package class
 * @since 1.0.8
*/

class cls_data
{
	private $table; //操作的表名
    private $last_sql;//最后操作的sql
	private $db; //操作的DB实例 多数据库操作用得上
	
	/**
	 * 构造函数
	 * @param string $table 要操作的表名
	 * @return Data 返回Data实例
	 */
	function __construct($table)
	{
		$this->table = $table;
	}
	
	function set_db( $db )
	{
		$this->db = $db;
	}
	function get_db()
	{
		if( $this->db )
		{
			return $this->db;
		}else
		{
			global $db;
			return $db;
		}
	}
	
	/**
	 * 设置要操作的表
	 * @param string $table 表名
	 * @return true
	 */
	function set_table($table)
	{
		$this->table = $table;
	}
	
    /**
     * 返回最后操作的sql
     * @return string
     */
    function get_last_sql()
    {
        $db = $this->get_db();
        return $db->option( $this->last_sql );
    }
	 
    /**
     * 获取数据库最后一条错误信息
     * @return string
     */
    function get_error()
    {
        $db = $this->get_db();
        return $db->get_db_error();
    }
	
	/**
	 * 设置最后操作的sql
	 * @param string $sql 操作的sql
	 * @return true
	 */
    private function set_last_sql($sql)
    {
        $this->last_sql = $sql;
    }
	
	/**
	 * 执行sql 有返回值
	 * @param string $sql 要执行的sql
	 * @return array 返回执行结果
	 */
	public function execute($sql)
	{
        $this->set_last_sql($sql);		
        $db = $this->get_db();
		$db->execute($sql);
		$data_list = $db->get_array();
		
		return $data_list;
	}
	
	/**
	 * 执行sql 无返回值
	 * @param string $sql 要执行的sql
	 * @return array 返回执行结果
	 */
	public function execute_none_query($sql)
	{
        $this->set_last_sql($sql);
        $db = $this->get_db();
		
		return $db->execute_none_query($sql);
	}
	
	/**
     * 执行sql查询
     * @param string $col 需要查询的字段值[例`name`,`gender`,`birthday` 默认为*]
     * @param string $where 查询条件[例`name`='$name']
     * @param string $limit 返回结果范围[例：10或10,10 默认为空]
     * @param string $order 排序方式    [默认按数据库默认方式排序]
     * @param string $group 分组方式    [默认为空]
     * @param string $addon_table 附加表
     * @param string $addon_col 附加表字段
     * @return array 查询结果集数组
     */
    public function select( $col = '*', $where = '', $limit = '', $order = '', $group = '', $addon_table = '', $addon_col = '', $join = '', $having = '', $force_index = '', $join_ex = array() )
    {    
		if( is_array( $where ) )
		{
			if(  count( $where ) )
			{
				$where = implode(' and ', $where );
			}else
			{
				$where = '';
			}
		}
        $col = $col == '' ? '*' : $col;
        $where = $where == '' ? '' : ' where ' . $where;
        $group = $group == '' ? '' : ' group by ' . $group;
        $limit = $limit == '' ? '' : ' limit ' . $limit;
        $join = $join == '' ? '' : '' . ' ' . $join;
		if( $join_ex )
		{
			foreach( $join_ex as $join_info )
			{
				if( $join_info['table'] )
				{
					$join .= " {$join_info['join_type']} {$join_info['table']} on {$join_info['where']}";
				}
			}
		}
        $having = $having == '' ? '' : '' . ' having ' . $having;
        $force_index = $force_index == '' ? '' : " force index ({$force_index}) ";
   
        $table = $this->table;
        if( !empty($addon_table) )
        {
            //这里col都要加上前标
            $col_arr = explode( ',', $col );
            foreach( $col_arr as $col_key=> $col_v )
            {
                $col_arr[$col_key] = $this->table . '.' . $col_v;
            }
            $col = implode( ',', $col_arr );
           
            $col_addon_arr = explode( ',', $addon_col );
            foreach( $col_addon_arr as $col_addon_key=> $col_addon_v )
            {
                $col_addon_arr[$col_addon_key] = $addon_table . '.' . $col_addon_v;
            }
            $addon_col = implode( ',', $col_addon_arr );
           
            $table .= ',' . $addon_table;
            $col .= ',' . $addon_col ;
        }
        $order = $order == '' ? '' : ' order by ' . $order;

        $sql = 'select ' . $col . ' from ' . $table . $force_index . $join . $where . $group . $having . $order . $limit;

        //echo $sql;
        $this->set_last_sql($sql);
        $db = $this->get_db();
        $db->execute($sql);
        $data_list = $db->get_array();
       
        return $data_list;
    }
	
	/**
     * 执行sql查询 和select不同的 这个解析字符串来查询
     * @param array $canshu 参数 cols 需要查询的字段值[例`name`,`gender`,`birthday` 默认为*] where 查询条件 limit 返回结果范围[例：10或10,10 默认为空] order 排序方式    [默认按数据库默认方式排序] group 分组方式    [默认为空]  addon_table附加表 addon_col 附加字段 例如:array('cols'=>'age', 'where'=>'age>18', limit=>'10', 'order'=>'id desc')
     * @return array 查询结果集数组
     */
    public function select_ex( $canshu = array() )
    {
        $data_list = $this->select($canshu['col'], $canshu['where'], $canshu['limit'], $canshu['order'], $canshu['group'], $canshu['addon_table'], $canshu['addon_col'], $canshu['join'], $canshu['having'], $canshu['force_index'], $canshu['join_ex']);
       
        return $data_list;
    }
	
	/**
     * 返回一条记录
     * @param array $canshu 参数 同select_ex 的$canshu说明
     * @return array 查询结果集数组
     */
    public function select_one($canshu = array())
    {
        $canshu['limit'] = 1;
        //p_r($canshu);
        $data_list = $this->select_ex($canshu);
       
        return $data_list;
    }
	
	/**
     * 返回一条记录 跟select_one区别是这个函数返回current($data_list) 而select_one返回$data_list
     * @param array $canshu 参数 同select_ex 的$canshu说明
     * @return array 查询结果集数组
     */
    public function select_one_ex( $canshu = array() )
    {
        $data_list = $this->select_one($canshu);
       
        return current($data_list);
    }
	
	
	/**
	 * 执行添加记录操作
	 * @param array $info 插入的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
	 * @param boolen $return_sql_only 只返回sql 不执行
     * @param boolen $return_insert_id 返回insert id
	 * @return int 返回值为文档的ID,失败返回0
	 */
	function insert( $info, $return_sql_only = 0, $return_insert_id = 1 )
	{
        $key_list = implode( '`,`', array_keys($info) );
        $key_list = '`' . $key_list . '`';
        $value_list = implode( "','", array_values($info) );
        $value_list = "'" . $value_list . "'";
        $sql = 'insert into ' . $this->table . "($key_list) values($value_list)";

		$return_val = '';
		if( $return_sql_only )
		{
			$return_val = $sql;
		}else
		{
			$db = $this->get_db();
			$val = $db->execute_none_query($sql);
			$this->set_last_sql($sql);
            if( $return_insert_id )
            {
                $return_val = $db->get_insert_id();
            }else
            {
                $return_val = $val;
            }
		}
		return $return_val;
	}

    /**
     * 执行添加记录操作 修正一个insert的function会造成的BUG,该bug在多次select中不会返回last_insert_id
     * @param array $info 插入的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param boolen $return_sql_only 只返回sql 不执行
     * @return int 返回值为文档的ID,失败返回0
     */
    function insert_ex( $info, $return_insert_id = 0, $return_sql_only = 0 )
    {
        return $this->insert( $info, $return_sql_only, $return_insert_id );
    }
	
	/**
	 * 批量执行添加记录操作
	 * @param array $info 插入的数据 格式为:array( array('a'=>1, 'b'=>2),array('a'=>3, 'b'=>4) )
	 * @param boolen $return_sql_only 只返回sql 不执行
	 * @return int 返回值为文档的ID,失败返回0
	 */
	function insert_bulk( $info, $return_sql_only = false )
	{
        $key_list = implode( '`,`', array_keys( current( $info ) ) );
        $key_list = '`' . $key_list . '`';
		$value_arr = array();
		foreach( $info as $data )
		{
        	$value_str ="('" .  implode( "','",  $data ) . "')";
			array_push( $value_arr, $value_str );
		}
		$value_list = implode( ',', $value_arr);
        $sql = 'insert into ' . $this->table . "($key_list) values {$value_list}";
		
		$return_val = '';
		if( $return_sql_only )
		{
			$return_val = $sql;
		}else
		{
       	
			$db = $this->get_db();
			$return_val = $db->execute_none_query($sql);
			//cls_app::log($sql);
			$this->set_last_sql($sql);
		}

		return $return_val;
	}	
	
	/**
     * 执行raplace记录操作
     * @param array $info 插入的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param boolen $return_sql_only 只返回sql 不执行
     * @return int 返回值为文档的ID,失败返回0
     */
    function replace( $info, $return_sql_only = false )
    {
        $key_list = implode( '`,`', array_keys($info) );
        $key_list = '`' . $key_list . '`';
        $value_list = implode( "','", array_values($info) );
        $value_list = "'" . $value_list . "'";
        $sql = 'replace into ' . $this->table . "($key_list) values($value_list)";

        $return_val = '';
        if( $return_sql_only )
        {
            $return_val = $sql;
        }else
        {

			$db = $this->get_db();
			$return_val = $db->execute_none_query($sql);
            //cls_app::log($sql);
            $this->set_last_sql($sql);
        }

        return $return_val;
    }

	function replace_bulk( $info, $return_sql_only = false )
	{
		$key_list = implode( '`,`', array_keys( current( $info ) ) );
		$key_list = '`' . $key_list . '`';
		$value_arr = array();
		foreach( $info as $data )
		{
			$value_str ="('" .  implode( "','",  $data ) . "')";
			array_push( $value_arr, $value_str );
		}
		$value_list = implode( ',', $value_arr);
		$sql = 'replace into ' . $this->table . "($key_list) values {$value_list}";

		$return_val = '';
		if( $return_sql_only )
		{
			$return_val = $sql;
		}else
		{
			global $db;
			$return_val = $db->execute_none_query($sql);
			//cls_app::log($sql);
			$this->set_last_sql($sql);
		}

		return $return_val;
	}
	/**
	 * 更新文档
	 * @param array $info 更新的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
	 * @param string $where 更新条件
     * @param boolen $return_sql_only 只返回sql 不执行
	 * @return boolean 更新成功返回true 失败返回false
	 */
	function update($info = array(), $where= '', $return_sql_only = false, $back_affected_rows = 0 )
	{
        $update_str = '';
		foreach($info as $key => $value)
		{
			//如果是+则不要''包围 如:click=click+1
			if( strpos($value, $key . '+') === false && strpos($value, $key . '-') === false )
			{
                $value = (string) $value;
                if( 'null' == $value )
                {
                    $update_str .= "`$key`=null,";
                }else
                {
                    $update_str .= "`$key`='$value',";
                }
			}else
			{
				$update_str .= "`$key`=$value,";
			}
		}
		$update_str = substr( $update_str, 0, strlen($update_str) - 1 );
		$sql = "update ".$this->table." set $update_str where $where";
        //echo $sql;

        if( $return_sql_only )
        {
            $return_val = $sql;
        }else
        {

            $db = $this->get_db();
            $return_val = $db->execute_none_query($sql);
            //cls_app::log($sql);
            $this->set_last_sql($sql);
            if( $back_affected_rows )
            {
                $return_val = $db->get_affected_rows();
            }
        }

		return $return_val;
	}

    /**
     * 更新一条文档
     * @param array $info 更新的数据 用数组表示,用$key=>$value来表示列名=>值 如array('title'=>'标题') 表示插入title的值为 标题
     * @param string $where 更新条件
     * @param boolen $return_sql_only 只返回sql 不执行
     * @return boolean 更新成功返回true 失败返回false
     */
    function update_one($info = array(), $where= '', $return_sql_only = false)
    {
        $sql = $this->update( $info, $where, true );
        $sql .= ' limit 1';

        if( $return_sql_only )
        {
            $return_val = $sql;
        }else
        {

            $db = $this->get_db();
            $return_val = $db->execute_none_query($sql);
            //cls_app::log($sql);
            $this->set_last_sql($sql);
        }

        return $return_val;

    }
	
	/**
	 * 执行删除操作
	 * @param array $id_list 删除的文档ID 可以是数组 比如array('1','2') 也可以用是字符串 但要以,分隔 比如1,2,3
	 * @param string $col 删除的基准列名，默认为ID 如删除ID=1的文档 要删除aid=1的文档则这个值为'aid'
	 * @return boolean 成功返回true 失败返回false
	 */
	function delete($id_list, $col = 'id')
	{
		if( is_array($id_list) )
		{
			$id_list = implode(',', $id_list);
		}
		
		if( !empty($id_list) )
		{
       		$db = $this->get_db();
			$sql = "delete from " . $this->table . " where $col in($id_list)";
        	$this->set_last_sql($sql);
			$r_val = $db->execute_none_query($sql);
		}else
		{
			$r_val = false;
		}
		
		return $r_val;
	}
	
	/**
	 * 执行删除操作
	 * @param string $where 删除条件
	 * @param string $limit 删除条数
	 * @return boolean 成功返回true 失败返回false
	 */
	function delete_ex( $where, $limit = '' )
	{
		if( !empty($where) )
		{
			$where = ' where ' . $where;
		}
		if( ! empty( $limit ) )
		{
			$limit = ' limit ' . $limit;
		}
		
        $db = $this->get_db();
		$sql = "delete from " . $this->table . $where . $limit;
        $this->set_last_sql($sql);
		$r_val = $db->execute_none_query($sql);
		
		return $r_val;
	}

    /**
     * 事务开始
     * @return boolean 成功返回true 失败返回false
     */
	function transaction_begin()
	{
        $db = $this->get_db();
		$sql = 'begin';
        $this->set_last_sql($sql);
		$r_val = $db->execute_none_query($sql);
	}

    /**
     * 事务提交
     * @return boolean 成功返回true 失败返回false
     */
	function transaction_rollback()
	{
        $db = $this->get_db();
		$sql = 'rollback';
        $this->set_last_sql($sql);
		$r_val = $db->execute_none_query($sql);
	}

    /**
     * 事务回滚
     * @return boolean 成功返回true 失败返回false
     */
	function transaction_commit()
	{
        $db = $this->get_db();
		$sql = 'commit';
        $this->set_last_sql($sql);
		$r_val = $db->execute_none_query($sql);
	}


    function get_found_rows()
    {
        $db = $this->get_db();
        $sql = 'select found_rows() as found_rows';
        $this->set_last_sql($sql);
        $r_val = $db->execute($sql);
        $value = $r_val[0]['found_rows'];
        return $value;
    }

	function ping()
	{
		$db = $this->get_db();
		return $db->ping();
	}
}

?>