<?php

if (! defined('BASEPATH'))
	exit('No direct script access allowed');

class Smiplehtml
{

	protected $dom;

	public function __construct()
	{
		require_once ('simple_html_dom.php');
		
		$this->dom = new simple_html_dom();
	}
	
	// helper functions
	// -----------------------------------------------------------------------------
	// get html dom form file
	function file_get_html()
	{
		$args = func_get_args();
		$this->dom->load(call_user_func_array('file_get_contents', $args), true);
		return $this->dom;
	}
	
	// get html dom form string
	function str_get_html($str, $lowercase = true)
	{
		$this->dom->load($str, $lowercase);
		return $this->dom;
	}
	
	// dump html dom tree
	function dump_html_tree($node, $show_attr = true, $deep = 0)
	{
		$lead = str_repeat('    ', $deep);
		echo $lead . $node->tag;
		if ($show_attr && count($node->attr) > 0) {
			echo '(';
			foreach ($node->attr as $k => $v)
				echo "[$k]=>\"" . $node->$k . '", ';
			echo ')';
		}
		echo "\n";
		
		foreach ($node->nodes as $c)
			$this->dump_html_tree($c, $show_attr, $deep + 1);
	}
	
	// get dom form file (deprecated)
	function file_get_dom()
	{
		$args = func_get_args();
		$this->dom->load(call_user_func_array('file_get_contents', $args), true);
		return $this->dom;
	}
	
	// get dom form string (deprecated)
	function str_get_dom($str, $lowercase = true)
	{
		$this->dom->load($str, $lowercase);
		return $this->dom;
	}
}
 