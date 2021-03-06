@extends('templates.main')
	
@section('content')
	<h2>{{ $title }}</h2>
 	{{ Table::striped_bordered_hover_condensed_open() }}
	{{ Table::headers(trans('messages.listName'), trans('messages.listLocation'), trans('messages.listDate')) }}
    <tbody class="talks-table">
    @foreach ($talks as $talk)
    <tr href="{{ URL::to('talk')}}/{{ $talk->id }}">
    <td><a href="{{ URL::to('talk')}}/{{ $talk->id }}">
    @if ($admin_view)
    @if ($talk->status == 'pending')
    <span class="glyphicon glyphicon-edit"></span>
    @elseif ($talk->status == 'cancelled')
    <span class="glyphicon glyphicon-ban-circle"></span>
    @else
    <span class="glyphicon glyphicon-ok"></span>
    @endif
    @endif
    	{{ $talk->title }}
	</a>
	</td>
        <td>{{ $talk->location }}</td>
        <td>{{ ($talk->date_start != 0) ?  date('d/m/Y', strtotime($talk->date_start)) : trans('messages.viewTBD') }}</td>
    </tr>
    @endforeach
    <tbody>
    {{ Table::close() }}
    <div class='pull-right'>
    	{{ $talks->links() }}
    </div>

@stop

