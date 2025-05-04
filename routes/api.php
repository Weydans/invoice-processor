<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListInvoiceController;
use App\Http\Controllers\ShowInvoiceController;
use App\Http\Controllers\CreateInvoiceController;
use App\Http\Controllers\DeleteInvoiceController;
use App\Http\Controllers\ListInvoiceItemController;
use App\Http\Controllers\ShowInvoiceItemController;
use App\Http\Controllers\CreateInvoiceItemController;
use App\Http\Controllers\DeleteInvoiceItemController;
use App\Http\Controllers\ProcessInvoicePaymentController;

Route::get('/invoices', ListInvoiceController::class);
Route::get('/invoice/{id}', ShowInvoiceController::class);
Route::post('/invoice', CreateInvoiceController::class);
Route::delete('/invoice/{id}', DeleteInvoiceController::class);
Route::post('/invoice/{id}/pay', ProcessInvoicePaymentController::class);

Route::get('/invoice-items', ListInvoiceItemController::class)->name('invoice-items.index');
Route::get('/invoice-item/{id}', ShowInvoiceItemController::class)->name('invoice-items.show');
Route::post('/invoice-item', CreateInvoiceItemController::class)->name('invoice-items.store');
Route::delete('/invoice-item/{id}', DeleteInvoiceItemController::class);
