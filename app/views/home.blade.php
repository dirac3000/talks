@extends('templates.main')
	
@section('content')
    @if (Session::has('logout_message'))
    <div class="alert alert-info alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      Logout successful.
    </div>
    @endif
   @if (Session::has('error'))
    <div class="alert alert-danger alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    @if (Session::get('error') == 'unauthorized') 
        Unauthorized action.
    @else
        Unexpected error.
    @endif
    </div>
    @endif
	<h2>Upcoming Talks</h2>
 	{{ Table::striped_bordered_hover_condensed_open() }}
	{{ Table::headers('Talk Name', 'Location', 'Date') }}
    <tbody class="talks-table">
    @foreach ($talks as $talk)
    <tr href="{{ URL::to('talk')}}/{{ $talk->id }}">
        <td><a href="{{ URL::to('talk')}}/{{ $talk->id }}">{{ $talk->title }}</a></td>
        <td>{{ $talk->location }}</td>
        <td>{{ date('d/m/Y', strtotime($talk->date_start)) }}</td>
    </tr>
    @endforeach
    <tbody>
    {{ Table::close() }}
@stop

