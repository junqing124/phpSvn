<?php

defined('IN_DCR') or exit('No permission.'); 

/**
 * 全站共用function
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @package class
 * @since 1.0.8
*/
 
/**
 * 对字符串进行加密
 * @param string $s 要加密的字符串
 * @return string 加密后的字符串
 */
function encrypt( $s )
{
	return crypt( md5( $s ), 'dcr' );
}

/**
 * 对字符串进行加密 这里调用encrypt函数,为encrypt函数的别名
 * @param string $s 要加密的字符串
 * @return string 加密后的字符串
 */
function jiami( $s )
{
	return encrypt( $s );
}

/**
 * 生成javascript跳转 并自动跳转
 * @param string $msg 显示信息
 * @param string $url 要跳转的地址
 * @param string $istop 是不是在父窗口中跳转
 * @return boolean 跳转到相应的网址
 */
function show_next( $msg, $url, $istop = 0 )
{
	if( strlen( $msg ) > 0 )
	{
		if( $istop )
		{
			$mymsg = "<script type='text/javascript'>alert(\"" . $msg . "\");top.location.href=\"" . $url . "\";</script>";
		}else
		{
			$mymsg = "<script type='text/javascript'>alert(\"" . $msg . "\");location.href=\"" . $url . "\";</script>";
		}
	}else
	{
		if( $istop )
		{
			$mymsg = "<script type='text/javascript'>top.location.href=\"" . $url . "\";</script>";
		}else
		{
			$mymsg = "<script type='text/javascript'>location.href=\"" . $url . "\";</script>";
		}
	}
	echo $mymsg;
	exit;
}

/**
 * 返回上一页
 * @param string $msg 显示信息
 * @return boolean 显示一个alert提示信息
 */
function show_back($msg = '')
{
	if( !empty($msg) )
	{
		echo "<script>alert(\"" . $msg . "\");history.back();</script>'";
	}else
	{
		echo "<script>history.back();</script>'";
	}
	exit;
}

//返回页面并提示信息
//action_msg 提示信息
//action_type 1为正确信息 2为错误信息
function back_msg( $action_msg, $action_type = 1, $back_url = '' )
{
	if( empty( $back_url ) )
	{
		$back_url = $_SERVER['HTTP_REFERER'];
	}
	$_SESSION['bullfrog_action_msg'] = $action_msg;
	$_SESSION['bullfrog_action_type'] = $action_type;
	/*p_r( $back_url );
	exit;
	$back_url_arr = parse_url( $back_url );
	$query = $back_url_arr['query'];
	$query_arr = explode('&', $query); 	     
	$params = array();
	$url_main = 'http://' . $back_url_arr['host'] . $back_url_arr['path'];
 	foreach ($query_arr as $param)
	{
		$item = explode('=', $param);
		$params[$item[0]] = $item[1];
	}
	//$params['action_msg'] = $action_msg;
	//$params['action_type'] = $action_type;
	$query_str = http_build_query( $params );
	$back_url = $url_main . '?' . $query_str;*/
	/*if( $back_url_arr['query'] )
	{
		$back_url .= "&action_msg={$action_msg}&action_type={$action_type}";
	}else
	{
		$back_url .= "?action_msg={$action_msg}&action_type={$action_type}";
	}*/
	echo "<script type='text/javascript'>location.href=\"{$back_url}\";</script>";
}

/**
 * 跳转
 * @param string $url 要跳转的地址
 * @return boolean 跳转到$url
 */
function redirect($url)
{
	echo "<script>location.href='" . $url . "';</script>'";
	exit;
}

/**
 * 截取字符串 能对中文进行截取
 * @param string $str 要截取的字条串
 * @param string $start 开始截取的位置
 * @param string $len 截取的长度
 * @return string 截取后的字符串
 */
function my_substr($str, $start, $len)
{
    $tmpstr = "";
    $strlen = $start + $len;
    for($i = 0; $i < $strlen; $i++)
    {
        if( ord( substr($str, $i, 1) ) > 0xa0 )
        {
            $tmpstr .= substr($str, $i, 3);
            $i += 2;
        } else
            $tmpstr .= substr($str, $i, 1);
    }
    return $tmpstr;
}

