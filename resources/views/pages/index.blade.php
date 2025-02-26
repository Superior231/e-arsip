@extends('layouts.main')

@section('content')
    <h1>Hi, {{ Auth::user()->name }}👋</h1>
@endsection
