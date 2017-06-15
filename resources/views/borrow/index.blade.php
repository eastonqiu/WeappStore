@extends('layouts.app_borrow')

@section('content')
<style>body{ background: #eee;}</style>
<div class="shop-pay-main">
	<div class="shop-pay-content">
		<div class="icon-shopmoney">
		<!--{if $platform == PLATFORM_WX}-->
			<h4><img src="static/images/icon-shopMoney.png"/></h4>
		<!--{else}-->
			<!--支付宝图标-->
			<h4><img src="static/images/alipay.png" style="border:none;"/></h4>
		<!--{/if}-->
		</div>
		<div class="pay-text">
		<!--{if $platform == PLATFORM_WX}-->
			<!--{if $usablemoney > 0}-->
				<h4>您的押金余额为<em>{$usablemoney}</em>元</h4>
			<!--{/if}-->
			<!--{if round($usablemoney,2) >= round($costPrice - BATTERY_DEPOSIT_DIFF, 2)}-->
				<h3>可直接租借</h3>
			<!--{elseif $usablemoney == 0}-->
				<span>需支付押金</span>
				<h2>{$costPrice}元</h2>
			<!--{else}-->
				<h3>需要补足押金<em>{eval echo round($costPrice-$usablemoney,2);}</em>元</h3>
			<!--{/if}-->
		<!--{else}-->
			<!--支付宝状态-->
			<h3>信用借还&nbsp;&nbsp;免押金借用充电宝</h3>
		<!--{/if}-->

		</div>
		<!--余额借用内容显示-->
		<div class="lease-pay-btn">
		<h4>充电宝自带充电线，请选择所需的接口类型</h4>
		<!--{loop $batteryCat $item}-->
			<!--{loop $battery_count $cableType $cableCount}-->
				<!--{if $cableType == APPLE_ANDROID_CABLE}-->
					<!--{if $cableCount > 0}-->
						<!--{if $platform != PLATFORM_ALIPAY}-->
						  <a onclick="paydirect2({$item['tid']}, {BATTERY_DEFAULT_FLAVOR}, {APPLE_ANDROID_CABLE})" href="javascript:;">
						  	<i class="icon-iphone"></i>苹果/安卓
						  </a>
						<!--{else}-->
						  <a onclick="paydirect2({$item['tid']}, {BATTERY_DEFAULT_FLAVOR}, {APPLE_ANDROID_CABLE}, true)" href="javascript:;"><i class="icon-iphone"></i>苹果/安卓</a>
						<!--{/if}-->
					<!--{else}-->
						  <a href="javascript:;" class="shop-pay-disabled"><i class="icon-iphone"></i>苹果/安卓</a>
					<!--{/if}-->
				<!--{elseif $cableType == TYPEC_CABLE}-->
					<!--{if $cableCount > 0}-->
						<!--{if $platform != PLATFORM_ALIPAY}-->
						<a onclick="paydirect2({$item['tid']}, {BATTERY_DEFAULT_FLAVOR}, {TYPEC_CABLE})"  href="javascript:;"><i class="icon-key"></i>Type-C</a>
						<!--{else}-->
						<a onclick="paydirect2({$item['tid']}, {BATTERY_DEFAULT_FLAVOR}, {TYPEC_CABLE}, true)" href="javascript:;"><i class="icon-key"></i>Type-C</a>
						<!--{/if}-->
					<!--{else}-->
						<a href="javascript:;" class="shop-pay-disabled"><i class="icon-key"></i>Type-C</a>
					<!--{/if}-->
				<!--{else}-->
				<!--{/if}-->
			<!--{/loop}-->
		<!--{/loop}-->
		</div>
	</div>
	<div class="shop-pay-footer">
		<h4>
			<span>温馨提示</span>
		</h4>
		<ul>
			<!--{if $platform != PLATFORM_ALIPAY}-->
			<li><span>1. 云充吧充电宝借出后，{$feeStr}</span></li>
			<li><span>2. 云充吧充电宝归还后，用户可在用户中心查看押金余额。</span></li>
			<!--{else}-->
			<li><span>云充吧充电宝借出后，{$feeStr}</span></li>
			<!--{/if}-->
		</ul>
	</div>
	<!-- loading图 -->
	<div class="shop-index-loading">
		<img id="loading_img" src="static/loading.gif"/>
	</div>
	<!--设备被占用状态-->
	<div class="occupied-bg">
		<div class="occupied-status">
			<h4><img src="static/images/icon-waitting .gif"/></h4>
			<span>请稍后</span>
			<!-- <div class="occupied-close"><a href="javascript:;">关闭</a></div> -->
		</div>
	</div>

	<!--用户有未归还的订单-->
	<div class="Withdrawals-log-bg">
		<div class="Withdrawals-log">
			<div class="mask-close-btn"><i></i></div>
			<div class="Withdrawals-img"><img src="static/images/prompt-bg.png"/></div>
			<p>您有租借中的充电宝，无法继续租借</p>
			<div class="Withdrawals-log-btn">
				<a href="/index.php?mod=shop&act=showorder">查看租借记录</a>
			</div>
		</div>
	</div>
</div>
<!--
<script src="{APPROOT}static/jquery.blockUI.js"></script>
<link rel="stylesheet" href="{APPROOT}static/jquery-ui.css">
-->
<script>
	$(".occupied-close a").click(function(){
		$(".occupied-bg").css("display","none");
	})
	$(".mask-close-btn i").click(function(){
		$(".Withdrawals-log-bg").css("display","none");
	});

	function paydirect2(itemId, flavor, cable, zhima) {
		/*$.blockUI({
			overlayCSS:{'backgroundColor':'0xFF'},
			message: $("#loading_img"),
			css:{'background':'transparent', 'border':'none',}
		});*/
		$(".loading-box").css("display","block");
		var zm = typeof(zhima) != "undefined" ? "&zm=true":"";
		var url = "{ROOT}wxpay.php?qid={$qid}&submit=paydirect&formhash={FORMHASH}&itemid=" + itemId + "&flavor=" + flavor + '&cableType=' + cable + zm ;
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'JSON',
			success:function(data) {
				if(data.errcode == 0) {
					if(data.pay_type == 1) {
						//alert("押金已从账户余额中扣取,请取充电柜上的电池, 谢谢!");
						$(".shop-index-loading").css("display","none");
						var psurl = "/wxpay.php";
						window.location.href=psurl+"?app=mcs&mod=wxpay&paymod=index&act=pay_success";
						return;
					} else if(data.pay_type == 0) {
						// $.unblockUI();
						jsApiParameters = data.jsApiParameters;
						callpay();
						$(".loading-box").css("display","none");
						return;
					}
				} else if(data.errcode == 1) {
					$(".occupied-bg").css("display","block");
					$(".shop-index-loading").css("display","none");
					timer = setInterval(function() {
						$.ajax({
							url: '/index.php?mod=api&act=user&opt=check_station_online',
							data: {qid: $qid},
						})
						.done(function(data) {
							console.log("success");
							if(data.code == 0) {
				                clearInterval(timer);
								$(".occupied-bg").css("display","none");
				                return;
							}
						})
						.fail(function() {
							console.log("error");
						});
					}, 1000);
					//$.unblockUI();
				} else if(data.errcode ==2 ){
					// alert("您有正在进行的租借服务，请先归还充电宝后再次尝试，谢谢。")
					$(".Withdrawals-log-bg").css("display","block");
					$(".shop-index-loading").css("display","none");
				} else {
					alert(data.errmsg);
					//$.unblockUI();
					$(".loading-box").css("display","none");

				}
			},
			error:function(e) {
				//$.unblockUI();
				$(".loading-box").css("display","none");
				alert(JSON.stringify(e));
				alert("服务器异常, 请稍后再试");
			},
			complete:function(e) {
				//$.unblockUI();
			}
		});
	}
</script>
@endsection
