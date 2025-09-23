<?php

// routes/web.php
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RegistrationsController;

Route::redirect('/', '/registration/new');

Route::prefix('registration')->group(function(){
  Route::get('new',   [RegistrationController::class, 'createForm'])->name('registration.form');
  Route::post('new',   [RegistrationController::class, 'createForm'])->name('registration.form');
  // Envia o formulário para PRÉ-VISUALIZAR (não salva ainda)
  Route::post('preview', [RegistrationController::class, 'preview'])->name('registration.preview');
  // Confirma e grava
  Route::post('confirm', [RegistrationController::class, 'confirm'])->name('registration.confirm');
  Route::get('summary', [RegistrationController::class, 'summary'])->name('registration.summary');
  // PIX
  Route::get('pay/{id}', [RegistrationController::class, 'pay'])->name('registration.pay');
  Route::get('pay/{id}/qr.svg', [RegistrationController::class, 'qr'])->name('registration.qr');
});

Route::prefix('admin')->group(function(){
    // -- registrations
    Route::get('registrations', [RegistrationsController::class, 'index'])->name('admin.reg.index');
    Route::post('registrations/{id}/paid', [RegistrationsController::class, 'markPaid'])->name('admin.reg.paid');
    Route::get('registrations/export', [RegistrationsController::class, 'exportCsv'])->name('admin.reg.export');
    Route::delete('registrations/{id}', [RegistrationsController::class, 'destroy'])->name('admin.reg.destroy');
    //-- products
    Route::get('products',                     [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('products/create',              [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('products',                    [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('products/{product}/edit',      [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('products/{product}',           [ProductController::class, 'update'])->name('admin.products.update');
    Route::patch('products/{product}',         [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('products/{product}',        [ProductController::class, 'destroy'])->name('admin.products.destroy');
});
