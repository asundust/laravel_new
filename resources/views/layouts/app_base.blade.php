<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>@yield('title') - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @section('head')
  @show
</head>
<body>
@yield('body')
</body>
@section('js')
@show
</html>
