<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{{ trans('messages.mainTitle') }}</title>
    <meta name="description" content="Talks Sessions Management">
    <meta name="author" content="Alvaro Moran">

    <style>
	@media print body {
		font-family: "Palatino Linotype", "Book Antiqua", Palatino, serif;
		font-size: 1em;
		color: #333333;

        }

	h1 {page-break-before: always;}

	@page {
       @bottom-left {
            content: counter(page) "/" counter(pages);
        }
     }
    th {
	border-width: 1px;
	border-color: black;
	}
    td {
	border-width: 1px;
	border-color: black;
	padding: 1em;
	}
    body {
	font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
	}
    table {
	width: 100%;
	border: 1px solid #000;
}
    </style>

    </head>
  <body>
<div

<div> 
	<h1>{{ $talk->title }}</h1>
	<h3><small><em>{{ trans('messages.editFormSpeakers') }}</em>:
	{{  ucwords(strtolower(implode(', ', (array)$speaker_names))) }}
	</small></h3>
	<p><strong>{{ trans('messages.editFormDateStart') }}</strong> {{ $talk->date_start }}</p>
	<p><strong>{{ trans('messages.editFormDateEnd') }}</strong> {{ $talk->date_start }}</p>
    
  <h3>{{ trans('messages.attAttendance') }}</h3>
    
    <table border="1">
    <tr>
	<col width="50%">
  	<col width="50%">
	<th>{{ trans('messages.attNames') }}</th><th>{{ trans('messages.attSignatures') }}</th></tr>
	@foreach ($reservations as $res)
	<tr>
    <td>{{ ucwords(strtolower($res->name)) }}</td>
	<td>&nbsp;</td>
	@endforeach
	</tr>
    <!-- Few more rows just in case -->
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>
</div>
</body>
</html>

