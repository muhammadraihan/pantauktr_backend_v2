@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => env('APP_SITE','https://pantauktr.org')])
<img src="{{asset('img/logo-email.png')}}" style="width: 120px;height:80px">
@endcomponent
@endslot

{{ $slot }}

@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent