<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Main extends MY_Controller {

	public function __construct() 
	{
		parent::__construct();
		ini_set("max_execution_time", 0);
	}
	
	/**
	 * 显示新浪IP查询接口页面
	 *
	 * @author ijibu.com@gmail.com
	 */
	public function index()
	{
		//$this->output->enable_profiler(TRUE);
		$data = array();
		
 		$code = trim($this->input->get_post('code'));
 		if (!$code) {
 			$code = '600601';
 		}
 		
 		$this->load->model('stock_model');
 		$tmp = $this->stock_model->get(array('code' => $code));
 		$stockCode = array_shift($tmp);
 		$stockCodes = $this->getStockCodes();
 		
		$this->load->model('transaction_log_model');
		$data = $this->transaction_log_model->getLogByCode($code);
		//print_r($data);exit;
		$this->load->view('k', array('data' => $data, 'stockCode' => $stockCode, 'stockCodes' => $stockCodes));
	}
	
	/**
	 * 获取上证的所有股票代码
	 */
	public function getShangTickers()
	{
		$file = APPPATH . 'cache/shang.html';
		$outPutFile = APPPATH . 'cache/shang.php';
		$outPutFileIni = APPPATH . 'cache/shang.ini';
		$conts = file_get_contents($file);
		
		//获取a标签的内容。
		$sContents = strip_tags($conts);
		$aContents = explode(')', $sContents);
		$aContents1 = array();
		foreach ($aContents as $val) {
			$row = array();
			$row = explode("(", trim($val));
			if (isset($row[1])) {
				$aContents1["sh_$row[1]"] = $row;
				error_log("$row[1]\n", 3, $outPutFileIni);
			}
		}
		//echo count($aContents1);exit;
		$data = var_export($aContents1, true);
		file_put_contents($outPutFile, "<?php \r\n return $data;?>");
	}
	
	/**
	 * 获取深证的所有股票代码
	 */
	public function getShenTickers()
	{
		$file = APPPATH . 'cache/shen.html';
		$outPutFile = APPPATH . 'cache/shen.php';
		$outPutFileIni = APPPATH . 'cache/shen.ini';
		$conts = file_get_contents($file);
	
		//获取a标签的内容。
		$sContents = strip_tags($conts);
		$aContents = explode(')', $sContents);
		$aContents1 = array();
		foreach ($aContents as $val) {
			$row = array();
			$row = explode("(", trim($val));
			if (isset($row[1])) {
				$aContents1["sz_$row[1]"] = $row;
				error_log("$row[1]\n", 3, $outPutFileIni);
			}
		}
		//echo count($aContents1);exit;
		$data = var_export($aContents1, true);
		file_put_contents($outPutFile, "<?php \r\n return $data;?>");
	}
	
	/**
	 * 获取上证的所有股票的交易数据
	 */
	public function getShangTickerTables()
	{
		//http://table.finance.yahoo.com/table.csv?s=600000.ss
		$baseUrl = "http://table.finance.yahoo.com/table.csv?s=";
		$file = APPPATH . 'cache/shang.php';
		$outPutBaseFile = APPPATH . 'cache/data/sh/';
		$erro_file = APPPATH . 'cache/erro.log';
		
		$data = include $file;
		
		foreach ($data as $row) {
			$url = $baseUrl . $row[1] . '.ss';
			$content = @file_get_contents($url);
			if (!$content) {
				error_log("ss:$row[1]	get failed;\r\n", 3, $erro_file);
			} else {
				file_put_contents($outPutBaseFile . $row[1] . '.csv', $content);
			}
		}
	}
	
	/**
	 * 初试添加股票
	 */
	public function initAddStocks()
	{
		$exchanges = array('shang' => 1, 'shen' => 2);
		$this->load->model('stock_model');
		
		foreach ($exchanges as $type => $exchange) {
			$file = APPPATH . "cache/{$type}.php";
			$data = include $file;
				
			foreach ($data as $row) {
				$stock = array();
				$stock['code'] = "{$row[1]}";
				$stock['name'] = $row[0];
				$stock['exchange'] = $exchange;
				$this->stock_model->add($stock);
			}
		}
	}
	
	/**
	 * 初始化添加交易日志
	 */
	public function initAddTransationLog()
	{
		$dir = APPPATH . "cache/data/sz";
		if (($dh = opendir($dir)) == true) {
			while (($file = readdir($dh)) !== false) {
				if(!is_dir($dir."/".$file) && $file!="." && $file!="..") {
					$content = '';
					$fileName = explode('.', $file);
					$code = $fileName[0];
					$filePath =  $dir."/".$file;
					$content = file_get_contents($filePath);
					$data = explode("\n", $content);
					array_shift($data);
					
					$count = count($data);
					for ($i = $count - 1; $i >= 0; $i -=500) {		//将csv文件中的数据后面的先放入数据库。
						$sql = "INSERT INTO transaction_log(stockCode, dateTime, openPrice, highPrice, lowPrice, closePrice, adjClosePrice, volume) VALUES ";
						$inserts = array();
							
						for ($j = 0; $j < 500; $j++) {
							if (isset($data[$i - $j]) && $data[$i - $j]) {
								$row = $data[$i - $j];
								$row = explode(',', $row);
								if (count($row) != 7) {
									error_log("{$code}_{$data[$i - $j]};\r\n", 3, APPPATH . 'cache/inserteror.sql');
									continue;
								} 
									
								$inserts[] = "('{$code}', '{$row[0]}', {$row[1]}, {$row[2]}, {$row[3]}, {$row[4]}, {$row[6]}, {$row[5]})";
							} else {
								continue;
							}
						}
							
						if ($inserts) {
							$sql .= implode(',', $inserts) . ";\r\n";
							$this->db->query($sql);
							//error_log($sql, 3, APPPATH . 'cache/insertlog.sql');
						}
					}
				}
			}
			closedir($dh);
		}
	}
	
	/**
	 * 批量添加所有股票某天的交易记录
	 */
	public function addTransationLog()
	{
		$startDate = trim($this->input->get_post('date'));
		if (!$startDate) {
			$startDate = date('Y-m-d', strtotime('-1 days'));
		}
		$startDate = explode('-', $startDate);
		
		//$url ="http://ichart.yahoo.com/table.csv?s=600000.SS&a=08&b=25&c=2010&d=09&e=8&f=2010&g=d";
		$startMonth = intval($startDate[1]) - 1;
		$url ="http://ichart.yahoo.com/table.csv?s=%s.%s&a={$startMonth}&b={$startDate[2]}&c={$startDate[0]}&d={$startMonth}&e={$startDate[2]}&f={$startDate[0]}&g=d";
		$opts = array(
  			'http'=>array(
    			'method'=>"GET",
  				'timeout'=>60,
  			)
		);
		$context = stream_context_create($opts);
		
		$this->load->model('stock_model');
		$stocks = $this->stock_model->get(array('status' => 1));
		foreach ($stocks as $stock) {
			if ($stock['exchange'] == 1) {
				$exchange = 'SS';
			} else {
				$exchange = 'SZ';
			}
			$code = $stock['code'];
			$dataUrl = sprintf($url, $code, $exchange);
			//echo $dataUrl;
			$content = @file_get_contents($dataUrl, false, $context);
			//var_dump($content);exit;
			error_log($code . '	result:' . $content, 3, APPPATH . 'cache/getcode.log');
			if ($content) {			//没法排除404的情况
				$data = explode("\n", $content);
				array_shift($data);
				$count = count($data);
					
				for ($i = 0; $i < $count; $i++) {
					$sql = "INSERT INTO transaction_log(stockCode, dateTime, openPrice, highPrice, lowPrice, closePrice, adjClosePrice, volume) VALUES ";
					$inserts = array();
						
					$row = $data[$i];
					$row = explode(',', $row);
					if (count($row) != 7) {
						continue;
					}
						
					$inserts[] = "('{$code}', '{$row[0]}', {$row[1]}, {$row[2]}, {$row[3]}, {$row[4]}, {$row[6]}, {$row[5]})";
						
					if ($inserts) {
						$sql .= implode(',', $inserts) . ";\r\n";
						//$this->db->query($sql);
						error_log($sql, 3, APPPATH . "cache/addTransationLog_" . date('Y-m-d') . ".sql");
					}
				}
			} else {	//get错误的情况。
				$this->stock_model->update(array('id' => $stock['id'], 'status' => 2));
			}
		}
		
		echo 'sucess';
		
		//print_r($stocks);exit;
		
// 		$log = array();
// 		$log['stockCode'] = $code;
// 		$log['dateTime'] = $row[0];
// 		$log['openPrice'] = $row[1];
// 		$log['highPrice'] = $row[2];
// 		$log['lowPrice'] = $row[3];
// 		$log['closePrice'] = $row[4];
// 		$log['adjClosePrice'] = $row[6];
// 		$log['volume'] = $row[5];
	}
	
	/**
	 * 获取股票代码
	 */
	protected function getStockCodes()
	{
		$cacheFile = APPPATH . 'cache/stockCodes.php';
		if (file_exists($cacheFile)) {
			return include $cacheFile;
		} else {
			$this->load->model('stock_model');
			$stocks = $this->stock_model->get(array('status' => 1));
			$data = var_export($stocks, true);
			file_put_contents($cacheFile, "<?php  return $data;?>");
			return $stocks;
		}
		
		
	}
}