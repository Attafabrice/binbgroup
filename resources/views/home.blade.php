@extends('layouts.app')

@push('preload')
    <link rel="preload" as="image" href="{{ asset('assets/images/hero.webp') }}" media="(min-width: 769px)">
    <link rel="preload" as="image" href="{{ asset('assets/images/hero-mobile.webp') }}" media="(max-width: 768px)">
@endpush

@section('content')
    @include('sections.hero')
    @include('sections.about')
    @include('sections.services')
    @include('sections.values')
    @include('sections.projects')
    @include('sections.earth')
    @include('sections.parteners')
    @include('sections.team')
    @include('sections.contact')
@endsection
