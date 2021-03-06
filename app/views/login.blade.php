@extends('templates.main')

@section('content')
    <div class="row">
    <div class="col-md-4"></div>
    <div class="well col-md-4 pull-center">
    {{ Form::open(array('url' => 'login', 
        'class' => 'form-signin'
        )) }}
        <!-- check for login errors flash var -->
        @if (Session::has('login_errors'))
            {{ Alert::error(trans('errors.loginError')) }}
        @endif
        <!-- username field -->
        <p>{{ Form::label('username', trans('messages.loginUsername')) }}</p>
        <p>{{ Form::text('username') }}</p>
        <!-- password field -->
        <p>{{ Form::label('password', trans('messages.loginPassword')) }}</p>
        <p>{{ Form::password('password') }}</p>
        <!-- submit button -->
        <p>{{ Form::submit(trans('messages.loginLogin'), array('class' => 'btn btn-lg btn-primary btn-block')) }}</p>
    {{ Form::close() }}
    </div>
    </div>
@stop
