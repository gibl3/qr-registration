<?php

// +--------------------------------------------------------------------+
// |                                                                    |
// | ██████╗ ███████╗███████╗ █████╗  ██████╗████████╗ ██████╗ ██████╗  |
// | ██╔══██╗██╔════╝██╔════╝██╔══██╗██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗ |
// | ██████╔╝█████╗  █████╗  ███████║██║        ██║   ██║   ██║██████╔╝ |
// | ██╔══██╗██╔══╝  ██╔══╝  ██╔══██║██║        ██║   ██║   ██║██╔══██╗ |
// | ██║  ██║███████╗██║     ██║  ██║╚██████╗   ██║   ╚██████╔╝██║  ██║ |
// | ╚═╝  ╚═╝╚══════╝╚═╝     ╚═╝  ╚═╝ ╚═════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝ |
// |                                                                    |
// +--------------------------------------------------------------------+

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SubjectStudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;

// Public Routes
Route::get('/', fn() => view('index'))->name('index');

Route::get('/login', [AuthController::class, 'index'])->name('login');

Route::post('/login/auth', [AuthController::class, 'authenticate'])->name('login.auth');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/store', [AuthController::class, 'storeAdmin'])->name('store');



// Student Registration Routes
Route::prefix('student')->name('registration.')->group(function () {
    Route::get('/register', [RegistrationController::class, 'create'])->name('create');
    Route::post('/store', [RegistrationController::class, 'store'])->name('store');
    Route::get('/{encryptedStudentId}', [RegistrationController::class, 'show'])->name('show');
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->middleware('is_instructor')->group(function (): void {


    Route::get('/', [InstructorController::class, 'index'])->name('index');

    // Scan Routes
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/store', [ScanController::class, 'store'])->name('scan.store');

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');

        Route::get('/{attendance}/edit', [AttendanceController::class,   'edit'])->name('edit');

        Route::put('/{attendance}/update', [AttendanceController::class, 'update'])->name('update');

        Route::post('/delete', [AttendanceController::class, 'destroyMany'])->name('destroyMany');
    });

    Route::prefix('students')->name('students.')->group(
        function () {
            Route::get('/', [StudentController::class, 'index'])->name('index');

            Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');

            Route::put('/{student}/update', [StudentController::class, 'update'])->name('update');

            Route::post('/delete', [StudentController::class, 'destroyMany'])->name('destroyMany');
        }
    );

    Route::prefix('settings')->name('settings.')->group(
        function () {
            Route::get('/edit', [SettingsController::class, 'edit'])->name('edit');
            Route::post('/update', [InstructorController::class, 'updatePassword'])->name('updatePassword');
        }
    );

    Route::prefix('subjects')->name('subjects.')->group(
        function () {
            Route::get('/', [SubjectController::class, 'index'])->name('index');
            Route::get('/{subject}/show', [SubjectController::class, 'show'])->name('show');

            Route::post('/store', [SubjectController::class, 'store'])->name('store');

            Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');

            Route::put('/{subject}/update', [SubjectController::class, 'update'])->name('update');

            Route::post('/{subject}/enroll', [SubjectStudentController::class, 'enroll'])
                ->name('enroll');

            Route::post('/{subject}/unenroll', [SubjectStudentController::class, 'unenroll'])
                ->name('unenroll');
        }
    );
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('is_admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('index');

    // Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('instructor')->name('instructor.')->group(
        function () {
            Route::get('/', [InstructorController::class, 'showTable'])->name('showTable');
            Route::get('/add', [InstructorController::class, 'create'])->name('create');
            Route::post('/store', [InstructorController::class, 'store'])->name('store');
            Route::get('/{instructor}/edit', [InstructorController::class, 'edit'])->name('edit');
            Route::put('/{instructor}/update', [InstructorController::class, 'update'])->name('update');
            Route::post('/delete', [InstructorController::class, 'destroyMany'])->name('destroyMany');
        }
    );
});
