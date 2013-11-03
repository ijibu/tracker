<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP抓包程序</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<script type="text/javascript" src="<?php echo JS_PATH ?>jquery.min.js"></script>
<script type="text/javascript">
	function jsonpCallback78280(data) {
		//data = $.parseJSON(data);

		var result = data.result, cont = result.length, code = '';
		for (var i=0; i < cont; i++) {
			code += ',' + result[i].PRODUCTID;
		}

		alert(code);
	}
</script>
<script type="text/javascript" src="<?php echo JS_PATH ?>shang-b.js"></script>
</head>

<body>
已经抓包：<div id="ipCount">
0
</div>条。
</body>
</html>