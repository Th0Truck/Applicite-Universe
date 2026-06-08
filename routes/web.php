<?php

use App\Http\Controllers\CmsPageController;
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

    Route::get('/pages', [CmsPageController::class, 'index'])
        ->middleware('permission:pages.view')
        ->name('cms.pages.index');

    Route::get('/pages/create', [CmsPageController::class, 'create'])
        ->middleware('permission:pages.create')
        ->name('cms.pages.create');

    Route::post('/pages', [CmsPageController::class, 'store'])
        ->middleware('permission:pages.create')
        ->name('cms.pages.store');

    Route::get('/pages/{page}/edit', [CmsPageController::class, 'edit'])
        ->middleware('permission:pages.update')
        ->name('cms.pages.edit');

    Route::put('/pages/{page}', [CmsPageController::class, 'update'])
        ->middleware('permission:pages.update')
        ->name('cms.pages.update');

    Route::delete('/pages/{page}', [CmsPageController::class, 'destroy'])
        ->middleware('permission:pages.delete')
        ->name('cms.pages.destroy');
});

Route::get('/user/two-factor-authentication', TwoFactorAuthenticationSetupController::class)
    ->middleware('auth')
    ->name('two-factor.show');

Route::get('/pages/{slug}', [CmsPageController::class, 'show'])
    ->name('cms.pages.show');
