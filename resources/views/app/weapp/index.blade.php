@extends('app.weapp.app')

@section('htmlheader_title')
    资讯
@endsection

@section('main-container')
        <!-- content here -->
        <div class="container index-body">
            <div class="row">
	            <div class="col-sm-9">
	            	<div id="news_list">
		            	<!-- @ foreach ($newsItems as $item) -->
			            <div class="panel news-panel-item">
			              <div class="panel-heading">
			              	<h4><a class="content-item-title" href="/news/detail?id=xx">title</a></h4>
			              	<div class="content-item-title-bottom">
			              	  <div style="float:left; height:30px; line-height:30px">
				              	  <span class="content-item-title-bottom"><span class="release_time">Time </span> / source site / 阅读量(0)</span> 
				              	  <a href="xxx" class="theme-color">原文</a>
			              	  </div>
			              	  <div style="float:left; margin-left:1.0rem" class="ds-share flat" data-thread-key="0" data-title="xxx" data-images="xx" data-content="xxx" data-url="http://www.hey-z.com/news/detail?id=xxx">
							    <div class="ds-share-inline">
							      <ul  class="ds-share-icons-16">
							      	<li data-toggle="ds-share-icons-more"><a class="ds-more" href="javascript:void(0);" data-service="wechat">分享</a></li>
							      </ul>
							      <div class="ds-share-icons-more">
      							  </div>
							    </div>
							   </div>
			              	  <!-- <span class="glyphicon glyphicon-heart content-item-title-bottom" aria-hidden="true"></span> -->
			              	  <!-- <span class="glyphicon glyphicon-share-alt content-item-title-bottom" aria-hidden="true"></span> -->
			              	  <!-- <span class="glyphicon glyphicon-share content-item-title-bottom" aria-hidden="true"></span> -->
			              	</div>
			              </div>
			              <div class="news-panel-body">
			                <div>
			                    Abstract
			                </div>
			              </div>
			            </div>
			            <!-- @ endforeach -->
		            </div>
		            <button id="addMoreBtn" type="button" class="btn btn-block add-more" onclick="addMore()">加载更多</button>
	            </div><!-- /.main -->
	            
	            <div class="col-sm-3">
		          <div class="bs-callout">
		          	<span class="span-mark"></span>
		            <div class="mark-text">本月阅读排行</div>
		            <div class="list-group">
		            <!-- @ foreach ($rankPV as $item) -->
		              <a href="/news/detail?id=xx" class="list-group-item clearfix">
		              	<span class="rank-item-title">title</span>
		              	<span class="badge"><span class="release_time">release time</span></span>
		              	<!-- <span class="badge">{{ $item['pv'] or 0 }}</span> -->
		              </a>
		            <!-- @ endforeach -->
					</div>
		            <div class="rank-list">
		            </div>
		          </div>
		        </div>
		        <div class="col-sm-3">
		          <div class="bs-callout">
		          	<span class="span-mark"></span>
		            <div class="mark-text">智能分类</div>
		            <div class="list-group">
		            <!-- @ foreach ($tagItems as $item) -->
		              <a href="/news?key=xxx" class="list-group-item clearfix">
		              	<span class="rank-item-title"> tag name</span>
		              </a>
		            <!-- @ endforeach -->
					</div>
		          </div>
		        </div>
		        <!-- /.sidebar -->
	        </div>
        </div><!-- /.container -->
@stop
