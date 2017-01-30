<!doctype html>
<html class="no-js" lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        
    	<!-- iOS 设备 begin -->
	    <meta name="apple-mobile-web-app-title" content="搜资讯">
	    <!-- 添加到主屏后的标题（iOS 6 新增） -->
	    <meta name="apple-mobile-web-app-capable" content="yes"/>
	    <!-- 是否启用 WebApp 全屏模式，删除苹果默认的工具栏和菜单栏 -->
	    <!-- <meta name="apple-itunes-app" content="app-id=myAppStoreID, affiliate-data=myAffiliateData, app-argument=myURL"> -->
	    <!-- 添加智能 App 广告条 Smart App Banner（iOS 6+ Safari） -->
	    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
	    <!-- 设置苹果工具栏颜色 -->
    	<meta http-equiv="Cache-Control" content="no-siteapp" />
    	<!-- iOS 设备 end -->
    	
    	<!-- 不让百度转码 -->
    	<meta name="renderer" content="webkit">
    	<!-- 启用360浏览器的极速模式(webkit) -->
    	<meta name="HandheldFriendly" content="true">
    	<!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
    	
    	<meta name="msapplication-TileColor" content="#000"/>
	    <!-- Windows 8 磁贴颜色 -->
	    <meta name="msapplication-TileImage" content="{{ asset('/images/favicon.ico') }}/>
	    <!-- Windows 8 磁贴图标 -->
        
        <!-- custom below -->
        <meta name="description" content="">
        <link rel="icon" href="{{ asset('/images/favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('/images/apple-touch-icon.png') }}">

        <title>@yield('htmlheader_title', 'Your title here') </title>

        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
        <!-- Custom styles for this template -->
        @yield('html_custom_css', '')

        <script src="{{ asset('/js/vendor/modernizr-2.8.3.min.js') }}"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        @yield('main-content')

		<div id="footer">
		  <div class="container text-center">
		    <p class="text-muted credit"><a href="/aboutus">关于我们</a>：service@hey-z.com</p>
			<p class="text-muted credit"> &copy; copyright ~2016</p>
		  </div>
		</div>
        <!-- js script -->
        <script src="{{ asset('/js/app.js') }}"></script>
         <!-- Custom js script for this template -->
        @yield('html_custom_js', '')

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <!--
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
        -->
    </body>
</html>
