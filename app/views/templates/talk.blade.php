@extends('templates.main')
	
@section('content')
<h2>{{ $talk->title }} 
@if ($talk->status == 'pending')
<span class="badge">{{ trans('messages.talkPending') }}</span>
@elseif ($talk->status == 'cancelled')
<span class="badge">{{ trans('messages.talkCancelled') }}</span>
@endif
</h2>

@yield('talk_nav_settings')
@if ($talk_rights != null)
<ul class="nav nav-tabs">
@if ($tab_selected == 'view')
  <li class="active"><a href="#">{{ trans('messages.talkView') }}</a></li>
  <li><a href="{{ URL::to('talk_edit/'.$talk->id) }}">{{ trans('messages.talkEdit') }}</a></li>
  <li><a href="{{ URL::to('talk_attach/'.$talk->id) }}">{{ trans('messages.talkAdd') }}</a></li>
@elseif ($tab_selected == 'edit')
  <li><a href="{{ URL::to('talk/'.$talk->id) }}">{{ trans('messages.talkView') }}</a></li>
  <li class="active"><a href="#">{{ trans('messages.talkEdit') }}</a></li>
  <li><a href="{{ URL::to('talk_attach/'.$talk->id) }}">{{ trans('messages.talkAdd') }}</a></li>
@elseif ($tab_selected == 'attach')
  <li><a href="{{ URL::to('talk/'.$talk->id) }}">{{ trans('messages.talkView') }}</a></li>
  <li><a href="{{ URL::to('talk_edit/'.$talk->id) }}">{{ trans('messages.talkEdit') }}</a></li>
  <li class="active"><a href="#">{{ trans('messages.talkAdd') }}</a></li>
@endif

@if ($talk_rights == 'admin')
  <li><a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="#">
{{ trans('messages.talkActions') }}<span class="caret"></span>
  </a>
  	<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
@if ($talk->status != 'approved')
	<li>
	{{ Form::open(array( 'id' => 'confirm_talk', 'url' => 'talk_status/'.$talk->id)) }}
	{{ Form::hidden('talk_status', 'approved') }}
	<button type="submit" class="btn btn-link ">{{ trans('messages.talkActConfirm') }}</butfon>
 	{{ Form::close() }}
	</li>
@endif
@if ($talk->status != 'cancelled')
	<li>
	{{ Form::open(array( 'id' => 'cancel_talk', 'url' => 'talk_status/'.$talk->id)) }}
	{{ Form::hidden('talk_status', 'cancelled') }}
	<button type="submit" class="btn btn-link ">{{ trans('messages.talkActCancel') }}</butfon>
 	{{ Form::close() }}
	</li>
@endif
	<li role="presentation" class="divider"></li>
	<li>
	{{ Form::open(array('id' => 'delete_talk', 'url' => 'talk_delete/'. $talk->id, 'method' => 'delete')) }}
	<button type="submit" class="btn btn-link ">{{ trans('messages.talkActDelete') }}</butfon>
 	{{ Form::close() }}
	</li>


  </ul></li>
@endif
  </ul>
@endif

@yield('talk_main')
@stop

@section('javascripts')
@parent
@if ($talk_rights != null)
    <script>
	$('#confirm_talk').submit(function(){
		return confirm("{{ trans('messages.talkConfirmMsg') }}");
	});
	$('#cancel_talk').submit(function(){
		return confirm("{{ trans('messages.talkConfirmMsg') }}");
	});
	$('#delete_talk').submit(function(){
		return confirm("{{ trans('messages.talkConfirmMsg') }}");
	});
    </script>
@endif
@stop