/**
 * 写入cookie
 * @param string $key cookie名
 * @param string $value cookie值
 * @param string $kptime cookie有效期
 * @param string $pa cookie路径
 * @return boolean 返回true
 */
function put_cookie($key, $value, $kptime = 0, $pa = "/")
{
	setcookie( $key, $value, time() + $kptime, $pa );
}

/**
 * 删除cookie
 * @param string $key cookie名
 * @return boolean 返回true
 */	
function drop_cookie( $key )
{
	setcookie( $key, '', time() - 360000, "/" );
}

/**
 * 获取cookie值
 * @param string $key cookie名
 * @return string 获取的cookie的值
 */		
function get_cookie($key)
{
	if( !isset($_COOKIE[$key]) )
	{
		return '';
	}
	else
	{
		return $_COOKIE[$key];		
	}
}

/**
 * 获取当前IP
 * @return string 本机的IP
 */	
function get_ip()
{
	if( ! empty( $_SERVER["HTTP_CLIENT_IP"] ) )
	{
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	else if( ! empty( $_SERVER["HTTP_X_FORWARDED_FOR"] ) )
	{
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if( ! empty( $_SERVER["REMOTE_ADDR"] ) )
	{
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else
	{
		$cip = '';
	}
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = isset( $cips[0]) ? $cips[0] : 'unknown';
	unset($cips);
	
	return $cip;
}

/**
 * 获取顶级域名
 * @param string $url 要操作的地址
 * @return string $url的顶级域名
 */	
function get_top_url($url = '')
{
	if(empty($url))
	{
		$url = $_SERVER['SERVER_NAME'];
	}
	$t_url = parse_url($url);
	$t_url = $t_url['path'];
	
	return $t_url;
}
	
/**
 * 显示提示信息
 * @param string $msg 信息内容
 * @param string $msg_type 信息类型1为一般信息 2为错误信息
 * @param string $back 返回地址 如果有多个则传入数组
 * @param string $msgTitle 信息标题
 * @param boolean $is_show_next_tip 为true时显示下你可以下一步操作,为false时不显示
 * @param boolean $is_show_back 为true时显示返回,为false时不显示 版本>=1.0.5
 * @return boolean(true) 显示一个提示信息
 */
function show_msg($msg, $msg_type = 1, $back = '', $msgTitle = '信息提示', $is_show_next_tip = true, $is_show_back = true)
{
	/*
	 *msg显示信息 如果要多条则传入数组
	 *msg_type信息类型1为一般信息 2为错误信息
	 *back为返回地址 如果有多个则传入数组
	 *msgTitle为信息标题
	 */
	if( is_array($msg) )
	{
		foreach($msg as $value)
		{
			if( $msg_type == 2 )
			{
				$msg_t .= "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:red;'>·$value</li>";
			}else{
				$msg_t .= "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:green;'>·$value</li>";
			}
		}
	}else
	{
		if( $msg_type == 2 )
		{
			$msg_t = "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:red;'>·$msg</li>";
		}else
		{
			$msg_t = "<li style='border-bottom:1px dotted #CCC;padding-left:5px;color:green;'>·$msg</li>";
		}
	}
	if($is_show_next_tip)
	{
		if($is_show_back)
		{
			$back_t = "<li style='border-bottom:1px dotted #CCC;padding-left:5px;'>·<a style='color:#06F; text-decoration:none' href='javascript:history.back()'>返回</a></li>";
		}
		if( is_array($back) )
		{
			foreach($back as $key=> $value )
			{
				$back_t .= "<li style='border-bottom:1px dotted #CCC;padding-left:5px;'>·<a style='color:#06F; text-decoration:none' href='$value'>$key</a></li>";
			}
		}
	}
	global $web_code;
	$msg_str = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=$web_code' /><title>信息提示页面</title></head><body><div style='width:500px; margin:0 auto; border:1px #09F solid; font-size:12px;'>
<div style='background-color:#09F; font-size:12px;padding:5px; font-weight:bold; color:#FFF;'>$msgTitle</div>
<div><ul style='list-style:none; line-height:22px; margin:10px; padding:0'>$msg_t</ul></div>";
	if( $is_show_next_tip )
	{
		$msg_str .= "<div style='border:1px #BBDFF8 solid; width:96%; margin:0 auto; margin-bottom:10px;'><div style='background-color:#BBDFF8; font-size:12px;padding:5px; font-weight:bold; color:#666;'>您可以：</div>
	<div><ul style='list-style:none; line-height:22px; margin:10px; padding:0'>$back_t</ul></div></div></div>";
	}
	$msg_str .= "</body></html>";
	//$msg_str.=$msg;
	echo $msg_str;
	exit;
}

/**
 * 获取随机字符串
 * @param int $len 字符串长度
 * @return string 产生的随机字符串
 */
function get_rand_str($len = 4, $rand_array = array("a","b","c","d","e","f","g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v","w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G","H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R","S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2","3", "4", "5", "6", "7", "8", "9") )
{
	$chars = $rand_array;
	$charsLen = count($chars) - 1;
	shuffle( $chars );
	$output = "";
	for( $i = 0; $i < $len; $i ++ )
	{
		$output .= $chars[mt_rand(0, $charsLen)];
	}
	
	return $output;
}

/**
 * 格式化输出数据
 * @param array $arr 要输出的数组
 * @param boolean $is_stop_output 是否停止输出流 如果为true则exit(); since>=1.0.7
 * @return true
 */	
function p_r($arr, $is_stop_output = false)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
	if($is_stop_output)
	{
		exit;
	}
}

/**
 * 去除数组空白元素
 * @since 1.0.8
 * @param array $arr 要操作的数组
 * @return array 去重后的数组
 */	
function array_remove_empty(&$arr, $trim = true)   
{   
	foreach ($arr as $key => $value)
	{
		if(is_array($value))
		{
    		array_remove_empty($arr[$key]);   
    	}else
		{
    		$value = trim($value);   
    		if ($value == '')
			{   
    			unset($arr[$key]);   
    		}elseif ($trim)
			{
    			$arr[$key] = $value;   
    		}   
    	}   
	}
	return $arr;
}

/**
 * 页面输出信息 弄这个function的目的是想页面所有的测试信息都用这个。以后不想有测试信息直接注释p_r($str)就OK了 ^_^ 懒人一枚唉...
 * @since 1.1.0
 * @param string $str 信息内容
 * @return true
 */	
function msg( $str )
{
	p_r($str);
}


/**
 * 获取页面接收的post,get数据
 * @since 1.1.1
 * @param string $no_field 不要的字段
 * @param string $have_time 是不是要添加时间
 * @return array
 */   
function get_req_data( $no_field = '', $have_time = 1 )
{
    global $req_data;
    $no_field_arr = array();
    if( ! empty($no_field) )
    {
        $no_field_arr = explode( ',', $no_field);
    }
    if( $no_field_arr )
    {
        foreach( $no_field_arr as $no_field_name )
        {
            unset( $req_data[$no_field_name] );
        }
    }
   
   	if( $have_time )
   	{
    	$req_data['add_time'] = time();
		$req_data['update_time'] = time();
   	}
   
    return $req_data;
}

//设置select值
function select_value( $value, $select_id )
{
	$html = '';
	if( $select_id )
	{
		$html = "<script type=\"text/javascript\">
						$('#{$select_id}').val('{$value}');
                    </script>";
	}
	echo $html;
}

function sort_by_value($array, $key)
{
	if( is_array( $array ) )
	{
		$key_array = null;  
		$new_array = null;  
		for( $i = 0; $i < count( $array ); $i++ )
		{
			$key_array[$array[$i][$key]] = $i;  
		}  
	
		ksort($key_array);  

		$j = 0;  
		
		foreach($key_array as $k => $v)
		{		
			$new_array[$j] = $array[$v];  
			$j++;  		
		}  

			unset($key_array);  		
			return $new_array;  
		
	}else
	{  
		
		return $array;  
		
	}  

}

function get_html( $url, $ssl = false)
{
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	if($ssl){
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	}
	$output = curl_exec( $ch );
	//p_r( curl_error($ch) );
	curl_close($ch);
	return $output;
}

function post_html( $url, $vars = '' )
{
	$ch=curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $vars ) );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	$content = curl_exec( $ch );
	curl_close( $ch );
	return $content;
}
	

