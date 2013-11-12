<?php
	/**
	* 把一个文件夹里的文件全部转码 只能转一次 否则全部变乱码
	* @param string $filename
	*/
	function iconv_file($filename, $input_encoding='gbk', $output_encoding='utf-8')
	{
		if(file_exists($filename)) {
			if(is_dir($filename)) {
				foreach (glob("$filename/*") as $key=>$value) {
					iconv_file($value);
				}
			} else {
				$contents_before = file_get_contents($filename);
				/*$encoding = mb_detect_encoding($contents_before,array('CP936','ASCII','GBK','GB2312','UTF-8'));
				echo $encoding;
				if($encoding=='UTF-8') mb_detect_encoding函数不工作
				{
				return;
				}*/
				$contents_after = iconv($input_encoding, $output_encoding, $contents_before);
				file_put_contents($filename, $contents_after);
			}
		} else {
			echo '参数错误';
			return false;
		}
	}
