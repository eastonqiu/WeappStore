function bindAddress(event, uid, shop_station_id, title, desc, address) {
	event.stopPropagation();

	// if(lbsID == uid) {
	// 	alert("该点已绑定");
	// 	return;
	// }

	// 去除非法字符 ( )
	address = address.replace('(', ' ');
	address = address.replace(')', ' ');

	var data = {
		'openid' : openid,
		'sid' : curSid,
		'lbsid' : uid,
		'shop_station_id' : shop_station_id,
		'title' : title,
		'desc' : desc,
		'address' : address 
	};
	$.ajax({
	    type: 'GET',
	    url: settingUrl ,
	    data: data ,
	    dataType: 'JSON',
	    success: function(data) {
	    	if(data.errcode == 0) {
	    		lbsID = uid;
	    		alert('绑定成功');
				wxApiCloseWindow();
	    	} else {
	    		alert("参数有误:" + data.errmsg);
				wxApiCloseWindow();
	    	}
	    },
	    error: function(e) {
	        alert("error 参数有误");
	        alert(JSON.stringify(e));
			wxApiCloseWindow();
	    }
	});
}

function bindA(uid, shop_station_id, title, desc, address) {
	return "<a href='javascript:void(0);' class='bindAddress' onclick=\"bindAddress(event, " + uid + ", " + shop_station_id + ", '" + title + "', '" + desc + "', '" + address + "');\" style='float:right'> 绑定地址</a>";
}

function bindShop(event, shop_id, desc) {
	event.stopPropagation();
	var data = {
		//'openid' : openid,
		'sid' : curSid,
		'shop_id' : shop_id,
		//'lbsid' : uid,
		//'shop_station_id' : shop_station_id,
		//'title' : title,
		'desc' : desc
		//'address' : address 
	};
	$.ajax({
	    type: 'GET',
	    url: actionUrl + '&act=bind_shop',
	    data: data ,
	    dataType: 'JSON',
	    success: function(data) {
	    	if(data.status == 0) {
				$(".mask-position-bg").css("display","none");
	    		lbsID = data.id;
	    		alert('绑定成功');
				wxApiCloseWindow();
	    	} else {
				$(".mask-position-bg").css("display","none");
	    		alert("参数有误:" + data.message);
				wxApiCloseWindow();
	    	}
	    },
	    error: function(e) {
			$(".mask-position-bg").css("display","none");
	        alert("error 参数有误");
	        alert(JSON.stringify(e));
			wxApiCloseWindow();
	    }
	});
}
//关闭微信页面
function wxApiCloseWindow() {
	WeixinJSBridge.invoke('closeWindow',{},function(res){
		if(res.err_msg == "close_window:error") {
			alert("关闭微信网页错误，请稍后重试，谢谢");
		}
	});
}
