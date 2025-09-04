<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AICoachController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\AccountSetupController;

// Default Routes
Route::get('/', function () { return view('welcome'); });

// Onboarding Routes
Route::get('/onboarding/hyrox-intake', [OnboardingController::class, 'show'])->name('onboarding.hyrox');
Route::post('/onboarding/hyrox-intake', [OnboardingController::class, 'store'])->name('onboarding.hyrox.store');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// App Related Routes
Route::get('/app', function () { return view('dashboard.index', ['user' => Auth::user()]); })->middleware('auth')->name('app');
Route::post('/account/setup', [AccountSetupController::class, 'store'])->middleware('auth');
Route::post('/chat/personal-trainer', [AICoachController::class, 'chat']);
