<?php

use App\Livewire\Dashboard\Overview;
use App\Livewire\Marketplace\Listings\Category;
use App\Livewire\Marketplace\Community\CommunityHome;
use App\Livewire\Marketplace\Community\CommunityRequest;
use App\Livewire\Marketplace\Listings\ItemDetails;
use App\Livewire\Marketplace\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('welcome');
Route::get('/dashboard', Overview::class)->name('dashboard');
Route::get('/category', Category::class)->name('category');
Route::get('/listing-details', ItemDetails::class)->name('listing-details');
Route::get('community', CommunityHome::class)->name('community');
Route::get('community/request/{id}', CommunityRequest::class)->name('community.request');