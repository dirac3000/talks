@extends('templates.main')
	
@section('content')
	<h2>{{ ucwords(strtolower($user->name)) }}</h2>

<dl class="dl-horizontal">
	<dt>Username</dt><dd>{{ $user->username? htmlentities($user->username) : "N/A" }}</dd>
	<dt>Email</dt><dd>{{ $user->email ? $user->email : "N/A" }}</dd>
	<dt>Manager</dt><dd>
	@if ($manager)
	<a href="{{ URL::to('user/'.$manager->id) }}">{{ ucwords(strtolower($manager->name)) }}</a>
	@else
	No Manager
	@endif
	</dd>
	<dt>Rights</dt><dd>{{ ucwords($user->rights) }}
@if ($admin_view && $user->id != Auth::user()->id) |
@if ($user->rights == "simple") 
   {{ HTML::link('/user/'.$user->id.'/rights=admin' , 'Change to administrator') }}
@else
   {{ HTML::link('/user/'.$user->id.'/rights=simple' , 'Change to simple') }}
@endif
@endif
</em>
	</dd>
	</dl>

@if ($reservations)
<h4>Reservations</h4>

{{ Table::open() }}
{{ Table::headers('Talk', 'Date', 'Status') }}
@foreach ($reservations as $res)
<tr>
<td><a href="{{ URL::to('talk/'.$res->talk_id) }}">{{ $res->title }}</a></td>
<td>{{ $res->date_start }}</td>
<td>
@if ($res->status == 'pending')
<span class="text-warning">Awaiting approval
@elseif ($res->status == 'approved')
<span class="text-success">Confirmed
@else
<span class="text-danger"><strong>Refused</strong>
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
<h4>Team Reservations</h4>
{{ Form::open(array('url' => 'res_mgr/'.$user->id)) }}

{{ Table::open() }}
{{ Table::headers('User Name', 'Talk', 'Date', 'Status', 'Comment') }}
@foreach ($mgr_reservations as $res)
<tr>
<td><a href="{{ URL::to('user/'.$res->user_id) }}">{{ $res->name }}</a></td>
<td><a href="{{ URL::to('talk/'.$res->talk_id) }}">{{ $res->title }}</a></td>

<td>{{ $res->date_start }}</td>
<td>
<select name="status[{{ $res->id }}]" class="form-control input-sm">
<option value="pending" {{ $res->status == 'pending'? 'selected' : '' }} >Awaiting</option>
<option value="approved" {{ $res->status == 'approved'? 'selected' : '' }} >Confirmed</option>
<option value="refused" {{ $res->status == 'refused'? 'selected' : '' }} >Refused</option>
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
<button type="submit" class='btn btn-primary pull-right'>Save Reservations</button>
{{ Form::close() }}
@endif

@stop


