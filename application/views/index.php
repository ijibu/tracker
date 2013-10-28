<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IP抓包程序</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<script type="text/javascript" src="<?php echo JS_PATH ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH ?>startip<?php echo $step ?>.js"></script>
<script type="text/javascript">
	var Ijibu = {}, step = <?php echo $step ?>, maxStep = 21, cookieName = 'getLen' + step;

	//获取cookie
	Ijibu.getCookie = function(name) {
		name = name.replace(/([\.\[\]\$])/g, "\\$1");
		var rep = new RegExp(name + "=([^;]*)?;", "i");
		var co = document.cookie + ";";
		var res = co.match(rep);
		if (res) {
			return res[1] || ""
		} else {
			return ""
		}
	};
	
	//设置cookie
	Ijibu.setCookie = function(name, value, expire, path, domain, secure) {
		var cstr = [];
		cstr.push(name + "=" + escape(value));
		if (expire) {
			var dd = new Date();
			var expires = dd.getTime() + expire * 3600000;
			dd.setTime(expires);
			cstr.push("expires=" + dd.toGMTString())
		}
		if (path) {
			cstr.push("path=" + path)
		}
		if (domain) {
			cstr.push("domain=" + domain)
		}
		if (secure) {
			cstr.push(secure)
		}
		document.cookie = cstr.join(";")
	};
	
	//删除cookie
	Ijibu.deleteCookie = function(name) {
		document.cookie = name + "=;expires=Fri, 31 Dec 1999 23:59:59 GMT;"
	};

	var ipCount = ips.length, i = parseInt(Ijibu.getCookie(cookieName));
	if (isNaN(i)) {
		i = 0;
	}
	
	/**
	 * 获取IP地址，每次获取5个，先不考虑并行出错的情况。
	 */
	function getIp() {
		if (i >= ipCount) {
			if (step < maxStep) {		//执行完了一个IP库后，自动执行下一个IP库。
				step++;
				//10秒钟后调用下一步。
				setTimeout("window.location.href='/main/index?step=" + step + "'", 10000);
			}
			clearInterval(getIpInt);
			return;
		}
		
		for (var j = 0; j < 2; j++) {
			i++;
			if (typeof ips[i] == 'undefined') {		//防止数组越界
				break;
			}
			
			/**
			var ip = ips[i], url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' + ip;
			//由于是异步操作，会使得回调函数中的ip被重写，所以会使得发布到后台去的ip连续5次是一样的bug。
			$.getScript(url, function(){
				if (typeof remote_ip_info != 'undefined') {
					if (remote_ip_info.ret == 1) {
						remote_ip_info.ip = ip;
						$.ajax({
							type: "POST",
							url: '/main/syncIpName',
							data: remote_ip_info,
							//dataType: "json",
							success: function(json) {
								//clearInterval(getIpInt);
							},
							error: function(XMLHttpRequest, textStatus) {
								//alert(textStatus);
								//clearInterval(getIpInt);
							}
						});
					}
				}
			});
			*/
			
			var ip = ips[i];
			getSinaIp(ip);
			
		}
		
		Ijibu.setCookie(cookieName, i, 24 * 3, '/');
		$('#ipCount').html(i);
	}

	function getSinaIp(ip) {
		var url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' + ip;
		
		$.getScript(url, function(){
			if (typeof remote_ip_info != 'undefined') {
				if (remote_ip_info.ret == 1) {
					remote_ip_info.ip = ip;
					$.ajax({
						type: "POST",
						url: '/main/syncIpName',
						data: remote_ip_info,
						//dataType: "json",
						success: function() {
							//clearInterval(getIpInt);
						},
						error: function(XMLHttpRequest, textStatus) {
							//alert(textStatus);
							//clearInterval(getIpInt);
						}
					});
				}
			} else {
				sendErrors(ip);
				//clearInterval(getIpInt);
			}
		});	
	}
	
	/**
	 * 发送获取IP地址错误的记录
	 */
	function sendErrors(ip) {
		$.ajax({
			type: "POST",
			url: '/main/getIpErrors',
			data: {errorIp: ip}
		});
	}
	
	var getIpInt = setInterval('getIp()', 400);
</script>
</head>

<body>
已经抓包：<div id="ipCount">
0
</div>条。
</body>
</html>