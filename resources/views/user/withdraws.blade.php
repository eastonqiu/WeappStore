@extends('layouts.app_borrow')

@section('content')
<style>body{ background:#e7e6e6;}</style>
<div class="ucenter">
	<div class="uhead">
        <div class="uportrait">
			<img src="{{ $user['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'"/>
        </div>
        <h2>{{ $user['nickname'] }}</h2>
    </div>
	<div class="balance">
        <h4>账户余额：<span>{{ $user['balance'] }}元</span></h4>
    </div>
    <ul>
		<li>
        	<a href="/user/withdraw">
            	<h4>余额提现</h4>
                <i class="arrow-rig"></i>
            </a>
        </li>
    	<li>
        	<a href="/user/orders">
            	<h4>租借记录</h4>
                <i class="arrow-rig"></i>
            </a>
        </li>
		<li>
			<a href="/user/withdraws">
				<h4>提现记录</h4>
				<i class="arrow-rig"></i>
			</a>
		</li>
    </ul>
</div>

<div class="ucenter-footer">
	<h4>客服电话<em>{{ env('SERVICE_PHONE') }}</em></h4>
</div>
@endsection
