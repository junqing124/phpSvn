<?php

defined('IN_DCR') or exit('No permission.'); 

/**
 * 安全类
 * ===========================================================
 * 版权所有 (C) 2006-2020 我不是稻草人，并保留所有权利。
 * 网站地址: http://www.dcrcms.com
 * ----------------------------------------------------------
 * 这是免费开源的软件；您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * 不允许对程序修改后再进行发布。
 * ==========================================================
 * @author:     我不是稻草人 <junqing124@126.com>
 * @version:    v1.0
 * @package class
 * @since 1.1.4
*/

class cls_safe
{
   
    /**
     * 去掉变量里的HTML
     * @param object $var 要去掉html的变量,可以是多维数组或字符串
     * @return object 返回去掉html后的变量
     */
    static function no_html( $var )
    {
        if( is_array( $var ) )
        {
            for( $i = 0; $i < count( $var ); $i ++ )
            {
                if( is_array( $var[$i] ) )
                {
                    $var[$i] = cls_safe:: no_html( $var[$i] );
                }else
                {
                    $var[$i] = strip_tags( $var[$i] );
                }
            }
            return $var;
        }
        if( is_string( $var ) )
        {
            return strip_tags( $var );
        }
    }
}

?>