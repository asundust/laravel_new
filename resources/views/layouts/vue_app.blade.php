@extends('layouts.app_base')
@section('head')
  @parent
  @include('layouts.vue')
@endsection
@section('body')
  <div id="app">
    @yield('app')
  </div>
@endsection
@section('js')
  @parent
@endsection
