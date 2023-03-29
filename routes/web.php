<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
  return Inertia::render('HomeView');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/alerts', function () {
  return Inertia::render('AlertView');
})->middleware(['auth', 'verified'])->name('alerts');

Route::get('/table', function () {
  return Inertia::render('TablesView');
})->middleware(['auth', 'verified'])->name('tables');

Route::get('/forms', function () {
  return Inertia::render('FormsView');
})->middleware(['auth', 'verified'])->name('form');

Route::get('/error', function () {
  return Inertia::render('ErrorView');
})->middleware(['auth', 'verified'])->name('error');

Route::get('/login', function () {
  return Inertia::render('LoginView');
})->middleware(['auth', 'verified'])->name('login');

Route::get('/userprofile', function () {
  return Inertia::render('ProfileView');
})->middleware(['auth', 'verified'])->name('userprofile');

Route::get('/responsive', function () {
  return Inertia::render('ResponsiveView');
})->middleware(['auth', 'verified'])->name('responsive');

Route::get('/style', function () {
  return Inertia::render('StyleView');
})->middleware(['auth', 'verified'])->name('style');

Route::get('/ui', function () {
  return Inertia::render('UiView');
})->middleware(['auth', 'verified'])->name('ui');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
