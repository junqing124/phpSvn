<?php

defined( 'IN_DCR' ) or exit( 'No permission.' );

/**
 * 分页类
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
class cls_page
{
    private $url;
    private $cpage;
    private $total_page;
    private $tpl;
    private $web_url_module;

    /**
     * 构造函数
     * 模板说明：{index}表示首页 {pagelist}链接列表 {option}下拉列表框 {next}下一页 {pre}上一页 {cur}当前页 {index=首页}表示首页的链接文字为首页，即=号后为链接文字，不过这对{pagelist}{option}无效
     * @param string $cpage 当前页
     * @param string $tatolPage 总页数
     * @param string $tpl 模板.
     * @param string $url 要分页的url 默认为当前页
     * @return PageClass
     */
    function __construct( $cpage, $total_page, $tpl = '', $url = '' )
    {
        $this->cpage = $cpage;
        $this->total_page = $total_page;
        if( strlen( $tpl ) == 0 )
        {
            $this->tpl = "{cur=当} {index=首} {pre=上} {next=下} {end=最} {option}"; //中文分页
        } else
        {
            $this->tpl = $tpl;
        }

        if( strlen( $url ) == 0 )
        {
            //得出地址
            $top_url = '';
            //从HTTP_REFFRER获取主url
            $referer_url = $_SERVER['HTTP_REFERER'];
			//p_r( $_SERVER );
            $referer_url_arr = explode( '/', $referer_url );
            //p_r( $referer_url_arr );
            $top_url = $referer_url_arr[0] . '//' . $referer_url_arr[2];
            //var_dump( $top_url );
            $this->url = $top_url . $_SERVER["REQUEST_URI"];
        } else
        {
            $this->url = $url;
        }
		$this->url = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $this->web_url_module = 1;
    }

    /**
     * 返回生成的分页HTML
     * @return string
     */
    function show_page()
    {
        //显示分页
        $url_option = array();//url的后缀如：?page=1&typeid=1
        $parse_url = parse_url( $this->url );
        if( !empty( $_SERVER["SERVER_PORT"] ) )
        {
            $url_main = 'http://' . $parse_url['host'] . ':' . $parse_url['port'] . $parse_url['path'];
        } else
        {
            $url_main = 'http://' . $parse_url['path'];
        }
		//echo $url_main;
        if( $parse_url['query'] )
        {
            //url有参数
            $url_arr = preg_split( '/&/', $parse_url['query'] );
            if( is_array( $url_arr ) )
            {
                foreach( $url_arr as $key => $value )
                {
                    $c = preg_split( '/=/', $value );
                    if( $c[0] == page )
                    {
                    } else
                    {
                        array_push( $url_option, $c[0] . '=' . $c[1] );
                    }
                }
            }
        } else
        {
        }

        if( is_array( $url_option ) )
        {
            $url_optionStr_t = implode( '&', $url_option );
        }
        if( strlen( $url_optionStr_t ) > 0 )
        {
            $url_optionStr .= '&' . $url_optionStr_t;
        }

        $tpl_content = $this->tpl;//分页模板
        $page_html = $tpl_content;

        //首页
        if( preg_match_all( '/\{index=([^}]*+)\}/', $tpl_content, $matches ) )
        {
            if( $this->web_url_module == '1' )
            {
                $new_url = '';
                $new_url = $url_main . '?page=1' . $url_optionStr;
            } else if( $this->web_url_module == '2' )
            {
                $new_url = '';
                $t_arr = array();
                $t_file_arr = array();
                $url_main = preg_replace( '/_p_(\d+)/', '', $url_main );
                $t_arr = parse_url( $url_main );
                $t_file_arr = explode( '.', $t_arr['path'] );
                $new_url = $t_arr[0] . $t_file_arr[0] . '_p_1.' . $t_file_arr[1];
            }
            $t_tpl = $matches[0][0]; //模板内容
            $t_word = $matches[1][0]; //分页字段
            $index_str = '<a href="' . $new_url . '">' . $t_word . '</a>';
            $page_html = str_replace( $t_tpl, $index_str, $page_html );
        }

        //当前页
        if( preg_match_all( '/\{cur=([^}]*+)\}/', $tpl_content, $matches ) )
        {
            $t_tpl = $matches[0][0];
            $t_word = $matches[1][0];
            $cur_str = $t_word . $this->cpage . '/' . $this->total_page;
            $page_html = str_replace( $t_tpl, $cur_str, $page_html );
        }

        //末页
        if( preg_match_all( '/\{end=([^}]*+)\}/', $tpl_content, $matches ) )
        {
            //这里判断 如果总页数为0 则最后页设置为1
            $total_page = $this->total_page == 0 ? 1 : $this->total_page;
            if( $this->web_url_module == '1' )
            {
                $new_url = '';
                $new_url = $url_main . '?page=' . $total_page . $url_optionStr;
            } else if( $this->web_url_module == '2' )
            {
                $new_url = '';
                $t_arr = array();
                $t_file_arr = array();
                $url_main = preg_replace( '/_p_(\d+)/', '', $url_main );
                $t_arr = parse_url( $url_main );
                $t_file_arr = explode( '.', $t_arr['path'] );
                $new_url = $t_arr[0] . $t_file_arr[0] . '_p_' . $total_page . '.' . $t_file_arr[1];
            }
            $t_tpl = $matches[0][0];
            $t_word = $matches[1][0];
            $end_page = '<a href="' . $new_url . '">' . $t_word . '</a>';
            $page_html = str_replace( $t_tpl, $end_page, $page_html );
        }

        //上一页
        if( preg_match_all( '/\{pre=([^}]*+)\}/', $tpl_content, $matches ) )
        {
            $t_tpl = $matches[0][0];
            $t_word = $matches[1][0];
            if( $this->cpage != 1 )
            {
                if( $this->web_url_module == '1' )
                {
                    $new_url = '';
                    $new_url = $url_main . '?page=' . ( $this->cpage - 1 ) . $url_optionStr;
                } elseif( $this->web_url_module == '2' )
                {
                    $new_url = '';
                    $t_arr = array();
                    $t_file_arr = array();
                    $url_main = preg_replace( '/_p_(\d+)/', '', $url_main );
                    $t_arr = parse_url( $url_main );
                    $t_file_arr = explode( '.', $t_arr['path'] );
                    $new_url = $t_arr[0] . $t_file_arr[0] . '_p_' . ( $this->cpage - 1 ) . '.' . $t_file_arr[1];
                }
                $pre_page = '<a href="' . $new_url . '">' . $t_word . '</a>';
            } else
            {
                $pre_page = $t_word;
            }
            $page_html = str_replace( $t_tpl, $pre_page, $page_html );
        }

        //下一页
        if( preg_match_all( '/\{next=([^}]*+)\}/', $tpl_content, $matches ) )
        {
            $t_tpl = $matches[0][0];
            $t_word = $matches[1][0];
            if( $this->cpage != $this->total_page && $this->total_page > 1 )
            {
                if( $this->web_url_module == '1' )
                {
                    $new_url = '';
                    $new_url = $url_main . '?page=' . ( $this->cpage + 1 ) . $url_optionStr;
                } else if( $this->web_url_module == '2' )
                {
                    $new_url = '';
                    $t_arr = array();
                    $t_file_arr = array();
                    $url_main = preg_replace( '/_p_(\d+)/', '', $url_main );
                    $t_arr = parse_url( $url_main );
                    $t_file_arr = explode( '.', $t_arr['path'] );
                    $new_url = $t_arr[0] . $t_file_arr[0] . '_p_' . ( $this->cpage + 1 ) . '.' . $t_file_arr[1];
                }
                $next_page = ' <a href="' . $new_url . '">' . $t_word . '</a>';
            } else
            {
                $next_page = $t_word;
            }
            $page_html = str_replace( $t_tpl, $next_page, $page_html );
        }

        //链接列表
        if( preg_match( "{pagelist}", $tpl_content ) )
        {
            $start = 1;
            $end = $this->total_page;
            if( $this->web_url_module == '1' )
            {
                if( $this->total_page > 10 )
                {
                    if( $this->cpage > 5 )
                    {
                        //echo $this->total_page;
                        if( $this->cpage + 10 > $end )
                        {
                            $start = $this->cpage - ( 10 - ( $this->total_page - $this->cpage ) );
                            $end = $end;
                        } else
                        {
                            $start = $this->cpage - 5;
                            $end = $start + 10;
                        }
                        //$start = ( $start + 10 > $end ) ? ( $this->cpage - ( 10 - ($this->total_page - $this->cpage) ) ) : $this->cpage - 5;
                        //echo $start;
                        //$end = ( $start + 10 > $end ) ? $end : $start + 10;
                        //echo $end;
                    } else
                    {
                        $start = 1;
                        $end = 11;
                    }
                }/*else
                {
                    $start = 1;
                    $end = $this->total_page;
                }*/
                for( $j = $start; $j <= $end; $j ++ )
                {
                    $page_list_url = $url_main . '?page=' . $j . $url_optionStr;
                    if( $j == $this->cpage )
                    {
                        $link_page .= ' <a class="current" href="' . $page_list_url . '">' . $j . '</a>';
                    } else
                    {
                        $link_page .= ' <a href="' . $page_list_url . '">' . $j . '</a>';
                    }
                }
            } else if( $this->web_url_module == '2' )
            {
                $new_url = '';
                $t_arr = array();
                $t_file_arr = array();
                $url_main = preg_replace( '/_p_(\d+)/', '', $url_main );
                $t_arr = parse_url( $url_main );
                $t_file_arr = explode( '.', $t_arr['path'] );
                $new_url = $t_arr[0] . $t_file_arr[0] . '_p_' . $i . '.' . $t_file_arr[1];
                $link_page .= ' <a href="' . $new_url . '">' . $i . '</a>';
            }
            $page_html = str_replace( '{pagelist}', $link_page, $page_html );
        }

        //下拉框分页
        if( preg_match( "{option}", $tpl_content ) )
        {
            $option_page = '<select onchange="javascript:window.location=' . "'" . $url_main . "?page='+this.options[this.selectedIndex].value+" . "'$url_optionStr'" . ';">';
            for( $i = 1; $i < $this->total_page + 1; $i ++ )
            {
                if( $i == $this->cpage )
                {
                    $option_page .= "<option selected='selected' value='$i'>第" . $i . "页</option>\n";
                } else
                {
                    $option_page .= "<option value='$i'>第" . $i . "页</option>\n";
                }
            }
            $option_page .= '</select>';
            $page_html = str_replace( '{option}', $option_page, $page_html );
        }
        //下拉框分页 数量少的 123....456...9 10 11这样的分页结构
        if( preg_match( "{option_short}", $tpl_content ) )
        {
            $option_page = '<select onchange="javascript:window.location=' . "'" . $url_main . "?page='+this.options[this.selectedIndex].value+" . "'$url_optionStr'" . ';">';
            for( $i = 1; $i < 4; $i ++ )
            {
                switch( $i )
                {
                    case 1:
                        $start = 1;
                        $end = 5;
                        break;
                    case 2:
                        if( $this->cpage > 5 )
                        {
                            if( $this->total_page - 5 > $this->cpage )
                            {
                                $start = $this->cpage;
                                $end = $this->cpage + 6;
                            } else
                            {
                                $start = 0;
                                $end = - 1;
                            }
                        } else
                        {
                            $start = 6;
                            $end = 6 + abs( 6 + $this->cpage - 8 );
                        }
                        break;
                    case 3:
                        $start = $this->total_page - 5;
                        $end = $this->total_page;
                        break;
                }
                for( $j = $start; $j <= $end; $j ++ )
                {
                    if( $j == $this->cpage )
                    {
                        $option_page .= "<option selected='selected' value='$i'>第" . $j . "页</option>\n";
                    } else
                    {
                        $option_page .= "<option value='$j'>第" . $j . "页</option>\n";
                    }
                }

            }
            $option_page .= '</select>';
            $page_html = str_replace( '{option_short}', $option_page, $page_html );
        }

        //输入框分页
        if( preg_match( "{input_box}", $tpl_content ) )
        {
            $option_page = "<input style='padding:0;width:40px;' type='text' value='{$this->cpage}' id='page_input_box'>";
            $option_page .= "<input style='padding:0;' type=\"button\" value=\"更换\" onclick=\"javascript:window.location='{$url_main}?page='+document.getElementById('page_input_box').value+'{$url_optionStr}';\">";

            $page_html = str_replace( '{input_box}', $option_page, $page_html );
        }

        return $page_html;
    }
}

?>
