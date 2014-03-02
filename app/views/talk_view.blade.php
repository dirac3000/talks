@extends('templates.main')
	
@section('content')
<h2>{{ $talk->title }} 
@if ($talk->status == 'pending')
<span class="badge">Awaiting Confirmation</span>
@elseif ($talk->status == 'cancelled')
<span class="badge">Cancelled</span>
@endif
</h2>

@if ($talk_rights != null)
<ul class="nav nav-tabs">
  <li class="active"><a href="#">View</a></li>
  <li><a href="{{ URL::to('talk_edit/'.$talk->id) }}">Edit</a></li>

@if ($talk_rights == 'admin')
  <li><a id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="#">
    Actions<span class="caret"></span>
  </a>
  	<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
@if ($talk->status != 'approved')
	<li>
	{{ Form::open(array( 'id' => 'confirm_talk', 'url' => 'talk_status/'.$talk->id)) }}
	{{ Form::hidden('talk_status', 'approved') }}
	<button type="submit" class="btn btn-link ">Confirm</butfon>
 	{{ Form::close() }}
	</li>
@endif
@if ($talk->status != 'cancelled')
	<li>
	{{ Form::open(array( 'id' => 'cancel_talk', 'url' => 'talk_status/'.$talk->id)) }}
	{{ Form::hidden('talk_status', 'cancelled') }}
	<button type="submit" class="btn btn-link ">Cancel</butfon>
 	{{ Form::close() }}
	</li>
@endif
	<li role="presentation" class="divider"></li>
	<li>
	{{ Form::open(array('id' => 'delete_talk', 'url' => 'talk_delete/'. $talk->id, 'method' => 'delete')) }}
	<button type="submit" class="btn btn-link ">Delete</butfon>
 	{{ Form::close() }}
	</li>


  </ul></li>
  </ul>
@endif
@endif

    <div class="container">
      <div class="col-md-8"> 
	<h2><small><em>
	{{  ucwords(strtolower(implode(', ', (array)$speaker_names))) }}
	</em></small></h3>
	<p class="lead">{{ $talk->aim }}</p>
{{ Typography::horizontal_dl(
    array(
    "Target" 		=> $talk->target? htmlentities($talk->target) : "Not defined yet",
    "Requirements"	=> $talk->requirements? $talk->requirements : "None",
    "Description" 	=> $talk->description? $talk->description: "Not available",

    "Date start"	=> $talk->date_start? $talk->date_start : "TBD",
    "Date end"		=> $talk->date_end? $talk->date_start : "TBD",
    "Available places"	=> $talk->places? 
    	($talk->places - $confirmed).'/'.$talk->places : "TBD", 
    "Location"		=> $talk->location? $talk->location: "TBD",
    )
)
}}


</div>
@if ($talk->status == 'approved')
    <div class="col-md-4">
    <h4>Reservations</h4>
    <ul>
	<?php
		$reserved = false;
	?>
	@if (count($reservations) == 0)
		<li class="text-muted">None yet</li>
	@else
	{{ Form::open(array('url' => 'talk_res_del/')) }}
	@foreach ($reservations as $res)
	@if ($res->status == 'refused')
	<li class="text-danger" style="text-decoration:line-through;">
	@elseif ($res->status == 'pending')
	<li class="text-muted" style="font-style:italic">
	@else
	<li>
	@endif
	<a href="{{ URL::to('user/'.$res->user_id) }}">{{ ucwords(strtolower($res->name)) }}</a>
	<?php
		if (!Auth::guest() && (Auth::user()->id == $res->user_id))       
		{
			$reserved = true;
			if ($res->status != 'refused') {	
			?>
			{{ Form::hidden('res_id', $res->id) }}
			<button type="submit" class="btn btn-link btn-xs">
			  <span class="glyphicon glyphicon-remove"></span> Cancel
			</button>
			<?php } 
		}
	?>
	</li>
	@endforeach
	{{ Form::close() }}
	@endif

	</ul>
@if ( ($talk->status == "approved") && !Auth::guest() && !$reserved  && (($talk->places - $confirmed) > 0))
	{{ Form::open(array('url' => 'talk_res_add/'. $talk->id )) }}

	<button type="submit" class='btn btn-sm btn-block'>Add reservation</button>
	 {{ Form::close() }}
@endif
</div>

@endif
      </div>

@stop


@section('javascripts')
@parent
@if ($talk_rights != null)
    <script>
	$('#confirm_talk').submit(function(){
		return confirm("Are you sure you want to confirm this talk?"+
		"\nThis will make the talk open to reservations.");
	});
	$('#cancel_talk').submit(function(){
		return confirm("Are you sure you want to cancel this talk?");
	});
	$('#delete_talk').submit(function(){
		return confirm("Are you sure you want to delete this talk?"+
		"\nThis action cannot be undone.");
	});
    </script>
@endif
@stop


