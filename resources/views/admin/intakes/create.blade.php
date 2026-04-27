@extends('layouts.admin')

@section('title', __('Create Intake'))

@section('content')
@php
$intake = null;
@endphp
@include('admin.intakes.form')
@endsection