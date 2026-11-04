<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CondutorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\OrgaoAplicadorController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VeiculoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/condutores', [CondutorController::class, 'index'])->name('condutores.index');
    Route::get('/condutores/{condutor}', [CondutorController::class, 'show'])->whereNumber('condutor')->name('condutores.show');

    Route::get('/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');
    Route::get('/veiculos/{veiculo}', [VeiculoController::class, 'show'])->whereNumber('veiculo')->name('veiculos.show');

    Route::get('/multas', [MultaController::class, 'index'])->name('multas.index');
    Route::get('/multas/inadimplentes', [MultaController::class, 'inadimplentes'])->name('multas.inadimplentes');
    Route::get('/multas/{multa}', [MultaController::class, 'show'])->whereNumber('multa')->name('multas.show');

    Route::get('/orgaos', [OrgaoAplicadorController::class, 'index'])->name('orgaos.index');

    Route::prefix('relatorios')->name('relatorios.')->controller(RelatorioController::class)->group(function () {
        Route::get('/ranking-infracoes', 'rankingInfracoes')->name('ranking-infracoes');
        Route::get('/ranking-infracoes/pdf', 'rankingInfracoesPdf')->name('ranking-infracoes.pdf');
        Route::get('/veiculos-mais-multados', 'veiculosMaisMultados')->name('veiculos-mais-multados');
        Route::get('/veiculos-mais-multados/pdf', 'veiculosMaisMultadosPdf')->name('veiculos-mais-multados.pdf');
        Route::get('/cnh-proximas-vencer', 'cnhProximasVencer')->name('cnh-proximas-vencer');
        Route::get('/cnh-proximas-vencer/pdf', 'cnhProximasVencerPdf')->name('cnh-proximas-vencer.pdf');
    });
});
