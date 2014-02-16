@extends('templates.main')
	
@section('content')
	<h1>{{ $description->title }}</h1>
    <!-- Speakers here -->
    <p class="lead">{{ $description->aim }}</p>
{{ Typography::horizontal_dl(
    array(
    "Target" => $description->target,
    "Requirements" => $description->requirements,
    "Description" => $description->description,
    )
)
}}
@stop

