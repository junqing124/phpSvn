<?php

/**
 * 后台管理员类
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

require_once(WEB_CLASS . '/class.data.php');

class cls_user extends cls_data
{
	private $username;
	private $password;
	private $can_sh_code;
	
	function __construct( $username = '', $password = '' )
	{
		//密码应该为加密后的字符串
		$this->username = $username;
		$this->password = $password;
		parent::__construct('svn_user');
	}
	function cls_member_admin($username = '', $password = '' )
	{
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	 * 验证用户名
	 * @return boolean
	 */
	function yz()
	{
		$info = parent::select_one_ex( array( 'col'=> '*', 'where'=>"su_name='{$this->username}' and su_password='{$this->password}'" ) );
		$this->can_sh_code = $info['su_can_sh_code'];
		$this->id = $info['su_id'];
		
		if( ! $info['su_id'] )
		{
			return array( 'ack' => 0, 'msg' => '您输入的用户名或密码不正确！' );
		} else
		{
			return array( 'ack' => 1, 'msg' => '登录成功' );
		}
	}
	
	/**
	 * 验证用户名
	 * @param array $canshu 参数列表：
	 * @param array $canshu['col']        要返回的字段列 以,分隔
	 * @param array $canshu['where']      条件
	 * @param array $canshu['limit']      返回的limit
	 * @param array $canshu['group']      分组grop
	 * @param array $canshu['order']      排序 默认是istop desc,id desc
	 * @return boolean
	 */
	function get_info( $canshu = array() )
	{
		if(!empty($canshu['where']))
		{
			$canshu['where'] .= " and user_username='{$this->username}'";
		}else
		{
			$canshu['where'] = " user_username='{$this->username}'";
		}
		
		$info = parent::select_one_ex( $canshu );
		
		return $info;
	}
	
	function get_info_by_id( $id, $col = '' )
	{
		$canshu = array();
		$canshu['where'] = "user_id={$id}";
		$canshu['col'] = $col;
		
		$info = parent::select_one_ex( $canshu );
		
		return $info;
		/*$cache_file = WEB_INCLUDE . '/define/user_list.php';
		require( $cache_file );
		$has_info = false;
		foreach( $user_list_cache as $info )
		{
			if( $info['user_id'] == $id )
			{
				$has_info = true;
				break;
			}
		}
		if( ! $has_info )
		{
			$info = false;
		}
		
		return $info;*/
	}
	function get_username_by_id( $id )
	{
		$info = $this->get_info_by_id($id);
		return $info['user_username'];
	}
	
	function get_info_by_username( $username, $col = '' )
	{
		$canshu = array();
		$canshu['where'] = "user_username='{$username}'";
		$canshu['col'] = $col;
		
		$info = parent::select_one_ex( $canshu );
		
		return $info;
		/*$cache_file = WEB_INCLUDE . '/define/user_list.php';
		require( $cache_file );
		$has_info = false;
		foreach( $user_list_cache as $info )
		{
			if( $info['user_username'] == $username )
			{
				$has_info = true;
				break;
			}
		}
		if( ! $has_info )
		{
			$info = false;
		}
		
		return $info;*/
	}	
	
	/*
		通过会员名获取ID
		*@ param string $username 用户名
		*@ return int 会员ID
	*/
	function get_id_by_username( $username )
	{
		$info = $this->get_info_by_username( $username );
		return $info['user_id'];
	}
	
	/*
		获取会员列表
		* @param array $cs $cs['pinying_qz']=1  表示name加个拼音前缀比如张三为Z_张三，其它的参数和select_ex一样
		* @return array 会员数组
	*/
	function get_list( $cs = array() ,$include_all = true )
	{
	  if( $include_all ){
	       if( is_array( $cs['where'] ) )
		  {
			$cs['where'][] = 'user_is_valid=1';
	   	   }else
		  {
			if( strlen( $cs['where'] ) )
			{
				$cs['where'] .= ' and user_is_valid=1';
			}else
			{
				$cs['where'] = 'user_is_valid=1';
			}
		  }
        }
		$list = parent::select_ex( $cs );
		if( $cs['pinying_qz'] )
		{
			foreach( $list as $key=> $info )
			{
				$list[$key]['user_username'] = get_char_sm( $list[$key]['user_username'] ) . '_' . $list[$key]['user_username'];
			}
		}
		return $list;
	}
		
	/**
	 * 登陆
	 * @return boolean
	 */
	function login()
	{		
		//登陆
		$_SESSION['admin_u'] = $this->username;
		$_SESSION['admin_p'] = $this->password;
		$_SESSION['admin_can_sh_code'] = $this->can_sh_code;
		$_SESSION['admin_id'] = $this->id;
	}
		
	/**
	 * 修改密码
	 * @param string $password 新密码
	 * @return boolean
	 */
	function chang_password( $password )
	{
		
		$info = array( 'user_password'=> jiami( $password ) );
		//p_r($info);
		return parent::update($info, "user_username='{$this->username}'");
	}	
		
	/**
	 * 退出管理
	 * @return true
	 */
	function logout()
	{
		$info = parent::select_one_ex( array( 'col'=> 'user_session_id', 'where'=>"user_username='{$_SESSION['admin_u']}'" ) );
		if( $info['user_session_id'] == session_id() ){
        $login_info = array(
							'user_session_id'=> '',
							);
		parent::update($login_info, "user_username='{$_SESSION['admin_u']}'");
		}
		unset($_SESSION['admin_u']);
		unset($_SESSION['admin_p']);
	}
	/**
	 * 用户列表缓存
	 * @return boolean				 ture or false
	 */
	
	function update_user_cache()
	{
		$list = $this->get_list();
		foreach( $list as $val ){
			$data[$val['user_id']] = $val;
		}
		//var_dump($data);
		$list = $data;
		$content = var_export( $list, true );
		$content = "<?php \$user_list_cache={$content};?>";
		$cache_file = '/define/user_list.php';
		$cls_cache = cls_app:: get_cache( $cache_file, 0, WEB_INCLUDE ); 
		$cls_cache->write( $content );
		return true;
	}
	/**
	 * 用户列表缓存(存有包含上下级关系)
	 * @return boolean				 ture or false
	 */
	function update_user_cache_2(){
		$col='user_id,user_username,user_password,user_login_time,user_login_ip,user_login_count,user_group_id,user_permissions_id,user_record_num,user_min_cgts,user_min_yjts,user_msg_account,user_is_valid,user_urls_quick,user_purchase_allow_order_type,user_father_id,user_permissions_path';
		$data = $this->get_sons_by_father_id(0,$col,1);
		$list = $data;
		$content = var_export( $list, true );
		$content = "<?php \$user_list_cache={$content};?>";
		$cache_file = '/define/user_list2.php';
		$cls_cache = cls_app:: get_cache( $cache_file, 0, WEB_INCLUDE ); 
		$cls_cache->write( $content );
		return true;
	}
	
	/**
	 * 用户权限编辑
	 * @param int                    $user_id  会员ID
	 * @param array                  $permissions 权限数组
	 * @param string 				 $type 添加(add)还是删除(delete)
	 * @return boolean				 ture or false
	 */
	function user_permission_option( $user_id,$permissions, $type )
	{
		$user_info = $this->get_info_by_id( $user_id ,$col='user_permissions_id');
		$user_permissions = explode(',', $user_info['user_permissions_id'] );
		if(empty($permissions)&&!is_array($permissions)){
			return false;
		}
		if( $type =='add'){
			$new_permission = array_unique(array_merge( $permissions,$user_permissions ));//添加合并
			$info['user_permissions_id'] = implode( ',', $new_permission );
			if($this->update($info,'user_id='.$user_id)){
				return true;
			}else{
				return false;
			}
		
		}else if( $type == 'delete'){
			$new_permission = array();
			foreach( $permissions as $key=>$val){
				if(in_array( $val,$user_permissions )){
					$user_permissions = array_remove( $user_permissions,$val);//删除权限
				}
			}
			$new_permission = $user_permissions;
			if(empty($new_permission)){
				$info['user_permissions_id'] = '';
			}else{
				$info['user_permissions_id'] = implode( ',', $new_permission );
			}
			if($this->update($info,'user_id='.$user_id)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/**
	 * 用户列表(包含层级)
	 * @param int                    $father_id  父类ID
	 * @param int 					 $level =1 层级默认1
	 * @return arry					 $list
	 */
	function get_user(){
		$cache_file = WEB_INCLUDE . '/define/user_list2.php';
		if(file_exists( $cache_file ) ){			
				require( $cache_file );
		}else{
			$col='user_id,user_username,user_password,user_login_time,user_login_ip,user_login_count,user_group_id,user_permissions_id,user_record_num,user_min_cgts,user_min_yjts,user_msg_account,user_is_valid,user_urls_quick,user_purchase_allow_order_type,user_father_id,user_permissions_path';
			$user_list_cache = $this->get_sons_by_father_id(0,$col,1);
		}
		return $user_list_cache;
	}
		
		
	/**
	 * 获取子类
	 * @param int                    $father_id  父类ID
	 * @param int 					 $level =1 层级默认1
	 * @return array					 $list
	 */
	function get_sons_by_father_id( $father_id =0, $col ='user_id,user_username,user_father_id,user_permissions_path',$level =1){
		$canshu['where'] = 'user_father_id = '.$father_id;
		$canshu['order'] = 'user_permissions_path desc';
		$canshu['col'] = $col;
		$list = $this->select_ex($canshu);
		if($list){
			foreach( $list as $key=>$v){
				$son = $this->get_sons_by_father_id( $v['user_id'] ,$col,$level+1);
				$list[$key]['sub_level'] = $level;	
					if($son){
						$list[$key]['sons'] = $son;
					}
			}
			return $list;
			}
	
	}
	
	/*
	* 获取子类(下级)的权限
	* @param int    $user_id 会员的ID
	* @return  array $sonID 子类ID
	*/
	function get_son_id( $user_id ){
		$col = 'user_id';
		$data = $this->get_sons_by_father_id( $user_id ,$col);
        if( $data )
        {
            foreach( $data as $val )
            {
                self::$sonID[] = $val['user_id'];
                if(is_array($val['sons'])){
                    $this->get_son_id( $val['user_id']);
                }
            }
            return self::$sonID;

        }else
        {
            return false;
        }
	}

	//本function和下面的get_order_account差不多。
	function get_order_account_ex( $user_id )
	{
		$cls_store = new cls_store();
		$result = array();
		$store_arr = array();
		if( $user_id )
		{
			$admin_info = $this->get_info_by_id( $user_id, 'user_order_account');
			//p_r( $admin_info );
			$user_order_account = $admin_info['user_order_account'];
			if( empty( $user_order_account ))
			{
				return $store_arr;
			}
			//不为空
			if( ! empty( $user_order_account ) && ! in_array( $user_order_account, array( ';any;', 'any,' ) ) )
			{
				$store_ids = str_replace( ';', ',', $user_order_account );
				$store_names = $cls_store->select_ex(array('col'=>'store_name','where'=>"store_id in ({$store_ids})")) ;
				foreach( $store_names as $store_name){
					array_push( $store_arr, $store_name['store_name'] );
				}
				$result['all_account'] = 0;
				$result['store_arr'] = $store_arr;
			}else{
				$store_names = $cls_store->select_ex(array('col'=>'store_name')) ;
				foreach( $store_names as $store_name){
					array_push( $store_arr, $store_name['store_name'] );
				}
				$result['all_account'] = 1;
				$result['store_arr'] = $store_arr;
			}
		}
		return $result;
	}

	/*
     * 获取能操作的订单帐号
     * @return array 帐号列表，用字符表示
     */
    function get_order_account( $user_id )
    {
		$cls_store = new cls_store();
        $store_arr = array();
        if( $user_id )
        {
            $admin_info = $this->get_info_by_id( $user_id, 'user_order_account');
            //p_r( $admin_info );
            $user_order_account = $admin_info['user_order_account'];
			if( empty( $user_order_account ))
            {
			  return $store_arr;
			}
			//不为空
            if( ! empty( $user_order_account ) && ! in_array( $user_order_account, array( ';any;', 'any,' ) ) )
            {
                $store_ids = str_replace( ';', ',', $user_order_account );
                $store_names = $cls_store->select_ex(array('col'=>'store_name','where'=>"store_id in ({$store_ids})")) ;
                foreach( $store_names as $store_name){
                    array_push( $store_arr, $store_name['store_name'] );
                }
            }else{
			 	$store_names = $cls_store->select_ex(array('col'=>'store_name')) ;
                foreach( $store_names as $store_name){
                    array_push( $store_arr, $store_name['store_name'] );
                }
			}
        }
        return $store_arr;
    }

    /*
     * 获取用户负责仓库区域
     */
    function  get_user_location()
    {
    	$location_list = $this->select_ex(array('col'=>'user_id,user_username,user_location_detail,user_location_order','where'=>'user_location_detail <>""', 'order'=>'user_location_order asc'));
    	return $location_list;
    }

    /*
     *解析仓库区域
     */
    function parse_location_list( $location )
    {
        $location_list = array();
        $location_arr = explode( ',', $location );
        foreach( $location_arr as $location_str )
        {
            $location_str_arr = explode( '-', $location_str );
            if( count( $location_str_arr ) == 1 )
            {
                array_push( $location_list, $location_str );
            }else
            {
                $start = $location_str_arr[0];
                $end = $location_str_arr[count( $location_str_arr ) - 1 ];
                $start_int = intval( substr( $start, 1 ) );
                $end_int = intval( substr( $end, 1 ) );
                for( $i = $start_int; $i<= $end_int; $i++ )
                {
                    $t_i = str_pad(  $i, 3, '0', STR_PAD_LEFT);
                    $t_str = substr( $start, 0, 1 ) . $t_i;
                    array_push( $location_list, $t_str );
                }
            }
        }
		$location_list = array_remove_empty( $location_list );
        return $location_list;
    }
    
    /*
     * @param $day格式1970-01-01
     * 获取用户负责仓库区域按天
     */
    function get_location_by_day($day){
    	$day = date('Ymd',strtotime($day));
    	$this->set_table( "@#@user_location" );
    	$location_info = $this->select_one_ex(array('where'=>"ul_day={$day}"));
    	return $location_info['ul_location_detail'];
    }
    
    function get_all_saler($col='*',$group_id='')
    {
        if( $group_id )
        {
            $user_list = $this->select_ex(array('col'=>"$col",'where'=>"user_group_id in($group_id)"));
        }else
        {
            $user_list = $this->select_ex(array('col'=>"$col",'where'=>'user_group_id in(3,5,6,7,11)'));
        }
        return $user_list;
    }
    
    //获取关联id
    function get_relate_user_by_id($admin_id)
    {
        $user_info = $this->get_info_by_id ( $admin_id );
       
        $user_father_id = $user_info ['user_father_id'];
		$user_has_par_sku = $user_info ['user_has_par_sku'];
		$user_see_cg_id = $user_info ['user_can_see_cg_id'];
		$user_sons_info = $this->get_sons_by_father_id ( $admin_id );
		$relation_id_Set = array (
				$admin_id 
		);

		if (trim ( $user_father_id ) != '' && $user_has_par_sku == 1) 
		{
			$relation_id_Set [] = $user_father_id;
		}
		if ($user_sons_info) 
		{
			foreach ( $user_sons_info as $user_sons_value ) {
				$relation_id_Set [] = $user_sons_value ['user_id'];
			}
		}

		if ($user_see_cg_id)
		{
			$user_see_cg_id_arr = explode ( ',', $user_see_cg_id );
			foreach ( $user_see_cg_id_arr as $user_see_cg_id ) {
				$relation_id_Set [] = $user_see_cg_id;
			}
		}
		return $relation_id_Set;
    }
}
?>