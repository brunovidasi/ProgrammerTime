<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('strsql')) {

	function strsql($string = ""){

		if(empty($string)) return "";

		$CI =& get_instance();
		$string = $CI->db->escape_str((string) $string);

		return $string;
	}

}

?>