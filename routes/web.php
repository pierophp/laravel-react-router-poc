<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ["message" => "Hello World From Laravel"];
});
