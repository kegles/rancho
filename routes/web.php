<?php

// routes/web.php
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\RegistrationsController;

Route::redirect('/', '/registration/new');

Route::prefix('registration')->group(function(){
  Route::get('new',   [RegistrationController::class, 'createForm'])->name('registration.form');
  Route::post('new',   [RegistrationController::class, 'createForm'])->name('registration.form');
  // Envia o formulário para PRÉ-VISUALIZAR (não salva ainda)
  Route::post('preview', [RegistrationController::class, 'preview'])->name('registration.preview');
  // Confirma e grava
  Route::post('confirm', [RegistrationController::class, 'confirm'])->name('registration.confirm');
  Route::get('{id}/summary', [RegistrationController::class, 'summary'])->name('registration.summary');
  Route::get('{id}/badge',   [RegistrationController::class, 'badge'])->name('registration.badge');
});

Route::prefix('admin')->group(function(){
  Route::get('registrations', [RegistrationsController::class, 'index'])->name('admin.reg.index');
  Route::post('registrations/{id}/paid', [RegistrationsController::class, 'markPaid'])->name('admin.reg.paid');
  Route::get('registrations/export', [RegistrationsController::class, 'exportCsv'])->name('admin.reg.export');
  Route::delete('registrations/{id}', [RegistrationsController::class, 'destroy'])->name('admin.reg.destroy');
});
