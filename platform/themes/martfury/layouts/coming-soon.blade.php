<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts-->
    <link href="https://fonts.googleapis.com/css?family={{ urlencode(theme_option('primary_font', 'Work Sans')) }}:300,400,500,600,700&amp;amp;subset=latin-ext" rel="stylesheet" type="text/css">
    <!-- CSS Library-->

    <style>
        :root {
            --color-1st: {{ theme_option('primary_color', '#fcb800') }};
            --color-2nd: {{ theme_option('secondary_color', '#222222') }};
            --primary-font: '{{ theme_option('primary_font', 'Work Sans') }}', sans-serif;
        }
    </style>

    {!! Theme::header() !!}
</head>
<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif @if (Theme::get('pageId')) id="{{ Theme::get('pageId') }}" @endif>
<div class="ps-page--comming-soon">
    <div class="container">
        <div class="ps-page__header">
            <h1>{{ SeoHelper::getTitle() }}</h1>
        </div>
        <div>{!! Theme::content() !!}</div>
        <div class="ps-page__footer">
            <ul class="ps-list--social">
                @for($i = 1; $i <= 5; $i++)
                    @if(theme_option('social-name-' . $i) && theme_option('social-url-' . $i) && theme_option('social-icon-' . $i))
                        <li>
                            <a href="{{ theme_option('social-url-' . $i) }}"
                               title="{{ theme_option('social-name-' . $i) }}" style="color: {{ theme_option('social-color-' . $i) }}">
                                <i class="fa {{ theme_option('social-icon-' . $i) }}"></i>
                            </a>
                        </li>
                    @endif
                @endfor
            </ul>
        </div>
    </div>
</div>

{!! Theme::footer() !!}
</body>
</html>

