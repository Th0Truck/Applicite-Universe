<?php

use App\Http\Controllers\TwoFactorAuthenticationSetupController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('users.index');

    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:users.update')
        ->name('users.edit');

    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('users.update');

    Route::put('/users/{user}/roles', [UserManagementController::class, 'updateRoles'])
        ->middleware('permission:roles.manage')
        ->name('users.roles.update');
});

Route::get('/user/two-factor-authentication', TwoFactorAuthenticationSetupController::class)
    ->middleware('auth')
    ->name('two-factor.show');
