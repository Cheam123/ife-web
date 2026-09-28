<meta charset="utf-8" />
<title>@yield('title') | {{ Config::get('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="" name="description" />
<meta content="" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- App favicon -->
<link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico')}}">