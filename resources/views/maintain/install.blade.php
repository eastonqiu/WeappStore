<!DOCTYPE html>
<html lang="en" style="width: 100%; height: 100%; overflow-x:hidden; overflow-y:hidden;">
<head>
<meta charset="utf-8">
<title>设备安装</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" id="viewport" name="viewport">
<link href="/lbs/css/bootstrap.css" rel="stylesheet">
<link href="/lbs/css/main.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="/js/vendor/jquery-ui.css">
</head>

<body style="width: 100%; height: 100%; overflow-x:hidden; overflow-y:hidden; background-color:#f3f1ec">
    <div id="address-search">
        <input type="search" id="address" placeholder="输入检索关键字">
        <span class="btn btn-primary search-btn">搜索</span>
        <!-- <span class="btn" id="chgMode">列表/地图</span> -->
    </div>
    <div id="mapbox_new" style="position: relative; overflow:hidden;">
        <div id="arrow_up"
            style="width: 100%; position: absolute; bottom: 0; display: none; z-index: 1000">
            <img src="/lbs/img/arrow_up.png"
                style="vertical-align: bottom" onclick="toggle();" />
        </div>
        <div id="map"></div>
    </div>

    <div id="main_new" style="display: none;">
        <div id="arrow_down">
            <img src="/lbs/img/arrow_down.png"
                style="vertical-align: top" onclick="toggle();" />
        </div>
        <!--地址正文-->
        <div id="listContainer" class="container" style="overflow-y: auto; overflow-x:hidden;">
            <div id="listWrap">
                <table class="table table-hover">
                    <tbody id="addLbsStation">
                        <a href="javascript:void(0);" onclick="formToggle();" id="addLbsStationBtn">新增商铺</a>
                        <a href="" id="enterSlotMgrPageBtn">进入维护页面</a>
                    </tbody>
                </table>
				<div class="listWrap-search">
					<input type="text" name="shop" id="autocomplete" placeholder="请输入想要搜索的商铺名称">
					<input type="hidden" name="shop_id" id="shop_id">
					<button onclick="getShopInfo()">搜索</button>
				</div>
				<table class="table">
                    <tbody id="listBoby">
                    </tbody>
				</table>
            </div>
            <div id="loading_img" style="display:none; width:100%; text-align:center"><img  src="/lbs/img/loading.gif" /></div>
        </div>
    </div>

<div class="mask-bg">
    <div class="bomb">
        <form action="" method="post" id="addStationForm">
           <ul>
				<li>
					<span>商铺名称:</span>
					<input type="text" name="stationName" placeholder="请输入商铺名称"/>
					<em>*</em>
				</li>
			   <li>
				   <span>商铺类型:</span>
				   <select name="shoptype" id="shop_type">
				   		<option value="">请选择商铺类型</option>
				   </select>
				   <em>*</em>
			   </li>
				<li>
					<span>选择省份:</span>
					<select name="province" id="get-province">
						<option value="">请选择省份</option>
					</select>
					<em>*</em>
					<span>选择城市:</span>
					<select name="city" id="get-city">
						<option value="">请选择城市</option>
					</select>
					<em>*</em>
					<span>选择区县:</span>
					<select name="area" id="get-area">
						<option value="">请选择区域</option>
					</select>
					<em>*</em>
				</li>
			    <li>
					<span>具体地址:</span>
					<input type="text" name="street" id="street">
					<em>*</em>
			    </li>
				<li>
					<span>摆放位置:</span>
					<input type="text" name="stationDesc" placeholder="请输入充电站的具体摆放位置"/>
					<em>*</em>
				</li>
			   <li>
				   <span>营业时间:</span>
				   <input type="time" name="stime" id="stime" /> - <input type="time" name="etime" id="etime" />
				   <em>*</em>
			   </li>
			   <li>
				   <span>联系电话:</span>
				   <input type="number" id="phone" />
				   <em>*</em>
			   </li>
			   <li>
				   <span>人均消费:</span>
				   <input type="number" style="width: 16%" id="cost" />
				   <span>元</span>
				   <em>*</em>
			   </li>
			   <li style="margin-bottom: 0;"><h6>标有<em>*</em>为必填项</h6></li>
				<li class="addStation">
					<a href="javascript:;" id="cancel" onclick="formCancel()">取消</a>
					<a onclick="addStation('');">新增</a>
				</li>
			</ul>
        </form>
     </div>
</div>
<!--遮罩弹框-->
<div class="mask-bg-small">
	<div class="bomb-small">
		<div>
			<span>摆放位置:</span>
			<input type="text" name="desc" placeholder="请输入充电站的具体摆放位置"/>
		</div>
		<div class="bomb-small-btn">
			<button class="cancel">取消</button>
			<!-- <button id="bind_shop" onclick="bindShop(event, ' + shop.id + ' )";>确定</button> -->
			<button id="bind_shop";>确定</button>
		</div>
	</div>
</div>
<div class="mask-status-bg mask-addStation-bg mask-position-bg">
	<div class="addStation-loading">
		<img src="/lbs/img/loading.gif"/>
	</div>
