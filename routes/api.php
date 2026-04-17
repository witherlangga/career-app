<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    });

    Route::get('jobs', [JobPostController::class, 'index']);
    Route::get('jobs/{jobPost}', [JobPostController::class, 'show']);
});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update'])->middleware('abilities:profile:write');
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->middleware('abilities:profile:write');
    Route::post('profile/cv', [ProfileController::class, 'uploadCv'])->middleware('abilities:profile:write');

    Route::post('jobs/{jobPost}/apply', [ApplicationController::class, 'store'])
        ->middleware(['role:worker', 'abilities:applications:write']);
    Route::get('worker/applications', [ApplicationController::class, 'myApplications'])
        ->middleware('role:worker');

    Route::prefix('employer')->middleware('role:employer')->group(function () {
        Route::get('jobs', [JobPostController::class, 'myJobs']);
        Route::post('jobs', [JobPostController::class, 'store'])->middleware('abilities:jobs:write');
        Route::put('jobs/{jobPost}', [JobPostController::class, 'update'])->middleware('abilities:jobs:write');
        Route::delete('jobs/{jobPost}', [JobPostController::class, 'destroy'])->middleware('abilities:jobs:write');
        Route::get('jobs/{jobPost}/applications', [ApplicationController::class, 'indexForEmployer']);
        Route::patch('applications/{application}', [ApplicationController::class, 'updateStatus']);
    });

});
