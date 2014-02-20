@extends('templates.main')
	
@section('content')
	<h2>{{ $talk->title }}
	<br><small>
	<!-- there might be a better way to show the array contents! -->
	@foreach ($speaker_names as $name)
	{{ $name }}, 
	@endforeach
	</small></h2>
	<p class="lead">{{ $talk->aim }}</p>
{{ Typography::horizontal_dl(
    array(
    "Target" => htmlentities($talk->target),
    "Requirements"	=> $talk->requirements,
    "Description" 	=> $talk->description,

    "Date start"	=> $talk->date_start,
    "Date end"		=> $talk->date_end,
    "Available places"	=> $talk->places, // might want to change it later
    "Location"		=> $talk->location,
    )
)
}}
@stop

