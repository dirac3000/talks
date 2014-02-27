@extends('templates.main')
	
@section('content')
	<h2>{{ ucwords(strtolower($user->name)) }}</h2>

<dl class="dl-horizontal">
	<dt>Username</dt><dd>{{ $user->username? htmlentities($user->username) : "N/A" }}</dd>
	<dt>Email</dt><dd>{{ $user->email ? $user->email : "N/A" }}</dd>
	<dt>Manager</dt><dd>{{ $manager ? $manager->name: "No manager" }}</dd>
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
@stop


