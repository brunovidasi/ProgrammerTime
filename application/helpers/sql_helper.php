<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('strsql')) {

	function strsql($string = ""){

		if(empty($string)) return "";

		$string = str_replace("'", "\'", $string);
		$string = mysql_real_escape_string($string);

		return $string;
	}

}

?>