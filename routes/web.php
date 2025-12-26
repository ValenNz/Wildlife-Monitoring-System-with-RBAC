<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GeozoneController;
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
| Web Routes - Wildlife Monitoring System (MODULAR & RBAC-READY)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

// Login Routes

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes (opsional)
Route::get('/register', function () {
    return view('auth.register');
})->name('register');


// Home - Redirect to Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// Authentication routes
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTES (Akses semua peran)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/animals/export', [DashboardController::class, 'exportAnimals'])->name('animals.export');
        Route::get('/animals/{id}', [DashboardController::class, 'animalDetail'])->name('animals.detail');
        Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    });
});

/*
|--------------------------------------------------------------------------
| ANIMAL MANAGEMENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_animals'])->group(function () {
    Route::resource('animals', AnimalController::class);
    Route::prefix('animals')->name('animals.')->group(function () {
        Route::get('export', [AnimalController::class, 'export'])->name('export');
        Route::get('{id}/tracking', [AnimalController::class, 'tracking'])->name('tracking');
        Route::post('{id}/assign-device', [AnimalController::class, 'assignDevice'])->name('assign-device');
    });
});

/*
|--------------------------------------------------------------------------
| MAP ROUTES (Akses semua peran)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_map'])->group(function () {
    Route::prefix('map')->name('map.')->group(function () {
        Route::get('/', [MapController::class, 'index'])->name('index');
        Route::get('/track/{id}', [MapController::class, 'trackAnimal'])->name('track-animal');
        Route::get('/api/positions', [MapController::class, 'getPositions'])->name('api.positions');
        Route::get('/api/heatmap', [MapController::class, 'getHeatmap'])->name('api.heatmap');
        Route::get('/api/geozones', [MapController::class, 'getGeozones'])->name('api.geozones');
        Route::get('/api/geozones/{id}/animals', [MapController::class, 'getAnimalsInZone'])->name('api.geozones.animals');
    });
});

/*
|--------------------------------------------------------------------------
| NOTIFICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_notifications'])->group(function () {
    Route::resource('notifications', NotificationController::class);
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('{id}/mark-read', [NotificationController::class, 'markRead'])->name('mark-read');
        Route::post('mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('api/unread-count', [NotificationController::class, 'unreadCount'])->name('api.unread-count');
        Route::get('api/unread', [NotificationController::class, 'getUnread'])->name('api.unread');
    });
});

/*
|--------------------------------------------------------------------------
| REPORT ROUTES
|--------------------------------------------------------------------------
*/
// View reports - semua peran kecuali konservasionis
Route::middleware(['auth', 'permission:view_reports'])->group(function () {
    Route::resource('reports', ReportController::class)->except(['create', 'store', 'edit', 'update']);
});

// Generate reports - hanya peneliti & pengambil keputusan
Route::middleware(['auth', 'permission:generate_reports'])->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('create', [ReportController::class, 'create'])->name('create');
        Route::post('store', [ReportController::class, 'store'])->name('store');
        Route::post('generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('{id}/preview', [ReportController::class, 'preview'])->name('preview');
        Route::get('{id}/export', [ReportController::class, 'export'])->name('export');
        Route::get('{id}/download', [ReportController::class, 'download'])->name('download');
    });
});

