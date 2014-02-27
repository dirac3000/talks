@extends('templates.main')
	
@section('content')
	<h2>Users</h2>
 	{{ Table::striped_bordered_hover_condensed_open() }}
	{{ Table::headers('Username', 'Name', 'Rights') }}
    <tbody class="talks-table">
    @foreach ($users as $user)
    <tr href="{{ URL::to('user')}}/{{ $user->id }}">
    <td><a href="{{ URL::to('user')}}/{{ $user->id }}">
    	{{ $user->username }}
	</a>
	</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->rights }}</td>
    </tr>
    @endforeach
    <tbody>
    {{ Table::close() }}
    <div class='pull-right'>
    	{{ $users->links() }}
    </div>
@stop


