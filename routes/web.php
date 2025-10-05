<?php

use App\Http\Controllers\CommodityAcquisitionController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\CommodityLocationController;
use App\Http\Controllers\CommodityServiceController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanPenyusutanController;
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

// Halaman utama = fitur scan barang (untuk guest/public)
Route::get('/', [CommodityController::class, 'publicScanPage'])->name('public.scan');
// API Barang (boleh diakses guest untuk pencarian & scan)
// Route baru (khusus scan berdasarkan item_code)
Route::get('/barang/code/{item_code}', [CommodityController::class, 'apiShowByCode'])->name('api.barang.showByCode');

// Halaman login tetap bisa diakses lewat tombol
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Di dalam group middleware 'auth'
    Route::get('/scan-barang', [CommodityController::class, 'showScanPage'])
        ->name('scan-barang');
    Route::get('/barang/{id}', [CommodityController::class, 'apiShow'])
        ->name('api.barang.show');

    Route::resource('barang', CommodityController::class)->except('create', 'edit', 'show')->parameter('barang', 'commodity');
    Route::prefix('barang')->name('barang.')->group(function () {
       //Route::get('/download-pdf', [CommodityController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/barang/print', [CommodityController::class, 'print'])->name('barang.print');
        Route::post('/print', [CommodityController::class, 'generatePDF'])->name('print');
        Route::get('/qrcode/{id}', [CommodityController::class, 'generateQRCode'])->name('print-qrcode');
        Route::post('/export', [CommodityController::class, 'export'])->name('export');
        Route::post('/import', [CommodityController::class, 'import'])->name('import');
    });

    Route::get('/servis', [CommodityServiceController::class, 'index'])->name('servis.index');
    Route::post('/servis/search', [CommodityServiceController::class, 'search'])->name('servis.search');
    Route::post('/servis/store', [CommodityServiceController::class, 'store'])->name('servis.store');
    Route::get('/servis/history/{id}', [CommodityServiceController::class, 'historyAjax'])
    ->name('servis.history.ajax');
    Route::get('/servis/ongoing', [CommodityServiceController::class, 'ongoing'])->name('servis.ongoing');
    // web.php (tidak perlu perubahan, sudah benar)
Route::patch('/servis/{id}/finish', [CommodityServiceController::class, 'markAsFinished'])
    ->name('servis.finish');

    Route::get('/laporan/penyusutan', [LaporanPenyusutanController::class, 'index'])->name('laporan.penyusutan.index');
Route::get('/laporan/penyusutan/export', [LaporanPenyusutanController::class, 'export'])->name('laporan.penyusutan.export');

    Route::resource('perolehan', CommodityAcquisitionController::class)
        ->except('create', 'edit', 'show')
        ->parameter('perolehan', 'commodity_acquisition');

    Route::resource('jenis-barang', JenisBarangController::class)->except('create', 'edit', 'show')
        ->parameter('jenis-barang', 'jenis-barang');

    Route::prefix('stock-opname')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('stock-opname.index');
        Route::get('/history', [StockOpnameController::class, 'history'])->name('stock-opname.history');
        Route::post('/', [StockOpnameController::class, 'store'])->name('stock-opname.store');
        Route::post('/store-single', [StockOpnameController::class, 'storeSingle'])->name('stock-opname.store-single');
        // Di dalam group middleware 'auth'
        Route::get('/find-by-barcode', [StockOpnameController::class, 'findByBarcode'])->name('stock-opname.find-by-barcode');
    });

    Route::resource('gedung', GedungController::class)->except('create', 'edit', 'show')
        ->parameter('gedung', 'gedung');
    Route::post('/gedung/import', [GedungController::class, 'import'])->name('gedung.import');
    Route::post('/gedung/export', [GedungController::class, 'export'])->name('gedung.export');

    Route::resource('ruangan', CommodityLocationController::class)->except('create', 'edit', 'show')
        ->parameter('ruangan', 'commodity_location');
    Route::post('/ruangan/import', [CommodityLocationController::class, 'import'])->name('ruangan.import');
    Route::post('/ruangan/export', [CommodityLocationController::class, 'export'])->name('ruangan.export');

    Route::resource('pengguna', UserController::class)->except('create', 'edit', 'show')
        ->parameter('pengguna', 'user');

    Route::resource('peran-dan-hak-akses', RoleController::class)->parameter('peran-dan-hak-akses', 'role');


});
