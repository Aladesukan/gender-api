<?php

use App\Http\Controllers\Api\ClassifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PhpParser\Builder\Class_;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/trial', function(){
    return response()->json(['message' => 'Api is working']);
});

Route::get('/classify', [ClassifyController::class, 'classify'])->name('classify');