function trim_strlen( $str )
{
	return strlen( trim( $str ) );
}

//字符串包含
function has_str( $subject, $search )
{
	$t = explode( $search, $subject );
	return count( $t ) > 1;
}

//截取指定开始结束字符串里的字符
function jequ_str( $str, $start_str, $end_str )
{
	$t = explode( $start_str, $str );
	$str = $t[1];
	$t = explode( $end_str, $str );
	$str = $t[0];
	return $str;
}

//使用触发器实现数据完整性约束
function use_trigger(){
	/*
	以下为可能用到的触发器
	触发器名字 	  	  				时间 	事件		触发器所在的表
	UpdateInventoryWhenInsert 	 	AFTER 	INSERT	@#@purchase_order_detail
	UpdateInventoryWhenUpdate 		AFTER 	UPDATE	@#@purchase_order_detail
	UpdateInventoryWhenDelete 	 	AFTER 	DELETE	@#@purchase_order_detail
	DeleteDetail 					BEFORE 	DELETE	@#@purchase_order
	*/
}

//把arr的主key换成指定的。。比如 $arr = array( 1=>array( 'id'=>100,'name'=>'a' ) ,  2=>array( 'id'=>101, 'name'=>'b' ) );
//change_main_key( $arr, 'id' )
//结果为 array( 100=>array( 'id'=>100,'name'=>'a' ) ,  101=>array( 'id'=>101, 'name'=>'b' ) )
//$option = array( 'key_strtoupper'=>1 /*key全转为大写*/, 'key_strtolower'=> 1/*key转为小写*/ )
function change_main_key( $arr, $main_key, $option = array() )
{
	$arr_result = array();
	foreach( $arr as $info )
	{
        if( $option['key_strtoupper'] )
        {
            $info[$main_key] = strtoupper( $info[$main_key] );
        }
        if( $option['key_strtolowwer'] )
        {
            $info[$main_key] = strtolower( $info[$main_key] );
        }
		$arr_result[$info[$main_key]] = $info;
	}
	return $arr_result;
}