/*
|--------------------------------------------------------------------------
| DEVICE MANAGEMENT ROUTES (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_devices'])->group(function () {
    Route::resource('devices', DeviceController::class);
    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('export', [DeviceController::class, 'export'])->name('export');
        Route::post('{id}/test', [DeviceController::class, 'test'])->name('test');
        Route::post('{id}/reset', [DeviceController::class, 'reset'])->name('reset');
    });
});

/*
|--------------------------------------------------------------------------
| GEOZONE MANAGEMENT ROUTES (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_geozones'])->group(function () {
    Route::resource('geozones', GeozoneController::class);
    Route::prefix('geozones')->name('geozones.')->group(function () {
        Route::get('export', [GeozoneController::class, 'export'])->name('export');
        Route::get('{id}/animals', [GeozoneController::class, 'getAnimalsInZone'])->name('animals');
        Route::post('{id}/toggle', [GeozoneController::class, 'toggle'])->name('toggle');
    });
});

/*
|--------------------------------------------------------------------------
| HISTORICAL TRACKING ROUTES (Akses semua peran)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_map'])->group(function () {
    Route::prefix('historical-tracking')->name('historical-tracking.')->group(function () {
        Route::get('/', [HistoricalTrackingController::class, 'index'])->name('index');
        Route::get('/{id}', [HistoricalTrackingController::class, 'show'])->name('show');
        Route::get('/animal/{animalId}', [HistoricalTrackingController::class, 'byAnimal'])->name('by-animal');
        Route::get('/device/{deviceId}', [HistoricalTrackingController::class, 'byDevice'])->name('by-device');
        Route::get('/playback/{animalId}', [HistoricalTrackingController::class, 'playback'])->name('playback');
        Route::get('/export/{id}', [HistoricalTrackingController::class, 'export'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| WEATHER ROUTES (Akses semua peran)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_weather'])->group(function () {
    Route::prefix('weather')->name('weather.')->group(function () {
        Route::get('/', [WeatherController::class, 'index'])->name('index');
        Route::get('/current', [WeatherController::class, 'current'])->name('current');
        Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');
        Route::get('/history', [WeatherController::class, 'history'])->name('history');
        Route::get('/api/current', [WeatherController::class, 'apiCurrent'])->name('api.current');
        Route::get('/api/forecast', [WeatherController::class, 'apiForecast'])->name('api.forecast');
    });
});

/*
|--------------------------------------------------------------------------
| INCIDENT MANAGEMENT ROUTES (Konservasionis & Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_incidents'])->group(function () {
    Route::resource('incidents', IncidentController::class);
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::post('{id}/resolve', [IncidentController::class, 'resolve'])->name('resolve');
        Route::post('{id}/escalate', [IncidentController::class, 'escalate'])->name('escalate');
        Route::post('{id}/assign', [IncidentController::class, 'assign'])->name('assign');
        Route::get('export', [IncidentController::class, 'export'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| ACTIVITY LOG ROUTES (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:view_activity_logs'])->group(function () {
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
        Route::get('/user/{userId}', [ActivityLogController::class, 'byUser'])->name('by-user');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
        Route::delete('/clear', [ActivityLogController::class, 'clear'])->name('clear');
    });
});

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT ROUTES (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_users'])->group(function () {
    Route::resource('users', UserController::class);
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('export', [UserController::class, 'export'])->name('export');
        Route::get('{id}/activity', [UserController::class, 'activity'])->name('activity');
    });
});

/*
|--------------------------------------------------------------------------
| SMART INTEGRATION ROUTES (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_smart'])->group(function () {
    Route::resource('smart-integrations', SmartIntegrationController::class);
    Route::prefix('smart-integrations')->name('smart-integrations.')->group(function () {
        Route::post('{id}/test', [SmartIntegrationController::class, 'test'])->name('test');
        Route::post('{id}/sync', [SmartIntegrationController::class, 'sync'])->name('sync');
        Route::post('{id}/toggle', [SmartIntegrationController::class, 'toggle'])->name('toggle');
        Route::get('{id}/logs', [SmartIntegrationController::class, 'logs'])->name('logs');
    });
});

/*
|--------------------------------------------------------------------------
| BACKUP MANAGEMENT ROUTES (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:manage_backups'])->group(function () {
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/', [BackupController::class, 'store'])->name('store');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/{id}/download', [BackupController::class, 'download'])->name('download');
        Route::post('/{id}/restore', [BackupController::class, 'restore'])->name('restore');
        Route::delete('/{id}', [BackupController::class, 'destroy'])->name('destroy');
    });
});
