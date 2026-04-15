<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClassifyController;

Route::get('/', function () {
    return view('welcome');
});
