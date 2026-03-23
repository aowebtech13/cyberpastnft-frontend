<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'api is running at ' . config('app.url');
});
