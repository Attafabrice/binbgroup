@php
    $value = max(0, min(100, (int) $getState()));
@endphp
<div role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100"
     style="width:100%;height:.875rem;background:#e5e7eb;border-radius:9999px;overflow:hidden">
    <div style="width:{{ $value }}%;height:100%;background:#0b5a9e;border-radius:9999px"></div>
</div>
<p style="margin-top:.5rem;font-size:.875rem">{{ $value }} % réalisé</p>