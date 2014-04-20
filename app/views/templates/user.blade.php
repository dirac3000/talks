@extends('templates.main')
	
@section('content')
<h2 class="row"><span class="col-md-10">{{ $user->name }} </span>
@if (Session::has('user_admin_actions'))
<div class="col-md-2 pull-right">
	{{ Form::open(array('id' => 'delete_user', 'url' => 'user/'. $user->id.'/delete', 'method' => 'delete')) }}
	<button type="submit" class="btn btn-danger "><span class="glyphicon glyphicon-remove"></span> {{ trans('messages.userDelete') }}</butfon>
	{{ Form::close() }}
</div>
@endif
</h2>

@yield('user_nav_settings')
@if (Session::has('user_admin_actions'))
<ul class="nav nav-tabs">

<li {{  ($tab_selected == 'view')? 'class="active"' : '' }}>
{{  ($tab_selected == 'view')? '<a href="#">' : '<a href="'. URL::to('user/'.$user->id) .'">' }}
{{ trans('messages.userView') }}</a></li>
<li {{  ($tab_selected == 'edit')? 'class="active"' : '' }}>
{{  ($tab_selected == 'edit')? '<a href="#">' : '<a href="'. URL::to('user/'.$user->id.'/edit') .'">' }}
{{ trans('messages.userEdit') }}</a></li>
</ul>
@endif

@yield('user_main')
@stop

@section('javascripts')
@parent

@if (Session::has('user_admin_actions'))
    <script>

	$('#delete_user').submit(function(){
		return confirm("{{ trans('messages.userDeleteMsg') }}");
	});
    </script>
@endif
@stop

