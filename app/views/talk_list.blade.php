@extends('templates.main')
	
@section('content')
    @if (Session::has('message'))
    <div class="alert alert-info alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ Session::get('message') }}
    </div>
    @endif
   @if (Session::has('error'))
    <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {{ Session::get('error') }}
    </div>
    @endif
	<h2>{{ $title }}</h2>
 	{{ Table::striped_bordered_hover_condensed_open() }}
	{{ Table::headers('Talk Name', 'Location', 'Date') }}
    <tbody class="talks-table">
    @foreach ($talks as $talk)
    <tr href="{{ URL::to('talk')}}/{{ $talk->id }}">
    <td><a href="{{ URL::to('talk')}}/{{ $talk->id }}">
    @if ($admin_view)
    @if ($talk->status == 'pending')
    <span class="glyphicon glyphicon-edit"></span>
    @else
    <span class="glyphicon glyphicon-ok"></span>
    @endif
    @endif
    	{{ $talk->title }}
	</a>
	</td>
        <td>{{ $talk->location }}</td>
        <td>{{ date('d/m/Y', strtotime($talk->date_start)) }}</td>
    </tr>
    @endforeach
    <tbody>
    {{ Table::close() }}
    <div class='pull-right'>
    	{{ $talks->links() }}
    </div>

@stop