function dectoupper($number){
	static $indexArr;
	static $valueArr;
	static $letterArr;
	
	$indexArr = array_merge(range(0,9),range('a','p'));
	$valueArr = range('A','Z');
	$letterArr = array_combine($indexArr,$valueArr);
	$number = strval(base_convert($number,10,26));
	$l = strlen($number);
	$retNum = '';
	for($i = 0;$i < $l;$i++){
		$retNum = $retNum.$letterArr[substr($number,$i,1)];
		
	}

	return $retNum;
}

//是不是开发人员
function is_kaifa()
{
	return USER_DEVELOPER == $_SESSION['admin_a'];
}

//获取汉字的首拼音
function get_char_sm( $str )
{
	$firstchar_ord = ord( strtoupper( $str{0} ) );
	if ( ( $firstchar_ord >= 65 and $firstchar_ord <= 91 ) or ( $firstchar_ord>=48 and $firstchar_ord <= 57 ) ) return $str{0};
	$s = iconv( "UTF-8", "gb2312", $str );
	$asc = ord($s{0}) * 256 + ord( $s{1} ) - 65536;
	if( $asc >= -20319 and $asc <= -20284)return "A";
	if( $asc >= -20283 and $asc <= -19776)return "B";
	if( $asc >= -19775 and $asc <= -19219)return "C";
	if( $asc >= -19218 and $asc <= -18711)return "D";
	if( $asc >= -18710 and $asc <= -18527)return "E";
	if( $asc >= -18526 and $asc <= -18240)return "F";
	if( $asc >= -18239 and $asc <= -17923)return "G";
	if( $asc >= -17922 and $asc <= -17418)return "H";
	if( $asc >= -17417 and $asc <= -16475)return "J";
	if( $asc >= -16474 and $asc <= -16213)return "K";
	if( $asc >= -16212 and $asc <= -15641)return "L";
	if( $asc >= -15640 and $asc <= -15166)return "M";
	if( $asc >= -15165 and $asc <= -14923)return "N";
	if( $asc >= -14922 and $asc <= -14915)return "O";
	if( $asc >= -14914 and $asc <= -14631)return "P";
	if( $asc >= -14630 and $asc <= -14150)return "Q";
	if( $asc >= -14149 and $asc <= -14091)return "R";
	if( $asc >= -14090 and $asc <= -13319)return "S";
	if( $asc >= -13318 and $asc <= -12839)return "T";
	if( $asc >= -12838 and $asc <= -12557)return "W";
	if( $asc >= -12556 and $asc <= -11848)return "X";
	if( $asc >= -11847 and $asc <= -11056)return "Y";
	if( $asc >= -11055 and $asc <= -10247)return "Z";
	return null;
}

