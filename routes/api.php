<?php

use App\Http\Controllers\LecturerController;
use App\Http\Controllers\FacultyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(LecturerController::class)->group(function () {
    Route::get('/lecturers', 'index')->name('lecturer.index');
    Route::get('/lecturers/{lecturer}', 'show')->name('lecturer.show');
});

Route::prefix('faculties')->group(function () {
    Route::controller(FacultyController::class)->group(function () {
        Route::get('/', 'index')->name('faculties.index');
        Route::get('/{faculty}', 'show')->name('faculties.show');
    });
});
