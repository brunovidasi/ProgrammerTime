<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('strsql')) {

	function strsql($string = ""){

		if(empty($string)) return "";

		$string = str_replace("'", "\'", $string);

		$CI =& get_instance();
		$string = mysqli_real_escape_string($CI->db->conn_id, $string);

		return $string;
	}

}

?>