<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\UserController;
use domain\invite\Invite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('auth.login');});

Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
Route::post('/groups/cancel-invite', [GroupController::class, 'cancelInvite'])->name('groups.cancel.invite');

Route::put('/groups/{group}/approve/{user}', [GroupController::class, 'approve'])->name('groups.approve');


Route::post('/invitations/{invitation}/reject', [GroupController::class, 'rejectt'])->name('invitations.rejectt');



Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

Auth::routes();

Route::get('/home', [HomeController::class,  'index'])->name('home');

Route::post('/groups/{group}/invite', [GroupController::class, 'inviteTeammate'])->name('groups.inviteTeammate');

Route::put('/groups/{group}/approve/{user}', [GroupController::class, 'approveRequest'])->name('groups.approveRequest');
Route::put('/groups/{group}/reject/{user}', [GroupController::class, 'rejectRequest'])->name('groups.rejectRequest');

Route::post('/groups/{group}/request-join', [InviteController::class, 'requestJoin'])->name('invite.requestJoin');
Route::post('/invitations/{invitation}/accept', [InviteController::class, 'acceptInvite'])->name('invite.acceptInvite');