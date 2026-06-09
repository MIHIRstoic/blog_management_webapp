<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/filter', [BlogController::class, 'filter'])->name('blogs.filter');
Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/blogs/create', [AdminController::class, 'create'])->name('admin.blogs.create');
    Route::post('/admin/blogs', [AdminController::class, 'store'])->name('admin.blogs.store');
    Route::get('/admin/blogs/{blog}/edit', [AdminController::class, 'edit'])->name('admin.blogs.edit');
    Route::put('/admin/blogs/{blog}', [AdminController::class, 'update'])->name('admin.blogs.update');
    Route::delete('/admin/blogs/{blog}', [AdminController::class, 'destroy'])->name('admin.blogs.destroy');
});
