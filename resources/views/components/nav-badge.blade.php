@props(['key'])

@if(isset($stats) && array_key_exists($key, $stats) && $stats[$key] > 0)
    <span class="nav-badge">{{ $stats[$key] }}</span>
@endif
