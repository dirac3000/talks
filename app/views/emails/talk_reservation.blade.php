@extends('templates.email')
	
@section('body')

<p>{{ trans('email.resAskPt1') }} {{ ucwords(strtolower($user->name)) }} {{ trans('email.resAskPt2') }} <strong><a href="{{ URL::to('talk/'.$talk->id) }}">{{ $talk->title }}</a></strong>.</p>
			<p>{{ trans('email.resAskPt3') }}</p>
<p> {{ URL::to('user/'.$user->manager_id) }} </p>
@stop

