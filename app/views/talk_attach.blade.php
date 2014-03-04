@extends('templates.talk')

@section('talk_nav_settings')
<?php $tab_selected='attach'; ?>
@stop

@section('talk_main')

<div>
<h2></h2>
<p>{{ trans('messages.attachWelcome') }}</p>
{{ Form::open(array(
	'url' => 'talk_attach/'.$talk->id, 
	'enctype' => 'multipart/form-data', 
	'files' => true, 
	'class' => 'form-inline')) 
}}
    <fieldset>
    <!-- Text input-->
    <div class="form-group">
	{{ $errors->first('attachment', Alert::error(":message")) }}
	
<input id="uploadFilename" placeholder="" disabled="disabled" class="form-control"/>
	<div class="fileUpload btn btn-default">
	<span><span class="glyphicon glyphicon-folder-open"></span> {{ trans('messages.attachBrowse') }}</span>
	{{ Form::file('attachment', array('id' => 'attachment', 'class' => 'upload')) }}
	</div>

<button type="submit" class='btn btn-success '><span class="glyphicon glyphicon-cloud-upload"></span> {{ trans('messages.attachUpload') }}</button>
<p>
{{ trans('messages.attachVisibility') }}: 
 <label class="radio radio-inline">
    <input type="radio" name="visibility" id="visibilityPublic" value="public" checked>
    {{ trans('messages.attachPublic') }}
  </label>
  <label class="radio radio-inline">
    <input type="radio" name="visibility" id="visibilityPrivate" value="private">
    {{ trans('messages.attachPrivate') }}
  </label>
<p>

	

        </div>

        </fieldset>
    {{ Form::close() }}
</div>

@stop


@section('styles')
@parent
<style>

.fileUpload {
	position: relative;
	overflow: hidden;
	margin: 10px;
}
.fileUpload input.upload {
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	padding: 0;
	font-size: 20px;
	cursor: pointer;
	opacity: 0;
	filter: alpha(opacity=0);
}

</style>
@stop

@section('javascripts')
@parent
    <script>
	document.getElementById("attachment").onchange = function () {
	    var filePath = this.value;
	    if(filePath.match(/fakepath/)) {
	                        // update the file-path text using case-insensitive regex
	                        filePath = filePath.replace(/C:\\fakepath\\/i, '');
	                    }
	    document.getElementById("uploadFilename").value = filePath;
	};
    </script>
@stop

