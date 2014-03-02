@extends('templates.talk')

@section('talk_nav_settings')
<?php $tab_selected='edit'; ?>
@stop

@section('talk_main')

<div>
@if ($talk->id == null)
<h2>{{ $title }}</h2>
@endif
    {{ Form::open(array('url' => 'talk_save')) }}
    <fieldset>
    <!-- Text input-->
    <div class="form-group">
    	{{ Form::hidden('talk_id', $talk->id) }}
        <!-- title field -->
        <p>{{ Form::label('title', 'Title') }}</p>
        {{ $errors->first('title', Alert::error(":message")) }}
        <p>{{ Form::text('title', Input::old('title', $talk->title)) }}</p>
        <!-- speakers field -->
        <p>{{ Form::label('speakers[]', 'Speakers') }}</p>
        {{ $errors->first('speakers', Alert::error(":message")) }}
        <p>
        {{ Form::select('speakers[]', 
            $name_list, 
            isset($speakers_list) ? $speakers_list: array(), 
            array('multiple' => 'true', 'class' => 'talk_speakers'));
        }}
        </p>
        <!-- target field -->
        <p>{{ Form::label('target', 'Target') }}</p>
        {{ $errors->first('target', Alert::error(":message")) }}
        <p>{{ Form::textarea('target', Input::old('target', $talk->target), array('class' =>  'textarea-short')); }}</p>
        <!-- aim field -->
        <p>{{ Form::label('aim', 'Aim') }}</p>
        {{ $errors->first('aim', Alert::error(":message")) }}
        <p>{{ Form::textarea('aim', Input::old('aim', $talk->aim), array('class' =>  'textarea-short')); }}</p>
        <!-- requirements field -->
        <p>{{ Form::label('reqs', 'Requirements') }}</p>
        {{ $errors->first('reqs', Alert::error(":message")) }}
        <p>{{ Form::textarea('reqs', Input::old('reqs', $talk->requirements), array('class' =>  'textarea-short')); }}</p>
        <!-- description field -->
        <p>{{ Form::label('desc', 'Description') }}</p>
        {{ $errors->first('desc', Alert::error(":message")) }}
        <p>{{ Form::textarea('desc', Input::old('desc', $talk->description)) }}</p>
        <!-- date start field -->
        <div class="col-md-6">
        <p>{{ Form::label('date_start', 'Date Start') }}</p>
        {{ $errors->first('date_start', Alert::error(":message")) }}
        <p>{{ Form::text('date_start', Input::old('date_start', $talk->date_start), 
            array('class' => 'form_datetime', 'size' => '16' )) }}</p>
        <!-- date end field -->
        </div>
        <div class="col-md-6">
        <p>{{ Form::label('date_end', ' Date End') }}</p>
        {{ $errors->first('date_end', Alert::error(":message")) }}
        <p>{{ Form::text('date_end', Input::old('date_end', $talk->date_end), 
            array('class' => 'form_datetime', 'size' => '16' )) }}</p>
        </div>
        <div class="col-md-6">
        <!-- places field -->
        <p>{{ Form::label('places', ' Number of available places') }}</p>
        {{ $errors->first('places', Alert::error(":message")) }}
        <p>{{ Form::text('places', Input::old('places', $talk->places)) }}</p>
        </div>
        <div class="col-md-6">
        <!-- location field -->
        <p>{{ Form::label('location', 'Location') }}</p>
        {{ $errors->first('location', Alert::error(":message")) }}
        <p>{{ Form::text('location', Input::old('location', $talk->location)) }}</p>
        <p>{{ Form::block_help('Please confirm with your room reservation system before filling this information.') }}</p>
        </div>
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
        $(".talk_speakers").select2();
    </script>
@stop
