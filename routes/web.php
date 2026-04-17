<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard');
Route::view('/login', 'login');
Route::view('/register', 'register');
Route::view('/profile', 'profile');
Route::view('/profile/edit', 'profile-edit');
Route::view('/employer/jobs/new', 'employer-create-job');
Route::view('/employer/jobs', 'employer-jobs');
Route::get('/jobs/{jobPost}', function () {
	return view('job-detail');
});
Route::get('/jobs/{jobPost}/apply', function () {
	return view('worker-apply');
});
Route::view('/worker/applications', 'worker-applications');
Route::view('/employer/applications', 'employer-applications');
Route::get('/employer/jobs/{jobPost}/edit', function () {
    return view('employer-edit-job');
});
