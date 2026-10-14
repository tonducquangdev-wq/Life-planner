@php
    $offlineHtmlPath = public_path('offline.html');
@endphp
@if(file_exists($offlineHtmlPath))
    {!! file_get_contents($offlineHtmlPath) !!}
@else
    <p>Bạn đang ngoại tuyến. Vui lòng kết nối mạng và thử lại.</p>
@endif
