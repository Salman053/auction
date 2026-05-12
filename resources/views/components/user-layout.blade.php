@props([
    'title' => null,
    'backUrl' => null,
])

@include('layouts.user', [
    'title' => $title,
    'backUrl' => $backUrl,
    'slot' => $slot,
])


