<?php

use App\Http\Controllers\LecturerController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SpecializationController;
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
    Route::get('/lecturers', 'index')->name('lecturers.index');
    Route::get('/lecturers/{lecturer}', 'show')->name('lecturers.show');
});

Route::prefix('faculties')->group(function () {
    Route::controller(FacultyController::class)->group(function () {
        Route::get('/', 'index')->name('faculties.index');
        Route::get('/{faculty}', 'show')->name('faculties.show');
    });

    Route::controller(MajorController::class)->group(function () {
        Route::get('/{faculty}/majors', 'index')->name('majors.index');
        Route::get('/{faculty}/majors/{major}', 'show')->scopeBindings()->name('majors.show');
    });
});

Route::controller(SpecializationController::class)->group(function () {
    Route::get('/majors/{major}/specializations', 'index')->name('specializations.index');
    Route::get('/majors/{major}/specializations/{specialization}', 'show')->scopeBindings()->name('specializations.show');
});