function array_remove($array,$v){        // $array为操作的数组，$v为要删除的值
    foreach($array as $key=>$value){    
        if($value == $v){       //删除值为$v的项        
        unset($array[$key]);    //unset()函数做删除操作        
        }    
    }
    return $array;
}

/**
 * 获取ebay链接地址
 * @since 1.1.1
 * @param string $item_id ebay item id
 * @return string 地址
 */
function get_ebay_item_url( $item_id )
{
    return "http://www.ebay.com/itm/{$item_id}";
}

/**
 * 获取smt链接地址
 * @since 1.1.1
 * @param string $item_id ebay item id
 * @return string 地址
 */
function get_smt_item_url( $item_id )
{
    return "http://www.aliexpress.com/wholesale?SearchText={$item_id}";
}

function get_amazon_item_url( $item_id, $country )
{
	$qz_url = '';
	switch( $country )
	{
		case 'United States':
			$qz_url = 'http://www.amazon.com';
			break;
		case 'Canada':
			$qz_url = 'http://www.amazon.ca';
			break;
		case 'France':
			$qz_url = 'http://www.amazon.fr';
			break;
		case 'United Kingdom':
			$qz_url = 'http://www.amazon.co.uk';
			break;
		case 'Germany':
			$qz_url = 'http://www.amazon.de';
			break;
		default:
			$qz_url = 'http://www.amazon.com';
			break;
	}
	$url = "{$qz_url}/dp/{$item_id}";
	return $url;
}
function get_item_url_wish( $item_id )
{
	return "http://www.wish.com/c/{$item_id}";
}

/**
 * 特殊字符替换成空格
 * @since 1.1.1
 * @param string $str 替换的字符串
 * @return string $newstr
 */

function replace_empty( $str ){
    $pattern = '/\'/';
    $newstr = preg_replace($pattern,' ',$str,-1);
    return $newstr;
}
/**
 * 国家别名处理成系统指定的国家
 * @since 1.1.1
 * @param string $country 替换的字符串
 * @return string $newcountry 替换成的新国家
 */
function replace_country( $country )
{
	$country_bm = array();
    require_once( WEB_INCLUDE.'/define_user/country_bm.php' );
    if( array_key_exists( $country, $country_bm )){
        $new_country = $country_bm[$country];
    }else{
        $new_country = $country ; 
    }
       return $new_country; 
}
/**
 * 字符串长度截取
 * @since 1.1.1
 * @param string $str替换的字符串
 * @param int $leng 截取的长度
 * @return string $newstr 新字符串
 */
function str_length( $str,$length){
	if( strlen( trim($str))> $length ){
		$new_str = substr( $str,0,$length );
	}else{
		$new_str = trim( $str );
	}
	return $new_str;
} 
 
function get_upload_excel_content( $input_name )
{

    require_once( WEB_CLASS . '/class.upload.php' );
    $cls_upload = new cls_upload($input_name);
    $file_info = $cls_upload->upload( WEB_DR . "/uploads/", '', array() );
    //p_r($file_info);
    $excel_file = $file_info['filename'];
    if( empty( $excel_file ) )
    {
        return array( 'ack'=> 0, 'error_id'=> 1001, 'msg'=>'请excel文件' );
    }

    require_once( WEB_CLASS . '/class.excel.php' );
    $excel_file_path = WEB_DR."/uploads/" . $excel_file;
    $cls_excel = new cls_excel( $excel_file_path );
    $data = $cls_excel->read();
    if( count( $data ) == 0 )
    {
        return array( 'ack'=> 0, 'error_id'=> 1002, 'msg'=>'获取excel失败' );
        //show_msg( '数据获取失败', 2 );
    }else
    {
        return array( 'ack'=> 1, 'data'=> $data );
    }
}

