<?php
/**
 * desc模型
 */
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Company_Model extends MY_Model 
{
	/**
	 * Specify the primary table to execute queries on
	 *
	 * @var string
	 */
	protected $primary_table = 'company_info';
	
	/**
	 * Set the primary key for the table
	 *
	 * @var string
	 */
	protected $primary_key = 'id';
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	
}