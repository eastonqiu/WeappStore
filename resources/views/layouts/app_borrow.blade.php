<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- <meta http-equiv="Cache-control" content="{if $_G['setting']['mobile'][mobilecachetime] > 0}{$_G['setting']['mobile'][mobilecachetime]}{else}no-cache{/if}" />  -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<!-- 开发环境使用不缓存策略 -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<!-- end 不缓存 -->
<meta name="format-detection" content="telephone=no" />
<title>充电宝租赁</title>
<link rel="stylesheet" href="/css/reset.css" type="text/css" media="all">
<link rel="stylesheet" href="/css/style.css" type="text/css" media="all">

<link rel="stylesheet" href="/css/mystyle.css?rand=<?php echo rand(1000,50000);?>">

<script src="/js/vendor/jquery-1.12.0.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
</script>
<script type="text/javascript">
function isWeiXin(){
	var ua = window.navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i) == 'micromessenger'){
		return true;
	}else{
		return false;
	}
}

	// var jsApiParameters = <?php echo empty($jsApiParameters)? 'null' : $jsApiParameters;?>;
    var jsApiParameters = 0;
	function jsApiCall() {
		jsApiParameters = eval('(' + jsApiParameters + ')');
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			jsApiParameters,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				if( res.err_msg == "get_brand_wcpay_request:ok" ) {
					//alert("{$orderid}\nPayment Succeed!");
					//$('#form_order').submit();
					// alert("支付成功，关闭本页面");
					// wxApiCloseWindow();
					var psurl = "/wxpay.php";
					window.location.href=psurl+"?app=mcs&mod=wxpay&paymod=index&act=pay_success";
				} else if ( res.err_msg == "get_brand_wcpay_request:cancel" ){
					// alert("您已放弃支付,谢谢!");
				} else if ( res.err_msg == "get_brand_wcpay_request:fail" ){
					alert("支付失败,请稍后再试,谢谢!");
				} else {
					alert("Error:" + res.err_code);
				}
			}
		);
	}

	function callpay() {
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		    return;
		}
	}

	function wxApiCloseWindow() {
		WeixinJSBridge.invoke('closeWindow',{},function(res){
		    if(res.err_msg == "close_window:error") {
		    	alert("关闭微信网页错误，请稍后重试，谢谢");
		    }
		});
	}

	function callCloseWindow() {
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', wxApiCloseWindow, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', wxApiCloseWindow);
		        document.attachEvent('onWeixinJSBridgeReady', wxApiCloseWindow);
		    }
		}else{
			wxApiCloseWindow();
			return;
		}
	}
</script>

</head>

<body>
    @yield('content')
</body>
</html>
