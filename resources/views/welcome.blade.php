<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SmartPath</title>
        <link rel="icon" href="/images/logo-smartpath.png" type="image/png">
        
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        @vite(['resources/js/app.js'])
        
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
