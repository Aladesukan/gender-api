<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClassifyController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/trial', function(){
    return response()->json(['message' => 'Api is working']);
});

Route::get('/classify', [ClassifyController::class, 'classify'])->name('classify');
