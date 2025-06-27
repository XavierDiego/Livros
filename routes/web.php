<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LivrosController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('v1')->group(function () {
    Route::post('auth/token', [AuthController::class, 'token']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('livros', [LivrosController::class, 'list']);
        Route::post('livros', [LivrosController::class, 'add']);
        Route::post('livros/{livroId}/importar-indices-xml', [LivrosController::class, 'addIndiceXML'])
            ->where('livroId', '\d+');
    });
});


require __DIR__.'/auth.php';
