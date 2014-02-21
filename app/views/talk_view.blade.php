@extends('templates.main')
	
@section('content')
	<h2>{{ $talk->title }}
	<br><small><em>
	<!-- there might be a better way to show the array contents! -->
	@foreach ($speaker_names as $name)
	{{ $name }}, 
	@endforeach
	</em></small></h2>
	<p class="lead">{{ $talk->aim }}</p>
{{ Typography::horizontal_dl(
    array(
    "Target" 		=> $talk->target? htmlentities($talk->target) : "Not defined yet",
    "Requirements"	=> $talk->requirements? $talk->requirements : "None",
    "Description" 	=> $talk->description? $talk->description: "Not available",

    "Date start"	=> $talk->date_start? $talk->date_start : "TBD",
    "Date end"		=> $talk->date_end? $talk->date_start : "TBD",
    "Available places"	=> $talk->places? $talk->places : "TBD", // might want to change it later
    "Location"		=> $talk->location? $talk->location: "TBD",
    )
)
}}
@stop