function str_option( $str )
{
    return addslashes( trim( $str ) );
}

function get_num_sql( $sql )
{
	$arr = preg_split( '/from/i', $sql );
	if( $arr )
	{
		$arr[0] = 'select count(1) as cnum';
		$new_sql = implode( ' from ', $arr );
	}
	$new_sql = preg_replace( '/limit \d+\,\d+/i', '', $new_sql );
	$new_sql = preg_replace( '/limit \d+/i', '', $new_sql );
	return $new_sql;
}

//二维数组排序
function muti_arr_sort( $array, $key, $order = 'asc' )
{

	$arr_nums = $arr = array();
	foreach( $array as $k=> $v )
	{
		$arr_nums[$k] = $v[$key];
	}

	if( $order == 'asc' )
	{
		asort( $arr_nums );
	}else
	{
		arsort( $arr_nums );
	}

	foreach( $arr_nums as $k => $v )
	{
		$arr[$k] = $array[$k];
	}

	return $arr;
}

function get_platform_id_from_user_group( $user_group_id )
{
	$arr = array(
		USER_ADMIN => array( 'platfrom_id_list'=> '1,2,3,4,5,6' ),
		USER_CAIGOU => array( 'platfrom_id_list'=> '1,2,3,4,5,6' ),
		USER_SMT_SELLER => array( 'platfrom_id_list'=> '2' ),
		USER_CAIWU => array( 'platfrom_id_list'=> '1,2,3,4,5,6' ),
		USER_WEBSITE_SELLER => array( 'platfrom_id_list'=> '4' ),
		USER_EBAY_SELLER => array( 'platfrom_id_list'=> '1' ),
		USER_WISH => array( 'platfrom_id_list'=> '5' ),
		USER_AMAZON_SELLER => array( 'platfrom_id_list'=> '3' ),
		USER_DEVELOPER => array( 'platfrom_id_list'=> '1,2,3,4,5,6' ),
		USER_STOCK => array( 'platfrom_id_list'=> '1,2,3,4,5,6' ),
		USER_KEFU => array( 'platfrom_id_list'=> '1' ),
		USER_KAIFA => array( 'platfrom_id_list'=> '1,2,3,4,5,6' ),
		USER_SMT_KEFU => array( 'platfrom_id_list'=> '2' ),
	);
	return $arr[$user_group_id];
}
//获取年月份
function get_year_month_list($start_month='2016-01',$end_month='2017-12')
{
    $data_list = array();
    $start_month_time = strtotime( $start_month ); 
    $end_month_time   = strtotime( $end_month );
    $i = false; //开始标示
    while( $start_month_time < $end_month_time )
    {
      $new_month = !$i ? date('Y-m', strtotime('+0 Month', $start_month_time)) : date('Y-m', strtotime('+1 Month', $start_month_time));
      $start_month_time = strtotime( $new_month );
      $i = true;
	  $new_month = str_replace('-','',$new_month);
      $data_list[] = $new_month;
    }
    return $data_list;
}

function url_short($url)  
{  
    $url= crc32($url);      
    $result= sprintf("%u", $url);      
    return base62($result);  
 }

function base62($x)
{   
    $show = '';
    while($x > 0){
        $s = $x % 62;
        if($s > 35) {
            $s = chr($s + 61);
        } elseif($s > 9 && $s <= 35) {
        $s = chr($s + 55);
        }      
    $show .= $s;
    $x = floor($x / 62);
    }
    return $show;
}
//是不是外网
function check_is_out_net()
{
	$is_romate = false;
	$referer_url = $_SERVER['HTTP_REFERER'];
	if( strpos( $referer_url, '.mobile21cn' ) )
	{
		$is_romate = 1;
	}else
	{
		$is_romate = 0;
	}
	return $is_romate;
}
?>