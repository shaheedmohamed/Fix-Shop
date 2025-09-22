<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel + Vue') }}</title>
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app" class="p-4">
            <example-component />
        </div>
    </body>
</html>
