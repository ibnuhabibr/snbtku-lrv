<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TryoutController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TryoutPackageController;
use App\Http\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Tryout Routes (Public - dapat diakses tanpa login untuk melihat daftar)
Route::get('/tryouts', [TryoutController::class, 'index'])->name('tryouts.index');

// User Routes (Authenticated)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Tryout Routes for authenticated users
    Route::get('/tryout/{userTryout}/conduct', [TryoutController::class, 'conduct'])->name('tryout.conduct');
    Route::get('/tryout/{userTryout}/result', [TryoutController::class, 'result'])->name('tryout.result');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Subject Management
    Route::resource('subjects', SubjectController::class);
    
    // Topic Management
    Route::resource('topics', TopicController::class);
    
    // Question Management
    Route::resource('questions', QuestionController::class);
    Route::get('questions/topics/{subject}', [QuestionController::class, 'getTopicsBySubject'])->name('questions.topics');
    
    // Tryout Package Management
    Route::resource('tryout-packages', TryoutPackageController::class);
    Route::get('tryout-packages/{package}/questions', [TryoutPackageController::class, 'questions'])->name('tryout-packages.questions');
    Route::post('tryout-packages/{package}/questions', [TryoutPackageController::class, 'addQuestion'])->name('tryout-packages.add-question');
    Route::delete('tryout-packages/{package}/questions/{question}', [TryoutPackageController::class, 'removeQuestion'])->name('tryout-packages.remove-question');
    Route::post('tryout-packages/{package}/questions/reorder', [TryoutPackageController::class, 'updateQuestionOrder'])->name('tryout-packages.reorder-questions');
    
    // Post Management
    Route::resource('posts', PostController::class);
});

require __DIR__.'/auth.php';