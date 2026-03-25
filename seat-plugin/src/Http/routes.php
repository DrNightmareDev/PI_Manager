<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => 'DrNightmare\\SeatPiManager\\Http\\Controllers',
    'middleware' => ['web', 'auth', 'can:seat-pi-manager.view'],
    'prefix'     => config('seat-pi-manager.route_prefix', 'pi-manager'),
], function () {
    Route::get('/', [
        'as'   => 'seat-pi-manager.index',
        'uses' => 'HomeController@index',
    ]);
});
