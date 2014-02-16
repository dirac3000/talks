@extends('templates.main')
	
@section('content')
	<h1>Upcoming Talks</h1>
	{{ Table::striped_bordered_hover_condensed_open() }}
	{{ Table::headers('Talk Name', 'Location', 'Date') }}
    <tbody class="talks-table">
    @foreach ($descriptions as $descr)
    <tr href="{{ URL::to('talk')}}/{{ $descr->id }}">
        <td><a href="{{ URL::to('talk')}}/{{ $descr->id }}">{{ $descr->title }}</a></td>
        <td>{{ $descr->location }}</td>
        <td>{{ date('d/m/Y', strtotime($descr->date_start)) }}</td>
    </tr>
    @endforeach
    <tbody>
        {{ Table::close() }}
@stop

