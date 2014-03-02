@extends('templates.talk')

@section('talk_nav_settings')
<?php $tab_selected='view'; ?>
@stop

@section('talk_main')

    <div class="container">
      <div class="col-md-8"> 
	<h2><small><em>
	{{  ucwords(strtolower(implode(', ', (array)$speaker_names))) }}
	</em></small></h3>
	<p class="lead">{{ $talk->aim }}</p>
{{ Typography::horizontal_dl(
    array(
    "Target" 		=> $talk->target? $talk->target : "Not defined yet",
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
		if (!Auth::guest() && (Auth::user()->id == $res->user_id)
			&& $talk->future() )       
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
@if ( ($talk->status == "approved") && !Auth::guest() && !$reserved  && (($talk->places - $confirmed) > 0) && $talk->future())
	{{ Form::open(array('url' => 'talk_res_add/'. $talk->id )) }}

	<button type="submit" class='btn btn-sm btn-block'>Add reservation</button>
	 {{ Form::close() }}
@endif
</div>
@endif
      </div>
@stop


