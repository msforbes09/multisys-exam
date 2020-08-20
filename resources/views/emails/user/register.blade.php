@component('mail::message')
# Hello

You are successfully registered in **Dishtansya** food delivery app.

@component('mail::button', ['url' => config('app.ui_url'), 'color' => 'green'])
Go To App
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
