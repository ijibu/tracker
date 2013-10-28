<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MY_Input extends CI_Input {

	/**
	 * Constructor
	 *
	 * Sets whether to globally enable the XSS processing
	 * and whether to allow the $_GET array
	 *
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Fetch from array
	 *
	 * This is a helper function to retrieve values from global arrays
	 *
	 * @access	private
	 * @param	array
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE) {
		if (!isset($array[$index])) {
			return FALSE;
		}

		//自定义规则（把单引号转义）
		$str = str_replace("'", "’", $array[$index]);

		if ($xss_clean === TRUE) {
			return $this->security->xss_clean($str);
		}

		return $str;
	}

}