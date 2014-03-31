@extends('templates.email')
	
@section('body')

<p>{{ trans('email.statusTalkPt1') }}<strong>
@if ($status != 'deleted')
<a href="{{ URL::to('talk/'.$talk->id) }}">
@endif
{{ $talk->title }}
@if ($status != 'deleted')
</a>
@endif
</strong> {{ trans('email.statusTalkPt2') }} {{ trans('email.statusTalk_'.$status) }}.</p>
@if ($status != 'deleted')
<p>{{ trans('email.statusTalkPt3') }}</p>
<p> {{ URL::to('talk/'.$talk->id) }} </p>
@endif
@stop
