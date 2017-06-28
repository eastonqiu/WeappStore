var maxCount = 32;
var map = new AMap.Map('map', {
    resizeEnable: true,
});
var userLocation = null;
var userCity = null;
//var curCity = null; // 默认城市
var nearByRes = null;
var nearByPoints = [];
var nearByPage = 0;
var NEARBY_DEFAULT = 100000; // 默认搜索范围
var nearByRadius = NEARBY_DEFAULT;
var toggleRefresh = true;

var LOCAL_SEARCH = 0;
var NEARBY_SEARCH = 1;
var BOUND_SEARCH = 2; // 暂时不用

var searchMode = NEARBY_SEARCH; // 默认搜索方式

var keyFilter = ["enable:1"]; // 过滤条件

var curPage = null, totalPage = null;

var showList = true;

var isLoading = false;

var dragcount = 0;

var clickShop = 0;

var bindPoint;
//关闭微信页面
function wxApiCloseWindow() {
	WeixinJSBridge.invoke('closeWindow',{},function(res){
		if(res.err_msg == "close_window:error") {
			alert("关闭微信网页错误，请稍后重试，谢谢");
		}
	});
}

function toggle() {
    showList = !showList;
    if(showList) {
        $('#mapbox_new').css("height", "30%");
        $('#main_new').css("display", "block");
        $('#arrow_down').css("display", "block");
        $('#arrow_up').css("display", "none");
        $('#main_new').css("height", "70%");
    } else {
        $('#mapbox_new').css("height", "100%");
        $('#main_new').css("display", "none");
        $('#arrow_down').css("display", "none");
        $('#arrow_up').css("display", "block");
    }
}
//$("#listWrap a").click(function(){
//  $('form').toggle();
//  })
function addStation() {
	$(".mask-addStation-bg").css("display","block");
    stationName = $(":text[name='stationName']").val();
    stationProvince = $("#get-province").val();
    stationCity     = $("#get-city").val();
    stationArea     = $("#get-area").val();
    stationStreet   = $("#street").val();
    stationDesc    = $(":text[name='stationDesc']").val();
    $(".mask-bg").css("display","none");
    
    url = '/index.php?mod=init_addr&act=add';

    data = {
        "stationName": stationName,
        "stationDesc":stationDesc,
        "stationProvince": stationProvince,
        "stationCity": stationCity,
        "stationArea": stationArea,
        "stationStreet":stationStreet,
        "sid": curSid,
        "cost":$("#cost").val(),
        "phone":$("#phone").val(),
        "stime":$("#stime").val(),
        "etime":$("#etime").val(),
        "type":$("#shop_type").val()
    };

    if (dragcount) {
        bindPoint = map.getCenter();
        data.latitude = bindPoint.lat;
        data.longitude = bindPoint.lng;
		$.get( url, data,
			function(data) {
				console.log('center data: ' + data.message);
				if (data.errcode == 0) {
					$(".mask-addStation-bg").css("display","none");
					// $('#bindAddress').trigger('click', [data.id, stationName, stationDesc, stationAddress] );
					alert('新增并绑定成功!');
					// window.location.reload();
					wxApiCloseWindow();
				} else {
					$(".mask-addStation-bg").css("display","none");
					//alert('新增失败，请重新添加!' + data.errmsg);
					$(".fail-status-content h4:first-child").text(data.errmsg + "新增失败，请重新添加!");
					$(".mask-fail-status").css("display","block");
					$(".fail-status-content h4 span").click(function(){
						$(".mask-fail-status").css("display","none");
						$(".mask-bg").css("display","block");
					})
				}
			}, 'json');
	} else {
		map.plugin('AMap.Geolocation', function() {
	        geolocation = new AMap.Geolocation({
	            enableHighAccuracy: true,//是否使用高精度定位，默认:true
	            timeout: 10000,          //超过10秒后停止定位，默认：无穷大
	            buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
	            zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
	            buttonPosition:'RB'
	        });
	        map.addControl(geolocation);
	        geolocation.getCurrentPosition();
	        AMap.event.addListener(geolocation, 'complete', onGeoComplete);//返回定位信息
	        AMap.event.addListener(geolocation, 'error', onGeoError);      //返回定位出错信息
	    });
	    //解析定位结果
	    function onGeoComplete(data) {
	        bindPoint = {'lng' : data.position.getLng(), 'lat' : data.position.getLat()};

            $.get( url, data,
                function(data) {
                    console.log(data);
                    console.log('center data: ' + data.message);
                    if (data.errcode == 0) {
						$(".mask-addStation-bg").css("display","none");
                        // $('#bindAddress').trigger('click', [data.id, stationName, stationDesc, stationAddress] );
                        alert('新增并绑定成功!');
						wxApiCloseWindow();
                    } else {
						$(".mask-addStation-bg").css("display","none");
                        //alert('新增失败，请重新添加!' + data.errmsg);
						$(".fail-status-content h4:first-child").text(data.errmsg + "新增失败，请重新添加!");
						$(".mask-fail-status").css("display","block");
                    }
                }, 'json');
	    }
	    //解析定位错误信息
	    function onGeoError(data) {
	        alert('定位失败');
	    }
    }
}

