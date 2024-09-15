<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;



Route::get('/', [EntryController::class, 'index']);
Route::post('entries/store', [EntryController::class, 'store']);
Route::get('entries/edit/{id}', [EntryController::class, 'edit']);
Route::post('entries/update/{id}', [EntryController::class, 'update']);
Route::delete('entries/destroy/{id}', [EntryController::class, 'destroy']);
Route::get('entries/data', [EntryController::class, 'getEntries']);
