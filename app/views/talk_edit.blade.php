@extends('templates.talk')

@section('talk_nav_settings')
<?php $tab_selected='edit'; ?>
@stop

@section('talk_main')

<div>
@if ($talk->id == null)
<h2>{{ $title }}</h2>
@endif
    {{ Form::open(array('url' => 'talk_save', 'id' => 'talk_edit_form')) }}
    <fieldset>
    <!-- Text input-->
    <div class="form-group">
    	{{ Form::hidden('talk_id', $talk->id) }}
        <!-- title field -->
        <p>{{ Form::label('title', trans('messages.editFormTitle')) }}</p>
        {{ $errors->first('title', Alert::error(":message")) }}
        <p>{{ Form::text('title', Input::old('title', $talk->title)) }}</p>
        <!-- speakers field -->
        <p>{{ Form::label('speakers[]', trans('messages.editFormSpeakers')) }}</p>
        {{ $errors->first('speakers', Alert::error(":message")) }}
        <p>
        {{ Form::select('speakers[]', 
            $name_list, 
            isset($speakers_list) ? $speakers_list: array(), 
            array('multiple' => 'true', 'class' => 'select2'));
        }}
        </p>
        <!-- target field -->
        <p>{{ Form::label('target', trans('messages.editFormTarget')) }}</p>
        {{ $errors->first('target', Alert::error(":message")) }}
        <p>{{ Form::textarea('target', Input::old('target', $talk->target), array('class' =>  'textarea-short')); }}</p>
        <!-- aim field -->
        <p>{{ Form::label('aim', trans('messages.editFormAim')) }}</p>
        {{ $errors->first('aim', Alert::error(":message")) }}
        <p>{{ Form::textarea('aim', Input::old('aim', $talk->aim), array('class' =>  'textarea-short')); }}</p>
        <!-- requirements field -->
        <p>{{ Form::label('reqs', trans('messages.editFormReqs')) }}</p>
        {{ $errors->first('reqs', Alert::error(":message")) }}
        <p>{{ Form::textarea('reqs', Input::old('reqs', $talk->requirements), array('class' =>  'textarea-short')); }}</p>
        <!-- description field -->
        <p>{{ Form::label('desc', trans('messages.editFormDesc')) }}</p>
        {{ $errors->first('desc', Alert::error(":message")) }}
        <p>{{ Form::textarea('desc', Input::old('desc', $talk->description)) }}</p>
	<div>
	<!-- date start field -->
        <div class="col-md-6">
        <p>{{ Form::label('date_start', trans('messages.editFormDateStart')) }}</p>
        {{ $errors->first('date_start', Alert::error(":message")) }}
        <p>{{ Form::text('date_start', Input::old('date_start', ($talk->date_start != 0)? $talk->date_start : ''), 
            array('class' => 'form_datetime', 'size' => '16' )) }}</p>
        <!-- date end field -->
        </div>
        <div class="col-md-6">
        <p>{{ Form::label('date_end', trans('messages.editFormDateEnd')) }}</p>
        {{ $errors->first('date_end', Alert::error(":message")) }}
        <p>{{ Form::text('date_end', Input::old('date_end', ($talk->date_end != 0)? $talk->date_end : '' ), 
            array('class' => 'form_datetime', 'size' => '16' )) }}</p>
        </div>
        <div class="col-md-6">
        <!-- places field -->
        <p>{{ Form::label('places', trans('messages.editFormPlaces')) }}</p>
        {{ $errors->first('places', Alert::error(":message")) }}
        <p>{{ Form::text('places', Input::old('places', $talk->places)) }}</p>
        </div>
        <div class="col-md-6">
        <!-- location field -->
        <p>{{ Form::label('location', trans('messages.editFormLocation')) }}</p>
        {{ $errors->first('location', Alert::error(":message")) }}
@if ($grr_rooms)
<p>
    	{{ Form::hidden('talk_title_old', $talk->title) }}

	<select id="location" name="location" class="col-xs-8 select2">
	<option value="">{{ trans('messages.editFormGrrNone') }}</option>
@foreach ($grr_rooms as $room)
	<option value="{{ $room->room_name }}" {{ ($room->room_name == $talk->location)? 'selected':'' }}>
	{{ $room->room_name }}
	</option>
@endforeach
        </select> 
	<button id="grrCheck" type="button" class="form col-xs-4 btn btn-primary">
	{{ trans('messages.editFormGrrConfirm') }}</button>
	</p>
	<p id="grrConfirm"> 
	</p>

@else
	<p>{{ Form::text('location', Input::old('location', $talk->location)) }}</p>
        <p>{{ Form::block_help(trans('messages.editFormLocationH')) }}</p>
@endif


	</div>
	</div>
	<p>&nbsp;</p>
       <!-- submit button -->
        <p class="pull-right">{{ Form::submit('Save', array('class' => 'btn-success')) }}</p>
        </div>
        </fieldset>
    {{ Form::close() }}
</div>

@stop

@section('styles')
@parent
    {{ HTML::style('css/select2.css') }}
    {{ HTML::style('css/select2-bootstrap.css') }}
@stop

@section('javascripts')
@parent
    {{ HTML::script('js/select2.min.js') }}
    <script>
        $(".select2").select2();
    </script>
<script>

@if ($grr_rooms)

var checkDates = function(date_start, date_end) 
{
	if (!date_start || !date_end) 
		return false;
	dStart = new Date(date_start);
	dEnd = new Date(date_end);
	// if dates are not correct return false
	if (isNaN(dStart) || isNaN(dEnd))
		return false;

	// check if dates are in order
	if (dStart >= dEnd)
		return false;
	return true;
}

grrConfirm = $('#grrConfirm');
$('#grrCheck').click(function() 
{
	grrConfirm.attr('class', 'text-muted');	
	grrConfirm.text("{{ trans('messages.editFormGrrCheck') }}");

	loc = $('#location').val();
	date_start = $('#date_start').val();
	date_end = $('#date_end').val();
	
	if (!loc) {
		grrConfirm.attr('class', 'text-danger');	
		grrConfirm.text("{{ trans('messages.editFormGrrNoLoc') }}");
		return;
	}
	if (!checkDates(date_start, date_end)) {
		grrConfirm.attr('class', 'text-danger');	
		grrConfirm.text("{{ trans('messages.editFormGrrBadDate') }}");
		return;
	}

	talk_grr = "{{ URL::to('talk_grr') }}";
	
	var data;
	data = $.param(date_start);	

	request = $.ajax({
	    url: talk_grr,
	    type: "POST",
	    data: $('#talk_edit_form').serialize() ,
	});
	
	// callback handler that will be called on success
	request.done(function (response, textStatus, jqXHR){
	        //console.log("Response " + response);
		response = $.trim(response);
		if (response == 'true') {
			grrConfirm.attr('class', 'text-success');
			grrConfirm.text("{{ trans('messages.editFormGrrOK') }}");
		} else {
			grrConfirm.attr('class', 'text-danger');
			grrConfirm.text("{{ trans('messages.editFormGrrNA') }}");
		}
	});
	
	// callback handler that will be called on failure
	request.fail(function (jqXHR, textStatus, errorThrown){
		// log the error to the console
		grrConfirm.attr('class', 'text-danger');	
		grrConfirm.text(
	        	"{{ trans('messages.editFormGrrError') }}"+
			textStatus, errorThrown
		);
	});
	
});

@endif

</script>
@stop

