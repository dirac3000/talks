<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{{ trans('messages.mainTitle') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Talks Sessions Management">
    <meta name="author" content="Alvaro Moran">
    @section('styles')
    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/bootstrap-datetimepicker.min.css') }}
    {{ HTML::style('css/bootstrap-theme.min.css') }}
    {{ HTML::style('css/talks.css') }}
    @show
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
  <body>
      <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ URL::to('/') }}">{{ trans('messages.mainTalks') }}</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="{{ URL::to('/') }}">{{ trans('messages.mainUpcoming') }}</a></li>
            <li><a href="{{ URL::to('past') }}">{{ trans('messages.mainPast') }}</a></li>
              @if ( $admin_view )
              <li><a href="{{ URL::to('talk_new') }}">{{ trans('messages.mainNewTalk') }}</a></li>
              <li><a href="{{ URL::to('user_list') }}">{{ trans('messages.mainUsers') }}</a></li>

              @endif
          </ul>
        <ul class="nav navbar-nav navbar-right">
            @if ( Auth::guest() )
	    	<li>
	    	<a href="{{ URL::to('login')}}">
                <span class="glyphicon glyphicon-user"></span> {{ trans('messages.mainLogin') }}
		</a>
		</li>
	    @else
		    <li class="dropdown">
		    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		    <span class="glyphicon glyphicon-user"></span> {{ ucwords(strtolower(Auth::user()->name)) }}
		    <b class="caret"></b></a>
		<ul class="dropdown-menu">
		<li></li>
		<li>
		    		    
		{{ HTML::link(URL::to('user/'.Auth::user()->id), trans('messages.mainProfile')) }} 		    
		</li>
		<li>
		{{ HTML::link('logout', trans('messages.mainLogout')) }} 
		</li></ul>
  	        </li>
            @endif
        </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>


    @section('main')
    <div class="container">
          <div>
    @if (Session::has('message'))
    <div class="alert alert-info alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ Session::get('message') }}
    </div>
    @endif
   @if (Session::has('error'))
    <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ Session::get('error') }}
    </div>
    @endif
          @yield('content')
          </div>
          @yield('pagination')
    </div><!--/container-->
    @show

    @section('javascripts')
    <!-- Scripts are here so page loads faster -->
    {{ HTML::script('js/jquery-1.11.0.min.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}
    {{ HTML::script('js/bootstrap-datetimepicker.min.js') }}
    {{ HTML::script('js/talks.js') }}
    @show


</body>
</html>
