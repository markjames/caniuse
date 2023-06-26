<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeatureController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::controller(FeatureController::class)->group(function () {
    Route::get('/', 'list');
    Route::get('/features', 'list');
    Route::get('/feature/{slug}', 'show')->where(['slug' => '[-_A-Za-z0-9]+']);
});
