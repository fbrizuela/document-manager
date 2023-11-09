<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});
Route::get('/upload-pdf', function () {
    return view('uploadPDF');
})->name('uploadPDF');
Route::post('/agregarQR', [PdfController::class, 'uploadPDF'])->name('agregarQR');

Route::get('/download-pdf', [PdfController::class, 'generatePDF'])->name('pdf.descargar');
