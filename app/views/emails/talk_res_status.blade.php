@extends('templates.email')
	
@section('body')

<p>{{ trans('email.resStatusPt1') }}<strong><a href="{{ URL::to('talk/'.$talk->id) }}">{{ $talk->title }}</a></strong>.</p>
			<p>{{ trans('email.resStatusPt2') }} <em>{{ trans('email.resStatus_'.$res->status) }}</em></p>
@if (($res->comment != null) && (strlen($res->comment) > 0))
<p>{{ trans('email.resStatusPt3') }}</p>
<blockquote>{{ $res->comment }}</blockquote>
@endif
<p>{{ trans('email.resStatusPt4') }}</p>
<p> {{ URL::to('user/'.$res->user_id) }} </p>
@stop
