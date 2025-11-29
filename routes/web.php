<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\LandlordMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\NotificationController;

//-----------Pages Routes-----------------
Route::get( '/', [MainController::class,"homePage"])->name(name: 'home');
Route::prefix('profile')->group(function (): void {
    Route::get('/info', [MainController::class, 'profileInfo'])->name('profileInfo');
    Route::get('/properties',  [MainController::class, 'profileProperties'])->name('profileProperties');
    Route::get('/favorites', [MainController::class, 'profileFavorites'])->name('profileFavorites');
    Route::get('/security',  [MainController::class, 'profileSecurity'])->name('profileSecurity');
    Route::put('/info', [MainController::class, 'updateProfile'])->name('profile.update')->middleware('auth');
    Route::put('/photo', [MainController::class, 'updateProfilePhoto'])->name('profile.update.photo')->middleware('auth');
});
Route::get('/profile', [MainController::class,"profilePage"])->name('profile');
Route::get('/search', [PropertyController::class, 'index'])->name('search');
Route::get('/list-property', [MainController::class, 'propertyPage'])->name('list-property')->middleware(['auth', LandlordMiddleware::class]);
//--------------Map Routes-------------------
Route::get('/map', [MapController::class, 'index'])->name('map');
Route::post('/map/geocode', [MapController::class, 'geocode']);
Route::post('/map/reverse-geocode', [MapController::class, 'reverseGeocode'])->name('map.reverse-geocode');
Route::get('/map-test', function() { return view('map-test'); });

//--------------Authentication Routes-------------------
Route::get('/login', [AuthenticationController::class, 'loginPage'])->name( name: 'login');
Route::post('/login', [AuthenticationController::class, 'submitLogin'])->name('submit.login');

Route::get('/register', [AuthenticationController::class, 'registerPage'])->name('register');
Route::post('/register', [AuthenticationController::class, 'submitRegister'])->name('submit.register');

Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

//---------------Properties Routes--------------
Route::post('/submit-listing', [PropertyController::class, 'submitListing'])->name('submit-listing')->middleware(['auth', LandlordMiddleware::class]);
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::post('/properties/{property}/like', [PropertyController::class, 'like'])->name('property.like');

// Review routes
Route::post('/properties/{property}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('/properties/{property}/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update')->middleware('auth');
Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');


//-------------------for property setting list--------------------
Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

//----------------Search Routes--------------------------
Route::get('/filter-search', [PropertyController::class, 'filterSearch'])->name('filter-search');

//----------------Landlord Routes-------------------
Route::middleware(['auth'])->prefix('landlord')->group(function () {
    Route::get('/apply', [LandlordController::class, 'showApplyForm'])->name('landlord.apply');
    Route::post('/apply', [LandlordController::class, 'submitApplication'])->name('landlord.submit-application');
});

Route::middleware(['auth', LandlordMiddleware::class])->prefix('landlord')->group(function () {
    Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('landlord.dashboard');
    Route::get('/properties', [LandlordController::class, 'properties'])->name('landlord.properties');
});

//----------------Admin Routes-------------------
Route::middleware(['web', 'auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/pending-applications', [AdminController::class, 'getPendingApplications'])->name('admin.pending-applications');
    Route::post('/landlord-applications/{application}/approve', [AdminController::class, 'approveLandlordApplication'])->name('admin.landlord.approve');
    Route::post('/landlord-applications/{application}/reject', [AdminController::class, 'rejectLandlordApplication'])->name('admin.landlord.reject');
});

//----------------Notification Routes-------------------
Route::middleware(['auth'])->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});