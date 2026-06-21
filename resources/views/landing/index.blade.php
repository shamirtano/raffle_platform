@extends('layouts.frontend')

@section('title', 'Inicio | El Palomo Negro')

@section('content')
    <!-- Secciones como componentes parciales -->
    @include('landing.partials.hero')
    @include('landing.partials.services')
    @include('landing.partials.reservations')
    @include('landing.partials.raffles', ['activeRaffles' => $activeRaffles])
    @include('landing.partials.reviews')
@endsection