<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Welcome;
use App\Livewire\Dashboard;

Route::get('/', Welcome::class)->name('welcome');
Route::get('/dashboard', Dashboard::class)->name('dashboard');
