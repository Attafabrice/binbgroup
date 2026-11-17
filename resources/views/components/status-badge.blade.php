@props(['status'])
@php
    $color = $status->getColor();
    $map = ['gray' => 'badge-gray', 'primary' => 'badge-primary', 'success' => 'badge-success',
            'danger' => 'badge-danger', 'warning' => 'badge-warning', 'info' => 'badge-primary'];
@endphp
<span class="badge {{ $map[$color] ?? 'badge-gray' }}">{{ $status->getLabel() }}</span>