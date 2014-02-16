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
            {{ Alert::error("Username or password incorrect.") }}
        @endif
        <!-- username field -->
        <p>{{ Form::label('username', 'Username') }}</p>
        <p>{{ Form::text('username') }}</p>
        <!-- password field -->
        <p>{{ Form::label('password', 'Password') }}</p>
        <p>{{ Form::password('password') }}</p>
        <!-- submit button -->
        <p>{{ Form::submit('Login', array('class' => 'btn btn-lg btn-primary btn-block')) }}</p>
    {{ Form::close() }}
    </div>
    </div>
<script type="text/javascript">
    var queries = {{ json_encode(DB::getQueryLog()) }};
    console.log('/------------------------------ Database Queries ------------------------------/');
    console.log(' ');
    queries.forEach(function(query) {
        console.log('   ' + query.time + ' | ' + query.query + ' | ' + query.bindings[0]);
    });
    console.log(' ');
    console.log('/------------------------------ End Queries -----------------------------------/');
</script>
@stop
