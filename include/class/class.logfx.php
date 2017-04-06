<?php

defined('IN_DCR') or exit('No permission.'); 

class cls_logfx
{
	private $xml;
    function __construct( $xml )
    {
		$this->xml = $xml;
    }
	
	function formate_version_log()
	{
		$list = array();
		foreach( $this->xml as $info )
		{
			$info_arr = (array)$info;
			$info_t = array();
			$info_t['revision'] = $info_arr['@attributes']['revision'];
			$info_t['author'] = (string)$info->author;
			$info_t['date'] = (string)$info->date;
			$info_t['msg'] = (string)$info->msg;
			array_push( $list, $info_t );
		}
		return $list;
	}
}
?>