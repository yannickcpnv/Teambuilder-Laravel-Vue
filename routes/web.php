<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberTeamController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/home');
Route::get('/home', static fn() => inertia('Home'))->name('home');

Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'create')->name('auth.create');
        Route::post('login', 'store')->name('auth.store');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::middleware('can:moderate')->group(function () {
        Route::post('members/{member}/nominate/{role}',
            [MemberController::class, 'nominate'])
            ->name('members.nominate');
    });

    Route::get('/members/{member}/teams',
        [MemberTeamController::class, 'index'])
        ->name('members.teams.index');

    Route::resource('members', MemberController::class)
        ->only(['index', 'show', 'edit', 'update']);

    Route::resource('members.teams', MemberTeamController::class)
        ->only(['index']);

    Route::resource('teams', TeamController::class)
        ->only('show');

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('auth.destroy');
});
