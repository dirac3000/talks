@extends('templates.email')
	
@section('body')

<p>{{ trans('email.delTalkPt1') }}<strong>{{ $talk->title }}</strong> {{ trans('email.delTalkPt2') }}</p>
<p>{{ trans('email.delTalkPt3') }}</p>

@stop


