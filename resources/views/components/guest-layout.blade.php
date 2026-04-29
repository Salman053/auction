@props([
    'title' => null,
    'search' => '',
])

@include('layouts.guest', [
    'title' => $title,
    'search' => $search,
    'slot' => $slot,
])

