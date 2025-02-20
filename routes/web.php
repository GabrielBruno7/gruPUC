<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');  // Rota correta para criar
Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');  // Rota correta para visualizar um grupo
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships.index');
Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');

Auth::routes();

Route::get('/home', [HomeController::class,  'index'])->name('home');
