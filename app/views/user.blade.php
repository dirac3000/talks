@extends('templates.main')
	
@section('content')
	<h2>{{ ucwords(strtolower($user->name)) }}</h2>

<dl class="dl-horizontal">
	<dt>{{ trans('messages.user') }}</dt><dd>{{ $user->username? htmlentities($user->username) : trans('messages.viewNA') }}</dd>
	<dt>{{ trans('messages.userEmail') }}</dt><dd>{{ $user->email ? $user->email : trans('messages.viewNA') }}</dd>
	<dt>{{ trans('messages.userManager') }}</dt><dd>
	@if ($manager)
	<a href="{{ URL::to('user/'.$manager->id) }}">{{ ucwords(strtolower($manager->name)) }}</a>
	@else
	{{ trans('messages.userNoManager') }}
	@endif
	</dd>
	<dt>{{ trans('messages.userRights') }}</dt><dd>{{ trans('messages.userRights_'.($user->rights)) }}
@if ($admin_view && $user->id != Auth::user()->id) |
@if ($user->rights == "simple") 
   {{ HTML::link('/user/'.$user->id.'/rights=admin' , trans('messages.userRightsMkAdmin')) }}
@else
   {{ HTML::link('/user/'.$user->id.'/rights=simple' , trans('messages.userRightsMkSimple')) }}
@endif
@endif
</em>
	</dd>
	</dl>

@if ($reservations)
<h4>{{ trans('messages.userReservations') }}</h4>

{{ Table::open() }}
{{ Table::headers('Talk', 'Date', 'Status') }}
@foreach ($reservations as $res)
<tr>
<td><a href="{{ URL::to('talk/'.$res->talk_id) }}">{{ $res->title }}</a></td>
<td>{{ $res->date_start }}</td>
<td>
@if ($res->status == 'pending')
<span class="text-warning">{{ trans('messages.userPending') }}
@elseif ($res->status == 'approved')
<span class="text-success">{{ trans('messages.userConfirmed') }}
@else
<span class="text-danger"><strong>{{ trans('messages.userPending') }}</strong>
@endif
@if ($res->comment != '')
: {{ $res->comment }}
@endif
</span></td>
</tr>
@endforeach
{{ Table::close() }}

@endif


@if ($mgr_reservations)
<h4>{{ trans('messages.userTeamRes }}</h4>
{{ Form::open(array('url' => 'res_mgr/'.$user->id)) }}

{{ Table::open() }}
{{ Table::headers( trans('messages.userTResName'), trans('messages.userTResTalk'), trans('messages.userTResDate'), trans('messages.userTResStatus'), trans('messages.userTResComment')) }}
@foreach ($mgr_reservations as $res)
<tr>
<td><a href="{{ URL::to('user/'.$res->user_id) }}">{{ $res->name }}</a></td>
<td><a href="{{ URL::to('talk/'.$res->talk_id) }}">{{ $res->title }}</a></td>

<td>{{ $res->date_start }}</td>
<td>
<select name="status[{{ $res->id }}]" class="form-control input-sm">
<option value="pending" {{ $res->status == 'pending'? 'selected' : '' }} >{{ trans('messages.userTResPending') }}</option>
<option value="approved" {{ $res->status == 'approved'? 'selected' : '' }} >{{ trans('trans('messages.userTResConfirmed') }}</option>
<option value="refused" {{ $res->status == 'refused'? 'selected' : '' }} >{{ trans('messages.userTResRefused') }}</option>
</select>
</td>
<td>
{{ Form::text('comment['.$res->id.']', $res->comment,
array( 'class' => 'form-control input-sm small'))}}
</td>

</td>
</tr>
@endforeach
{{ Table::close() }}
@if (Session::has('reservation_errors'))
<div class="alert alert-danger">{{ Session::get('reservation_errors') }}</div>
@endif
<button type="submit" class='btn btn-primary pull-right'>{{ trans('messages.userTResSave') }}</button>
{{ Form::close() }}
@endif

@stop


