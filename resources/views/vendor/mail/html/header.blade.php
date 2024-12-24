@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('image/QuestionMarkLogo.jpg') }}" class="logo" alt="App Logo">
    <!--https://laravel.com/img/notification-logo.png-->
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
