<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\FeatureImportController;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('app:updatedata', function() {

    // Call controller for import
    $controller = app()->make('App\Http\Controllers\FeatureImportController');
    app()->call([$controller,'import']);

    $this->comment("");

})->purpose('Update the feature data from the Can I Use datasource');
