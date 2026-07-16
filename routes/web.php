<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\FileController as AdminFileController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\TimeTrackerController as AdminTimeTrackerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\FileController as ClientFileController;
use App\Http\Controllers\Client\MessageController as ClientMessageController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('client.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('throttle:login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::put('api/dashboard/projects/{project}/remove', [App\Http\Controllers\Admin\DashboardController::class, 'removeProject'])->name('api.dashboard.remove-project')->middleware('throttle:api');
    Route::put('api/dashboard/clear-all', [App\Http\Controllers\Admin\DashboardController::class, 'clearAll'])->name('api.dashboard.clear-all')->middleware('throttle:api');

    Route::resource('projects', AdminProjectController::class);

    Route::post('projects/{project}/tasks', [AdminTaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('projects/{project}/tasks/{task}', [AdminTaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('projects/{project}/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('projects.tasks.destroy');
    Route::post('projects/{project}/tasks/{task}/toggle-status', [AdminTaskController::class, 'toggleStatus'])->name('projects.tasks.toggle');
    Route::post('projects/{project}/tasks/{task}/images', [AdminTaskController::class, 'uploadImage'])->name('projects.tasks.images.store');
    Route::delete('projects/{project}/tasks/{task}/images/{image}', [AdminTaskController::class, 'destroyImage'])->name('projects.tasks.images.destroy');

    Route::post('projects/{project}/files', [AdminFileController::class, 'store'])->name('projects.files.store');
    Route::get('projects/{project}/files/{file}/download', [AdminFileController::class, 'download'])->name('projects.files.download');
    Route::delete('projects/{project}/files/{file}', [AdminFileController::class, 'destroy'])->name('projects.files.destroy');

    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{project}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{project}', [AdminMessageController::class, 'store'])->name('messages.store');

    Route::get('projects-export/csv', [AdminProjectController::class, 'exportCsv'])->name('projects.export.csv')->middleware('throttle:exports');
    Route::get('projects-export/pdf', [AdminProjectController::class, 'exportPdf'])->name('projects.export.pdf')->middleware('throttle:exports');
    Route::get('projects/{project}/export/csv', [AdminProjectController::class, 'exportProjectCsv'])->name('projects.export.project.csv')->middleware('throttle:exports');
    Route::get('projects/{project}/export/pdf', [AdminProjectController::class, 'exportProjectPdf'])->name('projects.export.project.pdf')->middleware('throttle:exports');

    Route::get('timetracker', [AdminTimeTrackerController::class, 'index'])->name('timetracker.index');
    Route::get('timetracker/export/csv', [AdminTimeTrackerController::class, 'exportCsv'])->name('timetracker.export.csv')->middleware('throttle:exports');
    Route::get('timetracker/export/pdf', [AdminTimeTrackerController::class, 'exportPdf'])->name('timetracker.export.pdf')->middleware('throttle:exports');
    Route::post('timetracker/start', [AdminTimeTrackerController::class, 'start'])->name('timetracker.start');
    Route::post('timetracker/{entry}/stop', [AdminTimeTrackerController::class, 'stop'])->name('timetracker.stop');
    Route::delete('timetracker/{entry}', [AdminTimeTrackerController::class, 'destroy'])->name('timetracker.destroy');
    Route::post('timetracker', [AdminTimeTrackerController::class, 'store'])->name('timetracker.store');
    Route::get('timetracker/{entry}/edit', [AdminTimeTrackerController::class, 'edit'])->name('timetracker.edit');
    Route::put('timetracker/{entry}', [AdminTimeTrackerController::class, 'update'])->name('timetracker.update');
    Route::get('api/projects/{projectId}/tasks', [AdminTimeTrackerController::class, 'getTasks'])->name('api.tasks')->middleware('throttle:api');

    Route::put('api/projects/{project}/status', [AdminProjectController::class, 'updateStatus'])->name('api.projects.update-status')->middleware('throttle:api');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::delete('settings/logo', [SettingsController::class, 'clearLogo'])->name('settings.clear-logo');
});

Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');

    Route::get('projects', [ClientProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
    Route::post('projects/{project}/tasks/{task}/images', [ClientProjectController::class, 'uploadTaskImage'])->name('projects.tasks.images.store');
    Route::delete('projects/{project}/tasks/{task}/images/{image}', [ClientProjectController::class, 'destroyTaskImage'])->name('projects.tasks.images.destroy');

    Route::post('projects/{project}/files', [ClientFileController::class, 'store'])->name('projects.files.store');
    Route::get('projects/{project}/files/{file}/download', [ClientFileController::class, 'download'])->name('projects.files.download');
    Route::delete('projects/{project}/files/{file}', [ClientFileController::class, 'destroy'])->name('projects.files.destroy');

    Route::get('messages', [ClientMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{project}', [ClientMessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{project}', [ClientMessageController::class, 'store'])->name('messages.store');

    Route::get('projects-export/csv', [ClientProjectController::class, 'exportCsv'])->name('projects.export.csv')->middleware('throttle:exports');
    Route::get('projects/{project}/export/csv', [ClientProjectController::class, 'exportProjectCsv'])->name('projects.export.project.csv')->middleware('throttle:exports');

});
