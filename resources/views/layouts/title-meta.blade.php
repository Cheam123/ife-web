<meta charset="utf-8" />
<title>@yield('title') | {{ Config::get('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Ronda: your simple, reliable partner for every F&amp;B field team round." name="description" />
<meta content="" name="author" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- App icons (Ronda brand) -->
<link rel="icon" href="{{ URL::asset('assets/brand/favicon.ico') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ URL::asset('assets/brand/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ URL::asset('site.webmanifest') }}">
<meta name="theme-color" content="#0D729E">