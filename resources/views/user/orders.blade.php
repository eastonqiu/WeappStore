@extends('layouts.app_borrow')

@section('content')
<style>body{background: #eee; }}</style>
<div class="order-list">
@if (empty($orders))
	<div style="text-align:center; clear:both">暂无租借记录</div>
@else
	<ul class="order-list">
@foreach ($orders as $order)
	@if ($order['status'] != \App\Models\BorrowOrder::ORDER_STATUS_BORROW_CONFIRM)
	<!--已完成-->
	<li class="order-present order-complete">
		<div class="Presentmon">
			<h4>订单编号:{{ $order['orderid'] }}<span>已完成</span></h4>
		</div>
		<div class="order-complete-open">
			<div class="order-content">
				<p>租借地点<span>{{ $order['borrow_station_name'] }}</span></p>
				<p>租借时间<span>{{ $order['borrow_time'] }}</span></p>
                @if (!empty($order['return_station_name']))
				<p>归还地点<span>{{ $order['return_station_name'] }}</span></p>
				<p>归还时间<span>{{ $order['return_time'] }}</span></p>
				@else
				<p>归还地点<span>无</span></p>
				<p>归还时间<span>无</span></p>
				@endif
			</div>
			<div>
				<h4>租借时长:<em>{{ ($order['return_time'] - $order['borrow_time']) }}</em></h4>
				<h4 style="float: left;">产生费用:<em>{{ $order['usefee'] }}元</em></h4>
				<div class="pull-up-status order-complete-pullUp">
					<h4><i></i>收起</h4>
				</div>
			</div>

		</div>
		<div class="order-complete-fold">
			<div class="order-content">
				<p>租借地点<span>{{ $order['borrow_station_name'] }}</span></p>
				<p>租借时间<span>{{ $order['borrow_time'] }}</span></p>
			</div>
			<div class="order-money">
				<h4>产生费用:<em>{{ $order['usefee'] }}元</em></h4>
			</div>
			<div class="pull-down-status order-complete-btn">
				<h4><i></i>查看</h4>
			</div>
		</div>
	</li>

	@else

	<li class="order-present">
		<div class="Presentmon">
			<h4>订单编号:{{ $order['orderid'] }}<span>进行中</span></h4>
		</div>
		<div class="order-complete-open">
			<div class="order-content">
				<p>租借地点<span>{{ $order['borrow_station_name'] }}</span></p>
				<p>租借时间<span>{{ $order['borrow_time'] }}</span></p>
			</div>
			<div>
				<h4 style="float: left;">租借时长:<em>{{ (time() - $order['borrow_time']) }}</em></h4>
				<div class="pull-up-status order-pullUp">
					<h4><i></i>收起</h4>
				</div>
			</div>

		</div>
		<div class="order-complete-fold">
			<div class="order-content">
				<p>租借地点<span>{{ $order['borrow_station_name'] }}</span></p>
				<p>租借时间<span>{{ $order['borrow_time'] }}</span></p>
			</div>
			<div class="order-money">
				<h4>租借时长:<em>{{ (time() - $order['borrow_time']) }}</em></h4>
			</div>
			<div class="pull-down-status order-fold">
				<h4><i></i>查看</h4>
			</div>
		</div>
	</li>
	@endif
@endforeach
@endif

</div>
<script>
	//查看
	$(".pull-down-status").click(function(){
		$(this).parents(".order-complete-fold").siblings(".order-complete-open").css("display","block");
		$(this).parents(".order-complete-fold").css("display","none");
	 }})
	//收起
	$(".pull-up-status").click(function(){
		$(this).parents(".order-complete-open").css("display","none");
		$(this).parents(".order-complete-open").siblings(".order-complete-fold").css("display","block");
	 }})

	$(document).ready(function(){
		var open = $(".order-list li:first-child .order-complete-open");
		var fold = $(".order-list li:first-child .order-complete-fold");
		$(".order-list li:first-child .pull-up-status h4").click(function(){
			open.removeClass("super-open");
			open.addClass("super-fold");
			fold.removeClass("super-fold");
			fold.addClass("super-open");
		 }})

		$(".order-list li:first-child .pull-down-status h4").click(function(){
			open.removeClass("super-fold");
			open.addClass("super-open");
			fold.removeClass("super-open");
			fold.addClass("super-fold");
		 }})
	 }})
</script>
@endsection
