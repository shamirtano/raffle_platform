@extends('layouts.frontend')

@section('title', 'Inicio | El Palomo Negro')

@section('content')
    
    @include('landing.partials.hero', ['hero' => $hero])    
    
    @include('landing.partials.services', ['menu' => $menu, 'events' => $events, 'gallery' => $gallery])
    
    @include('landing.partials.reservations', ['areas' => $areas, 'hours' => $hours])

    @include('landing.partials.gallery', ['gallery' => $gallery])
    
    @include('landing.partials.raffles', ['activeRaffles' => $activeRaffles])
    
    @include('landing.partials.reviews')

    @include('landing.partials.hours', ['hours' => $hours])
@endsection