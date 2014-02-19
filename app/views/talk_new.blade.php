@extends('templates.main')
	
@section('content')

<div>
    <h2>Creating New Talk</h2>
    {{ Form::open(array('url' => 'talk_new')) }}
    <fieldset>
    <!-- Text input-->
    <div class="form-group">
        <!-- title field -->
        <p>{{ Form::label('talk_title', 'Title') }}</p>
        {{ $errors->first('talk_title', Alert::error(":message")) }}
        <p>{{ Form::text('talk_title', Input::old('talk_title')) }}</p>
        <!-- speakers field -->
        <p>{{ Form::label('talk_speakers[]', 'Speakers') }}</p>
        {{ $errors->first('talk_speakers', Alert::error(":message")) }}
        <p>
        {{ Form::select('talk_speakers[]', 
            $name_list, 
            array(), 
            array('multiple' => 'true', 'class' => 'talk_speakers'));
        }}
        </p>
        <!-- target field -->
        <p>{{ Form::label('talk_target', 'Target') }}</p>
        {{ $errors->first('talk_target', Alert::error(":message")) }}
        <p>{{ Form::textarea('talk_target', Input::old('talk_target'), array('class' =>  'textarea-short')); }}</p>
        <!-- aim field -->
        <p>{{ Form::label('talk_aim', 'Aim') }}</p>
        {{ $errors->first('talk_aim', Alert::error(":message")) }}
        <p>{{ Form::textarea('talk_aim', Input::old('talk_aim'), array('class' =>  'textarea-short')); }}</p>
        <!-- requirements field -->
        <p>{{ Form::label('talk_reqs', 'Requirements') }}</p>
        {{ $errors->first('talk_reqs', Alert::error(":message")) }}
        <p>{{ Form::textarea('talk_reqs', Input::old('talk_reqs'), array('class' =>  'textarea-short')); }}</p>
        <!-- description field -->
        <p>{{ Form::label('talk_desc', 'Description') }}</p>
        {{ $errors->first('talk_desc', Alert::error(":message")) }}
        <p>{{ Form::textarea('talk_desc', Input::old('talk_desc')) }}</p>
        <!-- date start field -->
        <div class="col-md-6">
        <p>{{ Form::label('talk_date_start', 'Date Start') }}</p>
        {{ $errors->first('talk_date_start', Alert::error(":message")) }}
        <p>{{ Form::text('talk_date_start', Input::old('talk_date_start'), 
            array('class' => 'form_datetime', 'size' => '16' )) }}</p>
        <!-- date end field -->
        </div>
        <div class="col-md-6">
        <p>{{ Form::label('talk_date_end', ' Date End') }}</p>
        {{ $errors->first('talk_date_end', Alert::error(":message")) }}
        <p>{{ Form::text('talk_date_end', Input::old('talk_date_end'), 
            array('class' => 'form_datetime', 'size' => '16' )) }}</p>
        </div>
        <div class="col-md-6">
        <!-- places field -->
        <p>{{ Form::label('talk_places', ' Number of available places') }}</p>
        {{ $errors->first('talk_places', Alert::error(":message")) }}
        <p>{{ Form::text('talk_places', Input::old('talk_places')) }}</p>
        </div>
        <div class="col-md-6">
        <!-- location field -->
        <p>{{ Form::label('talk_location', 'Location') }}</p>
        {{ $errors->first('talk_location', Alert::error(":message")) }}
        <p>{{ Form::text('talk_location', Input::old('talk_location')) }}</p>
        <p>{{ Form::block_help('Confirm with your room reservation system before filling this information.') }}</p>
        </div>
       <!-- submit button -->
        <p class="pull-right">{{ Form::submit('Create', array('class' => 'btn-success')) }}</p>
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