$(".fail-status-content h4 span").click(function(){
	console.log('ewifojew');
	$(".mask-fail-status").css("display","none");
	$(".mask-bg").css("display","block");
})

!function() {

    location(); // 定位并查找附近的点
    // ==========================================

    // 检索模块相关代码
    var keyword = "", // 检索关键词
    page = 0, // 当前页码
    points = [];

    function location() {
        $.blockUI({
            overlayCSS:{'backgroundColor':'0xFF'},
            message: $("#loading_img"),
            css:{'background':'transparent', "border":'none'}
        });

        /*if(curCity != null) {
            searchAction('', 0, LOCAL_SEARCH);
        }*/
		map.plugin('AMap.Geolocation', function() {
	        geolocation = new AMap.Geolocation({
	            enableHighAccuracy: true,//是否使用高精度定位，默认:true
	            timeout: 10000,          //超过10秒后停止定位，默认：无穷大
	            buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
	            zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
	            buttonPosition:'RB'
	        });
	        map.addControl(geolocation);
	        geolocation.getCurrentPosition();
	        AMap.event.addListener(geolocation, 'complete', onGeoComplete);//返回定位信息
	        AMap.event.addListener(geolocation, 'error', onGeoError);      //返回定位出错信息
	    });
	    //解析定位结果
	    function onGeoComplete(data) {
	        userLocation = {'lng' : data.position.getLng(), 'lat' : data.position.getLat()};
            var mk = new BMap.Marker(userLocation);
            mk.setIcon(getIcon(10));
            map.addOverlay(mk);
            map.addEventListener("dragend",function(){
                // 移动结束，让地图点自动居中
                dragcount = 1;
                if(mk){
                    mk.setPosition(map.getCenter());
                }
                fillCityInfo(mk.getPosition());
            });
            //map.centerAndZoom(userLocation, 15);
            // alert('您的位置：' + r.point.lng + ',' + r.point.lat);
            gc.getLocation(r.point, function(rs) {
                var addComp = rs.addressComponents;
                userCity = addComp.city;
                console.log(addComp.province + addComp.city + addComp.district +
                addComp.street + addComp.streetNumber);
                console.log('get location');
                $('#get-province').val(addComp.province);
                $('#get-province').trigger('change', [addComp.province, 1]);
                $('#get-city').val(addComp.city);
                $('#get-city').trigger('change', [addComp.province, addComp.city, 1]);
                $('#get-area').val(addComp.district);
                $('#street').val(addComp.street + addComp.streetNumber);
                if(curCity == null || isInCurCity(userCity)) {
                    changeCity(userCity);
                    searchAction('', 0, NEARBY_SEARCH, userLocation);
                } else {
                    searchAction('', 0, LOCAL_SEARCH);
                }
            });
	    }
	    //解析定位错误信息
	    function onGeoError(data) {
	        alert('定位失败');
	    }
    }

    /**
     * 进行检索操作
     *
     * @param 关键词
     * @param 当前页码
     */
    function searchAction(keyword, page, type, location) {
        page = page || 0;
        type = typeof(type) != "undefined"? type : searchMode;
        var url = "http://api.map.baidu.com/geosearch/v3/local?callback=?"; // 城市区域内搜索
        switch(type) {
        case LOCAL_SEARCH:
            url = "http://api.map.baidu.com/geosearch/v3/local?callback=?";
            break;
        case NEARBY_SEARCH:
            url = "http://api.map.baidu.com/geosearch/v3/nearby?callback=?";
            if(location == null)
                location = userLocation; //默认是用户位置
            break;
        case BOUND_SEARCH:
            url = "http://api.map.baidu.com/geosearch/v3/bound?callback=?";
            break;
        }
        var data = {
            'q' : curCity + " " + keyword, // 检索关键字 只检索当前城市
            'page_index' : page, // 页码
            'filter' : keyFilter.join('|'), // 过滤条件
            //'filter' : '',
            'region' : curCity, // 城市名
            // 'scope' : '2', // 显示详细信息
            'geotable_id' : GEOTABLE_ID, //test 117126, mcs 119779
            'sortby' : 'distance:1',
            'radius' : nearByRadius,
            'ak' : BMAP_AK // 用户ak
        };
        if (location) {
            data['location'] = location.lng + ',' + location.lat;
        }

        isLoading = true;

        // console.log("************************8");
        // console.log(url);
        console.log(data);
        // getDefaultShopInfo();
        // console.log("************************8");
        $.ajax({
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            url: url,
            data: data,
            success: function (data) {
            console.log(data);
           if(data.status != 0) {
                    alert("地图服务暂不可用,请稍后再试,谢谢!");
                    return;
                }

                renderMap(data, type, location);
                if(page == 0) {
                    if(points.length == 0) {
                        map.centerAndZoom(curCity);
                        var tip = '<p style="border-top:1px solid #DDDDDD;padding-top:10px;text-align:center;text-align:center;font-size:18px;" class="text-warning">抱歉，该城市暂时没有充电站信息</p>';
                        $('#listBoby').html($(tip));
                    } else {
                        map.setViewport(points);
                    }
                }
                curPage = page; // start 0
                totalPage = Math.ceil(data.total / 10); //start 1
                $("#listContainer").endlessScroll({
                    fireOnce: false,
                    fireDelay: false,
                    loader: '',
                    callback: function(p){
                        if(isLoading)
                            return;
                        $("#loading_img").css('display', 'block');
                        searchAction(keyword, curPage+1, type, location);
                    },
                    ceaseFire: function() {
                        return curPage+1 >= totalPage; // end
                    }
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                //baidu server error retry
                console.log(textStatus + ", " + errorThrown);
                //searchAction(keyword, page, type, location);
                alert("地图服务暂不可用,请稍后再试,谢谢!");
            },
            complete: function() {
                $.unblockUI();
                $("#loading_img").css('display', 'none');
                $('#main_new').css('display', 'block');
                // $('#changecity').css('display', 'block');
                //map.addControl(new BMap.ScaleControl()); // 添加比例尺控件
                isLoading = false;
            }
        });
    }

    function searchLocationForPoint(address) {
        var gc = new BMap.Geocoder();
        gc.getPoint(address, function(point) {
            if (point) {
                bindPoint = point;
                fillCityInfo(bindPoint);
                map.centerAndZoom(point, 16);
                map.addOverlay(new BMap.Marker(point));
                console.log('wefwefewf');
                console.log(curCity);
            } else {
                alert("您选择地址没有解析到结果!");
            }
        }, curCity);
    }

    $('.search-btn').on('click', function() {
        searchLocationForPoint($('#address').val());
    });

	// 绑定展开/收起事件
	$('#toggleBtn').bind('click', function() {
		$('#filterBox').toggle('normal', function() {
			Util.setMapHeight();
		});
	});

	/**
	 * 渲染地图模式
	 *
	 * @param result
	 * @param page
	 */
	function renderMap(res, type, location) {
		$('#waiting').css('display', 'none');
		$('#mainContainer').css('display', 'block');

		//$('#mapList').html('');
		//$('#listBoby').html('');

		//map.clearOverlays();
		//points.length = 0;
		// ==没有可视区域搜索的时候需初始化附近点
		//nearByRes = null;
		//nearByPage = 0;
		//nearByPoints.length = 0;
		// ==============================

		if(type == NEARBY_SEARCH) {
			nearByRes = res;
			nearByPoints = points;
		}

		points = renderData(res, type, location);
		$('button').on('click', function() {
		// 点击搜索按钮。则渲染shop和shop_staion的数据
			shop_id = $('#shop_id').val();
			$.ajax({
				url: 'index.php?mod=api&act=shop&opt=get_shop_info',
				dataType: 'json',
				data: {shop_id: shop_id},
			})
			.done(function(data) {
				renderDataNew(data);
				console.log("success");
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});

		});


		if(type == BOUND_SEARCH && nearByRes != null) {
			points = renderData(nearByRes, NEARBY_SEARCH, userLocation);
		}

		if (isInCurCity(userCity)) {
			var mk = new BMap.Marker(userLocation);
			mk.setIcon(getIcon(10));
			// 去除固定地图点
			// map.addOverlay(mk);
			points.push(userLocation);
//			nearByPoints.push(userLocation);
		}

//		if(type != BOUND_SEARCH && points.length != 0) {
//			map.setViewport(points);
//			map.centerAndZoom(userLocation, 15);
//		}
//
//		if(! mapShow())
//			toggleRefresh = true;
	}
	;

	function isInCurCity(city) {
		if ((curCity == city || (curCity+"市") == city) || curCity == (city+"市")) {
			return true;
		}
		return false;
	}

	function changeCity(city) {
		curCity = city;
		$('#curCity').html(city);
		$('#changeCityAction').attr('href', '/lbs/citylist.php?curcity=' + city);
	}

	function mapShow() {
		if($('#mapBox').is(":visible"));
			return false;
		return true;
	}
//渲染搜索列表页面
	function renderDataNew(e) {
//商铺
        var shop = e.data.shop[0];
        var shop_station = e.data.shop_station;
        var p = "";
        p += '<tr class="listBodyUp">';
        p +=       '<td class="shopClick">';
        p +=             '<p style="font-size:16px"> '+ shop.name +' </p>';
        p +=             '<p class="libs-desc"> '+ shop.locate +' </p>';
        p +=       '</td>';
        p +=       '<td class="bindShopBtn">';
        //p +=             '<span>222</span>'
        //p +=           '<span> <a onclick="bindShop(event, ' + shop.id + ' )";            id="bindShop">绑定地址</a> </span>';
        p +=             '<span> <a onclick="bindShopBtn(' + shop.id + ');" class="bindShop bindShopBtn">绑定地址</a> </span>';
        p +=       '</td>';
        p += '</tr>';
        $('#listBoby').html(p);
//商铺station
        var c = '';
        for(var i in shop_station){
            var point = new BMap.Point(shop_station[i].longitude,
                shop_station[i].latitude), marker = new BMap.Marker(
                point);
            var bindALabel = typeof(bindA) != "undefined"? bindA(shop_station[i].lbsid, shop_station[i].id, shop_station[i].title, shop_station[i].desc, shop_station[i].address) : "";
            console.log(shop_station[i]);
            var desc = (typeof(shop_station[i].desc) == "undefined" || shop_station[i].desc == '') ? '' : ('(' + shop_station[i].desc + ')');

            c += '<tr class="listBodyDown" style="display:none">';
            c +=       '<td>';
            c +=             '<p style="font-size:16px"> '+ shop_station[i].title +' </p>';
            c +=             '<p class="libs-desc"> '+ shop_station[i].address + desc +' </p>';
            c +=       '</td>';
            c +=       '<td class="bindAbtn">';
            // c +=          '<span style="font-size:12px; font-size:14px;"> '+ distance +'</span>'
            c +=             '<span> '+ bindALabel +'</span>';
            c +=       '</td>';
            c += '</tr>';
        }
        $('#listBoby').append(c);

        $(".shopClick").click(function(){
            $(".listBodyDown").toggle();
        })
        /*$(".listBodyUp").click(function(){
            if (clickShop) {
                return false;
            }
            clickShop = 1;
            $('#listBoby').append(c);
        });*/
    }
    function renderData(res, type, location) {
        var points = [];
        var content = res.contents;
        var nearbyContent = null;
        if(nearByRes != null && nearByRes.contents.length != 0)
            nearbyContent = nearByRes.contents;

        if (type != BOUND_SEARCH && content.length == 0) {
//          var tip = '<p style="border-top:1px solid #DDDDDD;padding-top:10px;text-align:center;text-align:center;font-size:18px;" class="text-warning">抱歉，您所在的城市没有找到充电站信息，请重新查询</p>';
//          $('#listBoby')
//          .html($(tip));
        } else {
            $.each(content,function(i, item) {
                //console.log(item);
                var point = new BMap.Point(item.location[0],
                        item.location[1]), marker = new BMap.Marker(
                        point);

                var exist = false;
                if(type == BOUND_SEARCH && nearbyContent != null) {
                    $.each(nearbyContent, function(i, nearbyItem) {
                        if(nearbyItem.uid == item.uid) {
                            exist = true;
                            return false; //break;
                        }
                    });
                    if(exist)
                        return true; //continue;
                }
                points.push(point);
                marker.addEventListener('click', showInfo);

                var distance = '';
                if(isInCurCity(userCity)) {
                    distance = map.getDistance(userLocation, point);
                    distance = distanceUnit(distance);
                }
                var bindALabel = typeof(bindA) != "undefined"? bindA(item.uid, item.sid, item.title, item.desc, item.address) : "";
                if(type != BOUND_SEARCH) {
            //list start
                    var tr = $("<tr><td>" + item.title + " (" + item.usable + "/" + (item.empty) + ")" + "<br/>地址："
                            + item.address + "</td></tr>").click(switchToShowInfo);

                        var desc = (typeof(item.desc) == "undefined" || item.desc == '') ? '' : ('(' + item.desc + ')');
                        var navigation = "http://api.map.baidu.com/direction?origin=latlng:" + userLocation.lat + "," + userLocation.lng + "|name:我的位置&destination=latlng:" + point.lat + "," + point.lng + "|name:" + item.title + "&mode=driving&region=" + userCity + "&output=html&src=云充吧|云充吧驿站";
                        //商铺名称与地址
                        /////var basicBlock = $('<p style="font-size:16px">' + item.title + '</p><p class="libs-desc">' + item.address + desc + '</p>');
                        //var distanceBlock = $("<div style='float:right;font-size:12px; text-align:center'>" + distance + "<div style='font-size:14px'><a href=" + navigation + ">到这里去</a></div></div>");
                        //距离
                        /////var distanceBlock = $("<div style='font-size:12px; text-align:center; font-size:14px;'>" + distance + "</div>");
                        ////var td = $('<td width="50%"></td>');
                        /////tr = $('<tr></tr>');

//                                      var html = "<tr><td>"
//                                               //+ "<div style='float:left;width:25px;height:25px;background:url(http://api.map.baidu.com/img/markers.png) no-repeat 2px -" + i*25 + "px;'></div>"
//                                               + item.title + " (" + item.usable + "/" + (item.empty) + ")"
//                                               + "<div style='float:right'>" + distance + "</div>"
//                                               + "<br/>地址：" + item.address
//                                               + bindALabel
//                                               + "</td></tr>";

                        ////td.append(basicBlock);
                        ////tr.append(td);

                        ////td = $('<td width="20%" style="text-align:center"></td>');
                        ////td.append(distanceBlock);
                        /////td.append($(bindALabel));
                        /////tr.append(td);

                        //td = $('<td  width="40%" style="padding-bottom:0"></td>');
                        //td.append(returnBlock);
                        //td.append(borrowBlock);
                        /////tr.append(td);

                        //////tr.click(switchToShowInfo);
                        //marker.setIcon(getIcon(i));
                    }
                    /////$('#listBoby').append(tr);
            //list end
                //}

                function switchToShowInfo() {
                    showInfo();
                }

                function showInfo() {
//                                  var content = "<p>" + item.title + "</p>"
//                                              + "<p>地址：" + item.address + "</p>";
                    var content = "地址：" + item.address;
                    var title = item.title + " (" + item.usable + "/" + (item.empty) + ")";
                    if(type == NEARBY_SEARCH)
                        title += " (" + distance + ")";
                    // 创建检索信息窗口对象
                    var infoWindow = new BMap.InfoWindow(
                            content, {
                                title : title + bindALabel, // 标题
                                enableAutoPan : true, // 自动平移
                            });
                    map.openInfoWindow(infoWindow,point);
                };
                map.addOverlay(marker);
            });
        }
        return points;

    }

    function getIcon(i) {
        return new BMap.Icon(
                "http://api.map.baidu.com/img/markers.png", new BMap.Size(
                        23, 25), {
                    offset : new BMap.Size(10, 25), // 指定定位位置
                    imageOffset : new BMap.Size(0, 0 - i * 25)
                // 设置图片偏移
                });
    }

    function distanceUnit(meter) {
        if(meter < 1000) {
            return Math.ceil(meter) + "米"; //取整
        } else {
            meter = (meter / 1000).toFixed(1);
            var meterInt = parseInt(meter);
            return (meter == meterInt ? meterInt : meter) + "千米";
        }
    }

    function getDefaultShopInfo() {
        shop_id = $('#shop_id').val();
        $.ajax({
            url: 'index.php?mod=api&act=shop&opt=get_default_shop_info',
            dataType: 'json',
        })
        .done(function(data) {
            shopListInfo(data);
            //console.log('sdfsdfsdf');
            //console.log(data);
            // 渲染
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });

    }

    /**
     * 当搜寻点或者移动点时，自动填充城市信息到填写栏内
     *
     * @param    {object}  bindPoint  地址的经纬度
     */
    function fillCityInfo(bindPoint) {
        var gc = new BMap.Geocoder();
        gc.getLocation(bindPoint, function(rs) {
            var addComp = rs.addressComponents;
            // console.log(addComp.province + addComp.city + addComp.district +
            // addComp.street + addComp.streetNumber);
            $('#get-province').val(addComp.province);
            $('#get-province').trigger('change', [addComp.province, 1]);
            $('#get-city').val(addComp.city);
            $('#get-city').trigger('change', [addComp.province, addComp.city, 1]);
            $('#get-area').val(addComp.district);
            $('#street').val(addComp.street + addComp.streetNumber);
        });
        return true;
    }
//渲染进入页面时的商铺和商铺站点页面
    function shopListInfo(e){
        console.log(e.data);
        for(var s in e.data){
            //document.write(e.data+"")
            console.log(e.data);
            var shop = e.data[s].shop[0]; // shop对象 1个商铺
            var shop_station = e.data[s].shop_station; //shop-station对象 n个站点

            //商铺
            var p = "";
            p += '<tr class="listBodyUp">';
            p +=       '<td class="shopListClick">';
            p +=             '<p style="font-size:16px"> '+shop.name +' </p>';
            p +=             '<p class="libs-desc"> '+ shop.locate +' </p>';
            p +=       '</td>';
            p +=       '<td class="bindShopBtn">';
            //p +=             '<span>222</span>'
            //p +=           '<span> <a onclick="bindShop(event, ' + shop.id + ' )";            id="bindShop">绑定地址</a> </span>';
            p +=             '<span> <a onclick="bindShopBtn(' + shop.id + ');" class="bindShop">绑定地址</a> </span>';
            p +=       '</td>';
            p += '</tr>';
            $('#listBoby').append(p);

            //商铺站点
            for(var i in shop_station){
                var c = '';
                var point = new BMap.Point(shop_station[i].longitude,
                    shop_station[i].latitude), marker = new BMap.Marker(
                    point);
                var bindALabel = typeof(bindA) != "undefined"? bindA(shop_station[i].lbsid, shop_station[i].id, shop_station[i].title, shop_station[i].desc, shop_station[i].address) : "";

                var desc = (typeof(shop_station[i].desc) == "undefined" || shop_station[i].desc == '') ? '' : ('(' + shop_station[i].desc + ')');

                c += '<tr class ="listBodyDown shopStationList-'+s+' " style="display:none">';
                c +=       '<td>';
                c +=             '<p style="font-size:16px"> '+ shop_station[i].title +' </p>';
                c +=             '<p class="libs-desc"> '+ shop_station[i].address + desc +' </p>';
                c +=       '</td>';
                c +=       '<td class="bindAbtn">';
                c +=             '<span> '+ bindALabel +'</span>';
                c +=       '</td>';
                c += '</tr>';
                $('#listBoby').append(c);
            }
        }
            $(".shopListClick").click(function(){
                var i = $(".shopListClick").index(this);
                $(".shopStationList-"+i).toggle();
            });
    }
}();


//点击绑定出现摆放位置输入框
function bindShopBtn(shop_id){
    $(".mask-bg-small").css("display","block");
    $('#bind_shop').on('click', function(event) {
		$(".mask-position-bg").css("display","block");
        bindShop(event, shop_id, $(":text[name='desc']").val());
    });
    console.log(shop_id);
}


$(".cancel").click(function(){
    $(".mask-bg-small").css("display","none")
})

function formToggle() {
    $('form').css("display","block");
    $(".mask-bg").css("display","block");
}

function formCancel(){
    $('form').css("display","none");
    $(".mask-bg").css("display","none");
}
