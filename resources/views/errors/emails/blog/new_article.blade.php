@component('mail::message')
# {{ $title }}

{{ __('l.We have published a new article that we think will interest you') }}

{{ Str::limit(strip_tags($blog->getTranslation('content', $defaultLanguage)), 200) }}

@component('mail::button', ['url' => $url])
{{ __('l.Read The Article') }}
@endcomponent

{{ __('l.Thanks for subscribing to our newsletter') }},<br>
{{ $siteTitle }}

@component('mail::subcopy')
{{ __('l.If you no longer wish to receive these emails') }}, [{{ __('l.Unsubscribe') }}]({{ $unsubscribeUrl }})
@endcomponent
@endcomponent