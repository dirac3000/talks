@extends('templates.main')
	
@section('content')
<h2>{{ trans('messages.usersTitle') }}</h2>
<div class="text-center control-group">
<form id="user_list" class="form-inline" role="form" action="{{ URL::to('user_list') }}">
{{ Form::label('search', trans('messages.usersName') ) }} 
{{ Form::text('search') }} 
<button type="submit" class='btn btn-success '><span class="glyphicon glyphicon-search"></span> {{ trans('messages.usersSearch') }}</button>
</form>
{{ $errors->first('search', Alert::error(":message")) }}
</div>
<br>
<div class="control-group">
 	{{ Table::striped_bordered_hover_condensed_open() }}
	{{ Table::headers( trans('messages.usersUsername'),  trans('messages.usersName'),  trans('messages.usersRights')) }}
    <tbody class="talks-table">
    @foreach ($users as $user)
    <tr href="{{ URL::to('user')}}/{{ $user->id }}">
    <td><a href="{{ URL::to('user')}}/{{ $user->id }}">
    	{{ $user->username }}
	</a>
	</td>
        <td>{{ ucwords(strtolower($user->name)) }}</td>
        <td>{{ trans('messages.userRights_'. $user->rights) }}</td>
    </tr>
    @endforeach
    <tbody>
    {{ Table::close() }}
    <div class='pull-right'>
    	{{ $users->links() }}
	</div>
</div>
@stop


