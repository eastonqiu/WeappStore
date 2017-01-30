@extends('app.weapp.app')

@section('htmlheader_title')
    资讯
@endsection


@section('main-container')
        <!-- content here -->
        <div class="container">
            <div class="row">
	            <div class="col-sm-9 detail-container">
		            <div class="panel">
		              <div class="panel-heading detail-heading">
		              	<h4><a class="content-item-title" href="#">title</a></h4>
		              	<div class="content-item-title-bottom">
		              	  <div style="float:left; height:30px; line-height:30px">
			              	  <span class="content-item-title-bottom"><span class="release_time">release time</span> / source site / 阅读量(0)</span> 
		              	  	  <a href="" class="theme-color">原文</a>
		              	  </div>
		              	  <div style="float:left; margin-left:1.0rem" class="ds-share flat" data-thread-key="id" data-title="title" data-images="xxx" data-content="abstract" data-url="http://www.hey-z.com/news/detail?id=xxx">
						  	<div class="ds-share-inline">
								<ul  class="ds-share-icons-16">
							    	<li data-toggle="ds-share-icons-more"><a class="ds-more" href="javascript:void(0);" data-service="wechat">分享</a></li>
							    </ul>
							    <div class="ds-share-icons-more"></div>
							</div>
						  </div>
		              	  
		              	  <!-- <span class="glyphicon glyphicon-heart content-item-title-bottom" aria-hidden="true"></span>
		              	  <span class="glyphicon glyphicon-share-alt content-item-title-bottom" aria-hidden="true"></span>
		              	  <span class="glyphicon glyphicon-share content-item-title-bottom" aria-hidden="true"></span>
		              	   -->
		              	</div>
		              </div>
		              <div class="detail-content">
                        content
		              </div>
                    <!-- 多说评论框 start -->
                      <div class="ds-thread" data-thread-key="id" data-title="title" data-url="/news/detail?id=x"></div>
                    <!-- 多说评论框 end -->
		            </div>
	            </div><!-- /.main -->
	            
	            <div class="col-sm-3">
		          <div class="bs-callout">
		          	<span class="span-mark"></span>
		            <div class="mark-text">相关资讯</div>
		            <div class="list-group">
		            
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
		           
		            </div>
		          </div>
		        </div>
		        <!-- /.sidebar -->
	        </div>
        </div><!-- /.container -->
@stop
