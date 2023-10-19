# Ads Server using Laravel 10.28.0,

## Setup

```sh
# === Check php version
php -v
# PHP 8.2.6 (10/13/23)
# Note: If php version < 8, the composer command below might create Laravel 8 instead of 10

# === Create Laravel 10 project in folder laravel-ads
composer create-project laravel/laravel laravel-ads
# Note: Vite 4 is preinstalled in package.json

# === Install frontend scaffolding, starting point for integrating Vue.js
composer require laravel/ui

# === Generate the basic scaffolding for Vue.js
php artisan ui vue

# === Install or upgrade libraries
# Vue is the frontend framework, Axios here for upgrade purpose, already included in Laravel
pnpm add -D vue vue-router axios
# Version Check: pnpm vite -v

# Vuetify is the styling library
pnpm add vuetify
pnpm add -D vite-plugin-vuetify

# For parsing HTML string
composer require seyyedam7/laravel-html-parser

# === Start server
php artisan serve
pnpm dev
```

## Integrate Vue and Laravel
```html
<!-- resources/views/welcome.blade.php -->
<html>
    <head>
    ...
      @vite(['resources/js/app.js'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
```

* Create `resources/js/App.vue`, configure `resources/js/app.js` to use this as the root Vue component

```js
// resources/js/app.js
import App from '@/App.vue';
const app = createApp(App);
```

## Useful Laravel Artisan Commands

```bash
php artisan make:model AdType
php artisan make:controller AdController --model=Ad

php artisan migrate:fresh --seed

# Useful when config files are changed
php artisan config:clear
```

## References

* Laravel 10 Application with Vue 3 https://medium.com/@laraveltuts/laravel-10-application-with-vue-3-complete-guide-to-crud-operations-3705f9a7cb22

* Vuetify components: https://vuetifyjs.com/en/components/all/

* Material Design Icons: https://pictogrammers.com/library/mdi/