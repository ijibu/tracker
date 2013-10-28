<?php
/**
 * desc模型
 */
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Transaction_Log_Model extends MY_Model 
{
	/**
	 * Specify the primary table to execute queries on
	 *
	 * @var string
	 */
	protected $primary_table = 'transaction_log';
	
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
	
	/**
	 * 获取股票交易的历史记录
	 * @param string $code
	 */
	public function getLogByCode($code)
	{
		return $this->get(array('stockCode' => $code));
	}
	
}