</div>
<div class="mask-fail-status">
	<div class="fail-status-content">
		<h4></h4>
		<h4><span>关闭</span></h4>
	</div>
    <script src="http://api.map.baidu.com/api?v=2.0&ak=uUudLhdswAhPXMfobArfyTHD"
        type="text/javascript"></script>
    <script type="text/javascript"
        src="/lbs/js/jquery.js"></script>
    <script src="/lbs/js/jquery.pager.js"
        type="text/javascript"></script>
    <script src="/lbs/js/jquery.endless-scroll.js"
        type="text/javascript"></script>
    <script src="/js/vendor/jquery-blockui/jquery.blockUI.js"></script>
    <script type="text/javascript"
        src="/lbs/js/bootstrap.js"></script>

    <script type="text/javascript">
        var curCity = null;
        var POI_TYPE = false;
        var BMAP_AK = "uUudLhdswAhPXMfobArfyTHDK";
        if(POI_TYPE) {
            BMAP_AK = "uLr3Yqy0G0LMmH0B5szXFw5u";
        }
        var GEOTABLE_ID = "GEOTABLE_ID";
    </script>
    <script type="text/javascript"
        src="/lbs/js/main.js?v=2"></script>

    <script type="text/javascript">
        var curSid = {{ $deviceId }};
        var lbsID = 0;
        var settingUrl = '';
        var actionUrl = '';
        var openid = '';
        keyFilter = ["enable:0"]; // 过滤条件
    </script>
    <script type="text/javascript"
        src="/lbs/js/addr_setting.js?v=2">
    </script>
    <script src="http://cdn.bootcss.com/jquery.devbridge-autocomplete/1.2.27/jquery.autocomplete.js"></script>
    <script>

    jQuery(function($){
            //getDefaultShopInfo();
            $.ajax({
                url: '/index.php?mod=api&act=shop_station&opt=get_province',
                success: function(e) {
                    //console.log(e);
                    //console.log('345235234');
                    var html = '<option value="">请选择省份</option>';
                    for(var i in e.data) {
                        if(e.data[i] == '{$city}') {
                            html += '<option value="'+e.data[i]+'" selected>' + e.data[i] + '</option>';
                        } else {
                            html += '<option value="'+e.data[i]+'">' + e.data[i] + '</option>';
                        }

                    }
                    $('#get-province').html(html);
                    // $('#get-area').html('<option value="">请选择区域</option>');
                }
            });

            $.ajax({
                url: '/index.php?mod=api&act=shop&opt=get_all_shop_type',
                dataType: 'json',
            })
            .done(function(e) {
                console.log(e.data[2]['type']);
                html = '';
                for(var i in e.data) {
                    html += '<option value="'+e.data[i]['id']+'">' + e.data[i]['type'] + '</option>';
                }
                $('#shop_type').html(html);
            });

            // ==============

            $.getJSON('/index.php?mod=api&act=shop&opt=get_all_shop_locate', function(data) {
                content = data.data;
                stations_array = content;
                autocomplete_title(stations_array);
            });

            function autocomplete_title(stations_array) {
                $('#autocomplete').autocomplete({
                    lookup : stations_array,
                    minChars : 0,
                    onSelect: function (suggestion) {
                        $('#shop_id').val(suggestion.data);
                        console.log('shop_id:' + suggestion.data + ', title:' + suggestion.value);
                    },
                });

                $('#autocomplete').on('focus', function (){
                    $(this).autocomplete().onValueChange();
                });
            }
        })

    $('#get-province').change(function(event, p, a) {
        if (!p) {p = $('#get-province').val();}
        if (!a) {a = 1;}
        console.log(p);
        $.ajax({
            type: 'POST',
            async: false,
            // data: {province:$('#get-province').val(), ajax:1},
            data: {province:p, ajax:a},
            url: 'index.php?mod=api&act=shop_station&opt=get_area_info',
            success: function(e) {
                var html = '<option value="">请选择城市</option>';
                for(var i in e.data) {
                    html += '<option value="'+e.data[i]+'">' + e.data[i] + '</option>';
                }
                $('#get-city').html(html);
                $('#get-area').html('<option value="">请选择区域</option>');
            }
        })
    });

    $('#get-city').change(function(event, p, c, a) {
        if (!p) {p = $('#get-province').val();}
        if (!c) {c = $('#get-city').val();}
        if (!a) {a = 1;}
        console.log(a);
        if (c == "") {
            var html = '<option value="">请选择区域</option>';
            $('#get-area').html(html);
        } else {
            $.ajax({
                type: 'POST',
                data: {province:p, city:c, ajax:a},
                async: false,
                url: 'index.php?mod=api&act=shop_station&opt=get_area_info',
                success: function(e) {
                    var html = '<option value="">请选择区域</option>';
                    for(var i in e.data) {
                        html += '<option value="'+e.data[i]+'">' + e.data[i] + '</option>';
                    }
                    $('#get-area').html(html);
                }
            })
        }
    });

    function getShopInfo() {
        shop_id = $('#shop_id').val();
        $.ajax({
            url: 'index.php?mod=api&act=shop&opt=get_shop_info',
            dataType: 'json',
            data: {shop_id: shop_id},
        })
        .done(function(data) {
            console.log(data);
            console.log("success");
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

    </script>
</body>
</html>
