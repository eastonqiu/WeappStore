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
</div>

<script>
	function pay(dId, pId) {
		$(".shop-index-loading").css("display","block");
		var url = "/borrow/order?dId=" + dId + "&pId=" + pId;
		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'JSON',
			success:function(data) {
				if(data.errcode == {{ App\Common\Errors::ORDER_PAY_BY_ACCOUNT }}) {
					alert("押金已从账户余额中扣取,请取充电柜上的电池, 谢谢!");
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
			},
			error:function(e) {
				alert(JSON.stringify(e));
				alert("服务器异常, 请稍后再试");
			},
			complete:function(e) {
				$(".shop-index-loading").css("display","none");
			}
		});
	}
</script>
@endsection
