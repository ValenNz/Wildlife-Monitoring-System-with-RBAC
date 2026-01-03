<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GeozoneController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SmartIntegrationController;
use App\Http\Controllers\HistoricalTrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes - Wildlife Monitoring System (MODULAR & RBAC-COMPLIANT)
|--------------------------------------------------------------------------
*/

// Autentikasi
require __DIR__.'/auth.php';

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD (Semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_dashboard'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/animals/export', [DashboardController::class, 'exportAnimals'])->name('animals.export');
    Route::get('/animals/{id}', [DashboardController::class, 'animalDetail'])->name('animals.detail');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
});

/*
|--------------------------------------------------------------------------
| ANIMALS
|--------------------------------------------------------------------------
*/
// routes/web.php

// routes/web.php

// routes/web.php
// 👁️ Semua role dengan view_animal_details
Route::middleware(['auth', 'permission:view_animal_details'])->group(function () {
    Route::get('animals', [AnimalController::class, 'index'])->name('animals.index');
    Route::get('animals/{id}', [AnimalController::class, 'show'])->name('animals.show');
});

// 🔧 Hanya admin (manage_animals) → AJAX & Modal
Route::middleware(['auth', 'permission:manage_animals'])->group(function () {
    Route::get('animals/create-modal', [AnimalController::class, 'createModal'])->name('animals.create.modal');
    Route::get('animals/{id}/edit-modal', [AnimalController::class, 'editModal'])->name('animals.edit.modal');

    Route::post('animals', [AnimalController::class, 'storeAjax'])->name('animals.store.ajax');
    Route::put('animals/{id}', [AnimalController::class, 'updateAjax'])->name('animals.update.ajax');
    Route::delete('animals/{id}', [AnimalController::class, 'destroyAjax'])->name('animals.destroy.ajax');
});

// Assign device: Admin + Konservasionis
Route::middleware(['auth', 'permission:assign_device_to_animal'])->post('animals/{id}/assign-device', [AnimalController::class, 'assignDevice'])->name('animals.assign-device');

/*
|--------------------------------------------------------------------------
| MAP & HISTORICAL TRACKING (Semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_map'])->prefix('map')->name('map.')->group(function () {
    Route::get('/', [MapController::class, 'index'])->name('index');
    Route::get('/track/{id}', [MapController::class, 'trackAnimal'])->name('track-animal');
    Route::get('/api/positions', [MapController::class, 'getPositions'])->name('api.positions');
    Route::get('/api/heatmap', [MapController::class, 'getHeatmap'])->name('api.heatmap');
    Route::get('/api/geozones', [MapController::class, 'getGeozones'])->name('api.geozones');
    Route::get('/api/geozones/{id}/animals', [MapController::class, 'getAnimalsInZone'])->name('api.geozones.animals');
});

Route::middleware(['auth', 'permission:view_historical_tracking'])->prefix('historical-tracking')->name('historical-tracking.')->group(function () {
    Route::get('/', [HistoricalTrackingController::class, 'index'])->name('index');
    Route::get('/{id}', [HistoricalTrackingController::class, 'show'])->name('show');
    Route::get('/animal/{animalId}', [HistoricalTrackingController::class, 'byAnimal'])->name('by-animal');
    Route::get('/device/{deviceId}', [HistoricalTrackingController::class, 'byDevice'])->name('by-device');
    Route::get('/playback/{animalId}', [HistoricalTrackingController::class, 'playback'])->name('playback');
    Route::get('/export/{id}', [HistoricalTrackingController::class, 'export'])->name('export');
});

/*
|--------------------------------------------------------------------------
| NOTIFICATIONS (Semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_notifications'])->group(function () {
    Route::resource('notifications', NotificationController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('notifications/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('notifications.bulk-delete');
    Route::get('notifications/api/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.api.unread-count');
    Route::get('notifications/api/unread', [NotificationController::class, 'getUnread'])->name('notifications.api.unread');
});

/*
|--------------------------------------------------------------------------
| REPORTS
|--------------------------------------------------------------------------
*/
// Lihat laporan: semua role
Route::middleware(['auth', 'permission:view_reports'])->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{id}/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::get('reports/{id}/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');
});

