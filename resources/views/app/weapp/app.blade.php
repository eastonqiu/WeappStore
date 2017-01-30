@extends('app')

@section('html_custom_css')
	@yield('app_custom_css', '')
@endsection

@section('html_custom_js')

	<script src="{{ asset('/js/vendor/moment.min.js') }}"></script>
	<script src="//cdn.bootcss.com/moment.js/2.11.2/locale/zh-cn.js"></script>
  @yield('app_custom_js', '')
@endsection

@section('main-content')
    <!-- navbar -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="/news">首页</a>
              <a href="#" class="navbar-brand visible-xs collapse-pull-right glyphicon glyphicon-search" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" aria-hidden="true"></a>
              
<!--               <span class="navbar-brand visible-xs glyphicon glyphicon-search collapse-pull-right" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" aria-hidden="true"></span> -->
              
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <form id="search_form" class="navbar-form navbar-left" role="search" action="news">
		        <div class="input-group">
		          <input name="key" type="text" class="form-control" value="{{ $key or '' }}" placeholder="搜索一下">
		          <i class="input-group-addon glyphicon glyphicon-search adjust-addon-icon" onclick="$('#search_form').submit();"></i>
		        </div>
		      </form>
              <ul class="nav navbar-nav navbar-right">
                <!-- 
                <li><a href="#filter">人工过滤</a></li>
                <li><a href="#category">智能分类</a></li>
                 -->
                <li><a href="/aboutus">关于我们</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav>

        <!-- content here -->
        @yield('main-container')
@stop