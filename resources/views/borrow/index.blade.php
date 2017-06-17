@extends('layouts.app_borrow')

@section('content')
<style>body{ background: #eee;}</style>
<div class="shop-pay-main">
	<div class="shop-pay-content">
		<div class="icon-shopmoney">
			<h4><img src="/images/icon-shopMoney.png"/></h4>
		</div>
		<div class="pay-text">
			<span>需支付押金</span>
			<h2>100元</h2>
		</div>
		<!--余额借用内容显示-->
		<div class="lease-pay-btn">
		<h4>充电宝自带充电线，请选择所需的接口类型</h4>
		<a onclick="pay({{ $dId }}, {{ $pId }})" href="javascript:;">
		  	<i class="icon-iphone"></i>确认支付
		</a>
		</div>
	</div>
	<div class="shop-pay-footer">
		<h4>
			<span>温馨提示</span>
		</h4>
		<ul>
			<li><span>1. 充电宝借出后，...</span></li>
			<li><span>2. 充电宝归还后，用户可在用户中心查看押金余额。</span></li>
			<li><span>充电宝借出后，...</span></li>
		</ul>
	</div>
	<!-- loading图 -->
	<div class="shop-index-loading">
		<img id="loading_img" src="/images/loading.gif"/>
	</div>
	<!--设备被占用状态-->
	<div class="occupied-bg">
		<div class="occupied-status">
			<h4><img src="/images/icon-waitting .gif"/></h4>
			<span>请稍后</span>
			<!-- <div class="occupied-close"><a href="javascript:;">关闭</a></div> -->
		</div>
	</div>

	<!--用户有未归还的订单-->
	<div class="Withdrawals-log-bg">
		<div class="Withdrawals-log">
			<div class="mask-close-btn"><i></i></div>
			<div class="Withdrawals-img"><img src="/images/prompt-bg.png"/></div>
			<p>您有租借中的充电宝，无法继续租借</p>
			<div class="Withdrawals-log-btn">
				<a href="/index.php?mod=shop&act=showorder">查看租借记录</a>
			</div>
		</div>
	</div>
</div>

<script>
	$(".occupied-close a").click(function(){
		$(".occupied-bg").css("display","none");
	})
	$(".mask-close-btn i").click(function(){
		$(".Withdrawals-log-bg").css("display","none");
	});

	function pay(dId, pId) {
		$(".loading-box").css("display","block");
		var url = "/borrow/order?dId=" + dId + "&pId=" + pId;
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'JSON',
			success:function(data) {
				if(data.errcode == {{ App\Common\Errors::ORDER_PAY_BY_ACCOUNT }}) {
					alert("押金已从账户余额中扣取,请取充电柜上的电池, 谢谢!");
					$(".shop-index-loading").css("display","none");
				} else if(data.errcode == {{ App\Common\Errors::ORDER_PAY_NEW }}) {
					prepayId = data.errmsg;
					callpay();
				} else if(data.errcode == {{ App\Common\Errors::ORDER_STOCK_NO_ENOUGH }} ){
					alert("库存不足");
				} else if(data.errcode == {{ App\Common\Errors::ORDER_WECHAT_ORDER_FAIL }} ){
					alert("下单失败");
				} else {
					alert(data.errmsg);
				}
				$(".loading-box").css("display","none");
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
