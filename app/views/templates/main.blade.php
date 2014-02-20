<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Talks</title>
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
          <a class="navbar-brand" href="{{ URL::to('/') }}">Talks</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="{{ URL::to('/') }}">Home</a></li>
              @if ( !Auth::guest() )
              <li><a href="{{ URL::to('talk_new') }}">Create New</a></li>
              @endif
          </ul>
        <ul class="nav navbar-nav navbar-right">
            <li>
            @if ( Auth::guest() )
              <a href="{{ URL::to('login')}}">
                <span class="glyphicon glyphicon-user"></span> Login
              </a>
            @else
               {{ HTML::link('admin', 'Welcome, '. Auth::user()->username) }} 
               </li><li>
                {{ HTML::link('logout', 'Logout') }} 
            @endif
            </li>
        </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>


    @section('main')
    <div class="container">
          <div>
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