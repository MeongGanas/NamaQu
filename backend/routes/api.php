<?php

use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::controller(PublicController::class)->group(function () {
    Route::get('/site', 'site');
    Route::get('/recruitments', 'recruitments');
    Route::get('/recruitments/{slug}', 'recruitment');
    Route::get('/modules', 'modules');
    Route::get('/modules/{slug}', 'module');
    Route::get('/activities', 'activities');
    Route::get('/activities/{slug}', 'activity');
    Route::get('/team', 'team');
    Route::get('/testimonials', 'testimonials');
});

Route::post('/recruitments/{slug}/register', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1');