// Buat/Generate laporan: hanya Peneliti & Pengambil Kebijakan
Route::middleware(['auth', 'permission:generate_reports'])->group(function () {
    Route::get('reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
});

/*
|--------------------------------------------------------------------------
| WEATHER (Semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_weather'])->prefix('weather')->name('weather.')->group(function () {
    Route::get('/', [WeatherController::class, 'index'])->name('index');
    Route::get('/current', [WeatherController::class, 'current'])->name('current');
    Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');
    Route::get('/history', [WeatherController::class, 'history'])->name('history');
    Route::get('/api/current', [WeatherController::class, 'apiCurrent'])->name('api.current');
    Route::get('/api/forecast', [WeatherController::class, 'apiForecast'])->name('api.forecast');
});

/*
|--------------------------------------------------------------------------
| INCIDENTS (Admin + Konservasionis Lapangan)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_incidents'])->group(function () {
    Route::resource('incidents', IncidentController::class);
    Route::post('incidents/{id}/resolve', [IncidentController::class, 'resolve'])->name('incidents.resolve');
    Route::post('incidents/{id}/escalate', [IncidentController::class, 'escalate'])->name('incidents.escalate');
    Route::post('incidents/{id}/assign', [IncidentController::class, 'assign'])->name('incidents.assign');
    Route::get('incidents/export', [IncidentController::class, 'export'])->name('incidents.export');
});

/*
|--------------------------------------------------------------------------
| DEVICE MANAGEMENT (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_devices'])->group(function () {
    Route::resource('devices', DeviceController::class);
    Route::post('devices/{id}/test', [DeviceController::class, 'test'])->name('devices.test');
    Route::post('devices/{id}/reset', [DeviceController::class, 'reset'])->name('devices.reset');
    Route::get('devices/export', [DeviceController::class, 'export'])->name('devices.export');
});

/*
|--------------------------------------------------------------------------
| GEOZONE MANAGEMENT (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_geozones'])->group(function () {
    Route::resource('geozones', GeozoneController::class);
    Route::get('geozones/export', [GeozoneController::class, 'export'])->name('geozones.export');
    Route::get('geozones/{id}/animals', [GeozoneController::class, 'getAnimalsInZone'])->name('geozones.animals');
    Route::post('geozones/{id}/toggle', [GeozoneController::class, 'toggle'])->name('geozones.toggle');
});

/*
|--------------------------------------------------------------------------
| ACTIVITY LOGS (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_activity_logs'])->prefix('activity-logs')->name('activity-logs.')->group(function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('index');
    Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
    Route::get('/user/{userId}', [ActivityLogController::class, 'byUser'])->name('by-user');
    Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
    Route::delete('/clear', [ActivityLogController::class, 'clear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_users'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('users/{id}/activity', [UserController::class, 'activity'])->name('users.activity');
});

/*
|--------------------------------------------------------------------------
| SMART INTEGRATION (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_smart'])->group(function () {
    Route::resource('smart-integrations', SmartIntegrationController::class);
    Route::post('smart-integrations/{id}/test', [SmartIntegrationController::class, 'test'])->name('smart-integrations.test');
    Route::post('smart-integrations/{id}/sync', [SmartIntegrationController::class, 'sync'])->name('smart-integrations.sync');
    Route::post('smart-integrations/{id}/toggle', [SmartIntegrationController::class, 'toggle'])->name('smart-integrations.toggle');
    Route::get('smart-integrations/{id}/logs', [SmartIntegrationController::class, 'logs'])->name('smart-integrations.logs');
});

/*
|--------------------------------------------------------------------------
| BACKUP MANAGEMENT (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_backups'])->prefix('backups')->name('backups.')->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('index');
    Route::post('/create', [BackupController::class, 'create'])->name('create');
    Route::get('/{id}/download', [BackupController::class, 'download'])->name('download');
    Route::post('/{id}/restore', [BackupController::class, 'restore'])->name('restore');
    Route::delete('/{id}', [BackupController::class, 'destroy'])->name('destroy');
});
// === USER MANAGEMENT ===
Route::middleware(['auth', 'permission:manage_users'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
});

// === SPECIES MANAGEMENT ===
Route::middleware(['auth', 'permission:manage_species'])->group(function () {
    Route::resource('species', SpeciesController::class);
    Route::get('species/export', [SpeciesController::class, 'export'])->name('species.export');
});


// === ROLE MANAGEMENT (Opsional, jika Anda ingin admin bisa kelola role) ===
Route::middleware(['auth', 'permission:manage_roles'])->group(function () {
    Route::resource('roles', RoleController::class); // Anda perlu membuat controller ini
});
