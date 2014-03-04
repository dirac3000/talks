@extends('templates.talk')

@section('talk_nav_settings')
<?php $tab_selected='view'; ?>
@stop

@section('talk_main')

<div class="container">
@if ($talk->status == 'approved' || $talk_rights != null)
    <div class="col-md-4 pull-right">
    <h4>{{ trans('messages.viewReservations') }}</h4>
    <ul>
	<?php
		$reserved = false;
	?>
	@if (count($reservations) == 0)
		<li class="text-muted">{{ trans('messages.viewResNone') }}</li>
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
			  <span class="glyphicon glyphicon-remove"></span> {{ trans('messages.viewResCancel') }}
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

	<button type="submit" class='btn btn-sm btn-block'>{{ trans('messages.viewResAdd') }}</button>
	 {{ Form::close() }}
@endif
</div>
@endif


<div class="col-md-8"> 
	<h2><small><em>
	{{  ucwords(strtolower(implode(', ', (array)$speaker_names))) }}
	</em></small></h3>
	<p class="lead">{{ $talk->aim }}</p>
{{ Typography::horizontal_dl(
    array(
    trans('messages.editFormTarget')	=> $talk->target? $talk->target : trans('messages.viewTBD'),
    trans('messages.editFormReqs')	=> $talk->requirements? $talk->requirements : trans('messages.viewTBD'),
    trans('messages.editFormDesc') 	=> $talk->description? $talk->description: trans('messages.viewNA'),

    trans('messages.editFormDateStart')	=> $talk->date_start? $talk->date_start : trans('messages.viewTBD'),
    trans('messages.editFormDateEnd')		=> $talk->date_end? $talk->date_start : trans('messages.viewTBD'),
    trans('messages.editFormPlaces')	=> $talk->places? 
    	($talk->places - $confirmed).'/'.$talk->places : trans('messages.viewTBD'), 
    trans('messages.editFormLocation')	=> $talk->location? $talk->location: trans('messages.viewTBD'),
    )
)
}}
<?php
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
?>
@if (!$attachments->isEmpty())
<h4>{{ trans('messages.viewAttachments') }}</h4>
<table class="table table-nonfluid">
@foreach ($attachments as $att)
   <tr><td>
   <a href="{{ URL::to($att->path) }}">
   <span class="glyphicon glyphicon-file"></span> 
   <strong>{{ basename($att->path) }}</strong></a>
   <small>{{ human_filesize(filesize($att->path)) }}</small>
   </td>
@if ($talk_rights != null)
   <td>
   {{ Form::open(array(
	   'url' => 'talk_attach/'.$att->id.'/privacy',
	   'class' => 'form-inline',
   )) }}
	{{ Form::hidden('privacy', 
		($att->privacy == 'public')? 'private':'public') }}
	<button type="submit" class="btn btn-link btn-xs">
	@if ($att->privacy == 'public') 
	<span class="glyphicon glyphicon-eye-close"></span> {{ trans('messages.viewAttMakePrivate') }}
	@else
	<span class="glyphicon glyphicon-eye-open"></span> {{ trans('messages.viewAttMakePublic') }}
	@endif
	</button>
   {{ Form::close() }}
   </td>
   <td>
   {{ Form::open(array('url' => 'talk_attach/'.$att->id, 'method' => 'delete')) }}
	{{ Form::hidden('talk_id', $talk->id) }}
	<button type="submit" class="btn btn-link btn-xs">
	<span class="glyphicon glyphicon-remove"></span> {{ trans('messages.viewAttRemove') }}
	</button>
   {{ Form::close() }}
   </td>
@endif
@endforeach
@endif
</div>


</div>
@stop


