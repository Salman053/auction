@props([
    'title' => null,
    'backUrl' => null,
])

@include('layouts.admin', [
    'title' => $title,
    'backUrl' => $backUrl,
    'slot' => $slot,
])


