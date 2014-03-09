<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
		<p>{{ trans('email.hello') }}</p>
@yield('body')
		<p><em>{{ trans('email.signature') }}</em></p>
		</div>
	</body>
</html>


