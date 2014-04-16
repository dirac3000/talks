@extends('templates.user')

@section('user_nav_settings')
<?php $tab_selected='edit'; ?>
@stop

@section('user_main')

<div>
<br>
	{{ Form::open(array('url' => 'user_save', 'id' => 'user_edit_form')) }}
	<fieldset>
    	{{ Form::hidden('user_id', $user->id) }}

        <!-- username field -->
        <p>{{ Form::label('username', trans('messages.userUsername')) }}</p>
        {{ $errors->first('username', Alert::error(":message")) }}
        <p>{{ Form::text('username', Input::old('username', $user->username)) }}</p>

        <!-- password field -->
        <p>{{ Form::label('password', trans('messages.userPassword')) }}</p>
        {{ $errors->first('password', Alert::error(":message")) }}
        <p>{{ Form::password('password') }}</p>
	
        <!-- name field -->
        <p>{{ Form::label('name', trans('messages.userName')) }}</p>
        {{ $errors->first('name', Alert::error(":message")) }}
        <p>{{ Form::text('name', Input::old('name', $user->name)) }}</p>

        <!-- email field -->
        <p>{{ Form::label('email', trans('messages.userEmail')) }}</p>
        {{ $errors->first('email', Alert::error(":message")) }}
        <p>{{ Form::text('email', Input::old('email', $user->email)) }}</p>

	<!-- manager field -->
        <p class="row">{{ Form::label('manager', trans('messages.userManager'), array('class' => 'col-md-2 text-right')) }}
        {{ $errors->first('manager', Alert::error(":message")) }}
	<select id="manager" name="manager" class="select2 col-md-10">
	<option value="">{{ trans('messages.userNoManager') }}</option>
@foreach ($managers as $mgr)
	<option value="{{ $mgr->id }}" {{ ($mgr->id == $user->manager_id)? 'selected':'' }}>
	{{ $mgr->name }}
	</option>
@endforeach
        </select> </p>

	<!-- rights field -->
	<p class="row">{{ Form::label('rights', trans('messages.userRights'), array('class' => 'col-md-2 text-right')) }}
        {{ $errors->first('rights', Alert::error(":message")) }}
	<select id="rights" name="rights" class="select2 col-md-10">
	<option value="simple" {{ ($user->rights == 'simple')? 'selected':''}}>{{ trans('messages.userRights_simple') }}</option>
	<option value="admin" {{ ($user->rights == 'admin')? 'selected':''}}>{{ trans('messages.userRights_admin') }}</option>
	</select>
	</p>

       <!-- submit button -->
       <p class="row pull-right">{{ Form::submit('Save', array('class' => 'btn-success')) }}</p>

	</fieldset>
	
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

@stop

