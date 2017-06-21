@extends('layouts.app_borrow')

@section('content')
<style type="text/css">
.pagepaddingfix{
  padding: 8px;
  padding: 8px;
  font-size:20px;
}

.tip_msg{
  padding-top: 8px;
  padding-bottom: 8px;
  font-size:16px;
  color:#777;
}
</style>
<section class="pagepaddingfix pagepadding ">
	<div class="pagepaddingfix">
		<span>账户押金: </span>
		<span id="account_deposit">{{ $user['deposit'] }}</span>元
	</div>
	<div class="pagepaddingfix">
		<span>账户余额: </span>
		<span id="balance">{{ $user['balance'] }}</span>元
	</div>
	<div class="pagepaddingfix">
		<span>待退款: </span>
		<span id="refund_wait">{{ $user['refund'] }}</span>元
	</div>
	<div>
		<input type="hidden" id="refund" name="refund" value="{{ $user['balance'] }}">
		<div class="pagepaddingfix">
            提现金额: <span style='color:red'>{{ $user['balance'] }}元</span>
		</div>
		<input class="fullbutton pagepaddingfix" type="button" style="width:100%;background-color:#09925e" value="申请提现" onclick="refundReq();" />
	</div>
	<div class="tip_msg">
	   提示: <br>
	   请留意微信支付消息通知
	</div>
</section>

<div style="display:none">
    <img id="loading_img" src="/images/loading.gif" style="display:none" />
</div>
<div class="bounce" id="refund_tip_dlg">
	<div class="bounce-text" id="refund_tip"></div>
	<a id="dlg_close">关闭</a>
</div>
<script src="/js/vendor/jquery-blockui/jquery.blockUI.js"></script>
<link rel="stylesheet" href="/js/vendor/jquery-blockui/jquery-ui.css">

<script type="text/javascript">
function refundReq() {
	$('#refund').val(parseFloat($('#refund').val()).toFixed(2));
	if($('#refund').val() == '' || $('#refund').val() == 0 || parseFloat($('#balance').html()) < parseFloat($('#refund').val())) {
		alert("您暂无余额可以提现!");
		return;
	}

	$.blockUI({
		overlayCSS:{'backgroundColor':'0xFF'},
		message: $("#loading_img"),
		css:{'background':'transparent', 'border':'none'}
    });
	$.ajax({
		url:"/user/withdraw_apply",
		type:'GET',
		dataType: 'JSON',
		success:function(data) {
			if(data.errcode == {{ App\Common\Errors::NORMAL }}) {
				// 更新界面
				$('#balance').html(0).toFixed(2);
				$('#refund_wait').html((parseFloat($('#refund_wait').html()) + parseFloat($('#refund').val())).toFixed(2));
				// 提示
				$('#refund_tip').html('申请成功，系统正在退款!!');
				$('#dlg_close').unbind('click');
				$('#dlg_close').click(function() {
					$.unblockUI();
					callCloseWindow();
				});
				return;
			} else if(data.errcode == {{ App\Common\Errors::USER_ACCOUNT_WITHDRAW_BALANCE_NOT_ENOUGH }}) {
				$('#refund_tip').html('余额不足!!');
				$('#dlg_close').unbind('click');
				$('#dlg_close').click(function() {
					$.unblockUI();
				});
				$.blockUI({
					overlayCSS:{'backgroundColor':'0xFF'},
					message: $("#refund_tip_dlg"),
					css:{'background':'transparent', 'border':'none'}
		        });
				return;
			} else {
				alert('系统繁忙, 请稍后再试');
				alert(data.errmsg);
				$.unblockUI();
			}
		},
		error:function(e) {
			$.unblockUI();
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
