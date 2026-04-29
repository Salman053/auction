@props([
    'title' => null,
])

@include('layouts.user', [
    'title' => $title,
    'slot' => $slot,
])

