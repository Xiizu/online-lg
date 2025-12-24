<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PlayerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MessageController;
use App\Models\Player;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::post('/login' , [AdminController::class, 'login'])->name('login');
Route::get('/logout' , [AdminController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/games/create', [AdminController::class, 'createGame'])->name('admin.games.create');

Route::get('/admin/games', [AdminController::class, 'indexGame'])->name('admin.games.index');

Route::get('/admin/games/{id}/start', [AdminController::class, 'startGame'])->name('admin.games.start');
Route::post('/admin/games/{id}/end', [AdminController::class, 'endGame'])->name('admin.games.end');
Route::get('/admin/games/{id}', [AdminController::class, 'viewGame'])->name('admin.games.view');
Route::post('/admin/games/{id}/delete', [AdminController::class, 'deleteGame'])->name('admin.games.delete');

Route::post('/admin/players/{id}',[AdminController::class, 'getPlayerInfo'])->name('admin.players.info');
Route::post('/admin/players/{id}/update',[AdminController::class, 'updatePlayer'])->name('admin.players.update');
Route::post('/admin/roles',[AdminController::class, 'getRoles'])->name('admin.roles.list');
Route::post('/admin/etats',[AdminController::class, 'getEtats'])->name('admin.etats.list');
Route::post('/admin/camps',[AdminController::class, 'getCamps'])->name('admin.camps.list');

Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings.index');
Route::post('/admin/settings/etat/{id}/update', [AdminController::class, 'updateEtat'])->name('admin.settings.etat.update');
Route::post('/admin/settings/etat/{id}/delete', [AdminController::class, 'deleteEtat'])->name('admin.settings.etat.delete');
Route::post('/admin/settings/etat/create', [AdminController::class, 'createEtat'])->name('admin.settings.etat.create');
Route::post('/admin/settings/camp/{id}/update', [AdminController::class, 'updateCamp'])->name('admin.settings.camp.update');
Route::post('/admin/settings/camp/{id}/delete', [AdminController::class, 'deleteCamp'])->name('admin.settings.camp.delete');
Route::post('/admin/settings/camp/create', [AdminController::class, 'createCamp'])->name('admin.settings.camp.create');

Route::get('/admin/settings/role', [AdminController::class, 'rolesIndex'])->name('admin.settings.roles.index');

Route::post('/admin/settings/role/create', [AdminController::class, 'createRole'])->name('admin.settings.roles.create');
Route::post('/admin/settings/role/{id}/update', [AdminController::class, 'updateRole'])->name('admin.settings.roles.update');
Route::post('/admin/settings/role/{id}/delete', [AdminController::class, 'deleteRole'])->name('admin.settings.roles.delete');
Route::post('/admin/settings/role/{id}/info', [AdminController::class, 'getRoleInfo'])->name('admin.settings.roles.info');

Route::get('/players/{token}', [PlayerController::class, 'playerAccess'])->name('player.access');
Route::get('/player/dashboard', [PlayerController::class, 'playerDashboard'])->name('player.dashboard');
Route::get('/rules', [PlayerController::class, 'showRules'])->name('player.showRules');

Route::post('/admin/liages/create', [AdminController::class, 'createLiage'])->name('admin.liages.create');

Route::post('/admin/notes/{id}/save', [AdminController::class, 'updateNotes'])->name('admin.games.notes.save');
Route::post('/admin/notes/{id}/get', [AdminController::class, 'getNotes'])->name('admin.games.notes.get');
Route::get('/admin/notes', [AdminController::class, 'notesIndex'])->name('admin.notes.index');


/* Route::post('/broadcasting/auth', [MessageController::class, 'broadcastAuth'])->name('broadcasting.auth'); */

Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send');
Route::post('/messages/history', [MessageController::class, 'getMessageHistory'])->name('messages.history');
