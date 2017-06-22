@extends('layouts.app_borrow')

@section('content')
<style>body{background: #eee;}</style>

<ul class="refund-content">
	@if empty($withdraws)
		<li style="text-align:center; clear:both">暂无正在提现的记录</li>
	@else
		@foreach $withdraws $withdraw}-->
		@if $withdraw['status'] == REFUND_STATUS_REQUEST
		<li class="Present present-content">
			<div class="Presentmon">
				<h4 class="present-open-title">提现金额: { $withdraw['refund']}元<span>提现中</span></h4>
				<h4 class="present-fold-title">
					@if empty($withdraw['refund_time'])
					提现申请成功
					@else
					审核通过
					@endif
					<span>提现中</span>
				</h4>
			</div>
			<div class="present-open-content">
				<div class="refund">
					<em>?</em>
					<a href="/index.php?mod=shop&act=user_refund_help">退款帮助</a>
				</div>
				<ul>
					<li class="active">
						<i class="icon"></i>
						<div class="txt">
							<h4>提现申请成功</h4>
							<span>已将退款申请交至微信</span>
							<span class="successTime">{echo date('Y-m-d H:i:s', $withdraw['request_time']);}</span> </div>
					</li>
					<li class="{echo empty( $withdraw['refund_time'])? '' : 'active'}">
						<i class="icon"></i>
						<div class="txt">
							<h4>审核通过</h4>
							<span>您的资金转至微信处理</span>
							<span class="successTime">{echo empty($withdraw['refund_time'])? '' : date('Y-m-d H:i:s', $withdraw['refund_time']);}</span>
						</div>
					</li>
					<li>
						<i class="icon"></i>
						<div class="txt">
							<h4>已到账</h4>
							<span>微信将退款原路返回到你的支付账户中</span>
						</div>
					</li>
				</ul>
				<div class="pull-up-status"><h4><i></i>收起</h4></div>
			</div>
			<div class="fold-content">
				@if empty($withdraw['refund_time'])
				<span>已将退款申请交至微信<em> {echo date('Y-m-d H:i:s',  $withdraw['request_time']);}</em></span>
				@else
				<span>
					您的资金转至微信处理<em> {echo date('Y-m-d H:i:s',  $withdraw['refund_time']);}</em>
				</span>
				@endif
				<h4>金额: <em>{ $withdraw['refund']}元</em></h4>
				<h2><i></i>查看</h2>
			</div>
		</li>

		@elseif $withdraw['status'] == REFUND_STATUS_DONE

		<!--已提现展开-->
		<li class="Present present-already">
			<div class="Presentmon">
				<h4>提现金额 { $withdraw['refund']}元<span>已提现</span></h4>
			</div>
			<div class="present-open-content">
				<div class="refund">
					<em>?</em>
					<a href="/index.php?mod=shop&act=user_refund_help">退款帮助</a>
				</div>
				<ul>
					<li class="active">
						<i class="icon"></i>
						<div class="txt">
							<h4>提现申请成功</h4>
							<span>已将退款申请交至微信</span>
							<span class="successTime">{echo date('Y-m-d H:i:s',  $withdraw['request_time']);}</span>
						</div>
					</li>
					<li class="{echo empty( $withdraw['refund_time'])? '' : 'active'}">
						<i class="icon"></i>
						<div class="txt">
							<h4>审核通过</h4>
							<span>您的资金转至微信处理</span>
							<span class="successTime">{echo empty( $withdraw['refund_time'])? '' : date('Y-m-d H:i:s',  $withdraw['refund_time']);}</span>
						</div>
					</li>
					<li>
						<i class="icon"></i>
						<div class="txt">
							<h4>已到账</h4>
							<span>微信将退款原路返回到你的支付账户中</span>
						</div>
					</li>
				</ul>
				<div class="pull-up-status"><h4><i></i>收起</h4></div>
			</div>
			<div class="fold-content">
				<span>提现已到账<em> {echo date('Y-m-d H:i:s',  $withdraw['refund_time']);}</em></span>
				<h4>金额:<em>{ $withdraw['refunded']}元</em></h4>
				<h2><i></i>查看</h2>
			</div>
		</li>
		@if
		@endforeach
	@if
</ul>

<script>
	//查看
	$(".fold-content h2").click(function(){
		$(this).parents(".fold-content").siblings(".present-open-content").css("display","block");
		$(this).parents(".present-content").find(".present-open-title").css("display","block");
		$(this).parents(".fold-content").css("display","none");
		$(this).parents(".present-content").find(".present-fold-title").css("display","none");
	});

	//收起
	$(".pull-up-status h4").click(function(){
		$(this).parents(".present-open-content").css("display","none");
		$(this).parents(".present-content").find(".present-open-title").css("display","none");
		$(this).parents(".present-open-content").siblings(".fold-content").css("display","block");
		$(this).parents(".present-content").find(".present-fold-title").css("display","block");
	})

	$(document).ready(function(){
		var open = $(".refund-content li:first-child .present-open-content");
		var fold = $(".refund-content li:first-child .fold-content");
		$(".refund-content li:first-child .pull-up-status h4").click(function(){
			open.removeClass("super-open");
			open.addClass("super-fold");
			fold.removeClass("super-fold");
			fold.addClass("super-open");
		})

		$(".refund-content li:first-child .fold-content h2").click(function(){
			open.removeClass("super-fold");
			open.addClass("super-open");
			fold.removeClass("super-open");
			fold.addClass("super-fold");
		})
	})

</script>
@endsection
