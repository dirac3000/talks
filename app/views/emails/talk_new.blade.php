@extends('templates.email')
	
@section('body')

<p>{{ trans('email.newTalkPt1') }} <strong><a href="{{ URL::to('talk/'.$talk->id) }}">{{ $talk->title }}</a></strong>.</p>
<p>{{ trans('email.newTalkPt2') }}</p>
<p> {{ URL::to('talk/'.$talk->id) }} </p>

@stop

