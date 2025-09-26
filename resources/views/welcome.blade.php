<!DOCTYPE html>
<html
    data-app-name="{{ config('app.name') }}"
    data-environment="{{ app()->environment() }}"
    data-version-app="{{ config('app.version') }}"
    <?= app()->isDeveloper ? 'data-version-laravel="' . app()->version() . '"' : '' ?>
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Ads Server</title>

        <!-- === Icon Font used by Vuetify === -->
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

        <?php
            if (app()->isDeveloper) {
                // Skip Vue script if user is not a developer, to prevent database update
        ?>
            @vite(['resources/js/app.js'])
        <?php
            }
        ?>
    </head>
    <body>
        <div id="app">App: <?= config('app.name') ?></div>
    </body>
</html>
