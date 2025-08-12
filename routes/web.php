<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;

//-----------Pages Routes-----------------
Route::get('/', [MainController::class,"homePage" ] )->name('home');
Route::get('/profile', [MainController::class,"profilePage"])->name('profile');
Route::get('/search', [PropertyController::class, 'index'])->name('search');
Route::get('/list-property', [MainController::class, 'propertyPage'])->name('list-property');

//--------------Authentication Routes-------------------
Route::get('/login', [AuthenticationController::class, 'loginPage'])->name( 'login');
Route::post('/login', [AuthenticationController::class, 'submitLogin'])->name('submit.login');

Route::get('/register', [AuthenticationController::class, 'registerPage'])->name('register');
Route::post('/register', [AuthenticationController::class, 'submitRegister'])->name('submit.register');

Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

//---------------Properties Routes--------------
Route::post('/submit-listing', [PropertyController::class, 'submitListing'])->name('submit-listing');
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');



//-------------------for property setting list--------------------
Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

//----------------Search Routes--------------------------
Route::get('/filter-search', [PropertyController::class, 'filterSearch'])->name('filter-search');

//----------------Admin Routes-------------------
Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
});