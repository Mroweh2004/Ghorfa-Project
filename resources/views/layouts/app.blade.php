<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/home.css')}}">
    <title>@yield('title')</title>
</head>
<body>
    @include('partials.nav')
    <main>
    @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.mobile-nav')
</body>
</html>