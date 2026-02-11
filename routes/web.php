<?php declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlockedController;
use App\Http\Controllers\OfertaRutaController;
use App\Http\Controllers\OfertaCargaController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WorkProgressController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\AdminDocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifiedUser;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\IsAdmin;


require __DIR__.'/auth.php';

// Rutas de autenticación de broadcasting
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::middleware(['auth', CheckUserStatus::class])->group(function () {
    // Ruta para verificar el estado del usuario (usada por el polling)
    Route::get('/user/check-status', [ProfileController::class, 'checkStatus'])->name('user.checkStatus');
    
    Route::get('/profile/document-submission', [ProfileController::class, 'documentSubmission'])
        ->name('profile.document-submission');
    
    Route::post('/profile/documents/upload', [ProfileController::class, 'uploadDocument'])
        ->name('profile.upload-document');
    
    // Ruta para verificar cambios en documentos (auto-refresh)
    Route::get('/profile/check-document-status', [ProfileController::class, 'checkDocumentStatus'])
        ->name('profile.check-document-status');
    
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', VerifiedUser::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/documents', [ProfileController::class, 'documents'])->name('profile.documents');

    Route::get('/partials/ofertas-carga/card/{id}', function ($id) {
        $oferta = \App\Models\OfertaCarga::with(['cargoType','bids','user.empresa'])->findOrFail($id);
        return view('partials.oferta_carga_card', compact('oferta'))->render();
    })->name('partials.ofertas_carga.card');

    Route::get('/partials/ofertas-ruta/card/{id}', function ($id) {
        $oferta = \App\Models\OfertaRuta::with(['truckType','bids','user.empresa'])->findOrFail($id);
        return view('partials.oferta_ruta_card', compact('oferta'))->render();
    })->name('partials.ofertas_ruta.card');

    Route::resource('ofertas', OfertaRutaController::class);
    Route::resource('ofertas_carga', OfertaCargaController::class, [
        'parameters' => ['ofertas_carga' => 'oferta']
    ]);
    
    Route::prefix('bids')->name('bids.')->group(function () {
        Route::get('/', [BidController::class, 'index'])->name('index');
        Route::delete('/{bid}', [BidController::class, 'destroy'])->name('destroy');
        Route::get('/received', [BidController::class, 'received'])->name('received');
        Route::get('/create', [BidController::class, 'create'])->name('create');
        Route::post('/', [BidController::class, 'store'])->name('store')->middleware(['verified']);
        Route::get('/{bid}/edit', [BidController::class, 'edit'])->name('edit');
        Route::put('/{bid}', [BidController::class, 'update'])->name('update');
        Route::post('/{bid}/accept', [BidController::class, 'accept'])->name('accept');
        Route::post('/{bid}/reject', [BidController::class, 'reject'])->name('reject');
    });

    Route::get('/empresa', [EmpresaController::class, 'show'])->name('empresas.show');
    Route::get('/empresa/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
    Route::put('/empresa/update', [EmpresaController::class, 'update'])->name('empresas.update');
    
    Route::get('/empresas', [EmpresaController::class, 'show']);
    Route::get('/empresas/edit', [EmpresaController::class, 'edit']);

    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
        Route::post('/{chat}/message', [ChatController::class, 'message'])->name('message');
        Route::get('/{chat}/messages', [ChatController::class, 'getNewMessages'])->name('get-messages');
    });

    // Rutas para la gestión del progreso de trabajos
    Route::prefix('work')->name('work.')->group(function () {
        // Ruta principal para ver el progreso
        Route::get('/{bid}', [WorkProgressController::class, 'show'])
            ->name('show')
            ->where('bid', '[0-9]+');
        
        // Rutas para la finalización del trabajo
        Route::post('/{bid}/request-completion', [WorkProgressController::class, 'requestCompletion'])
            ->name('request-completion')
            ->where('bid', '[0-9]+');
            
        Route::post('/{bid}/confirm-completion', [WorkProgressController::class, 'confirmCompletion'])
            ->name('confirm-completion')
            ->where('bid', '[0-9]+');
            
        Route::post('/{bid}/reject-completion', [WorkProgressController::class, 'rejectCompletion'])
            ->name('reject-completion')
            ->where('bid', '[0-9]+');
            
        // Ruta para verificar el estado (usada por AJAX)
        Route::get('/{bid}/check-status', [WorkProgressController::class, 'checkStatus'])
            ->name('check-status')
            ->where('bid', '[0-9]+');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/check', [NotificationController::class, 'check'])->name('check');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
        Route::get('/test-send', [NotificationController::class, 'testSend'])->name('test-send');
    });

    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
});

Route::get('/cuenta-bloqueada', [\App\Http\Controllers\AccountController::class, 'showBlocked'])
    ->name('account.blocked');

Route::get('/contactar-admin', [\App\Http\Controllers\AccountController::class, 'contactAdmin'])
    ->name('contact.admin');

Route::get('storage/{path}', function($path) {
    $filePath = public_path('storage/' . $path);
    if (file_exists($filePath)) {
        return response()->download($filePath, basename($filePath), [
            'Content-Type' => mime_content_type($filePath),
        ]);
    }
    abort(404);
})->where('path', '.*');

// Ruta para servir documentos subidos por usuarios
Route::get('documents/{path}', function($path) {
    $filePath = public_path('documents/' . $path);
    if (file_exists($filePath)) {
        return response()->download($filePath, basename($filePath), [
            'Content-Type' => mime_content_type($filePath),
        ]);
    }
    abort(404);
})->where('path', '.*');

// Ruta para servir templates y otros archivos estáticos
Route::get('templates/{filename}', function($filename) {
    $filePath = public_path('templates/' . $filename);
    if (file_exists($filePath)) {
        return response()->download($filePath, $filename, [
            'Content-Type' => mime_content_type($filePath),
        ]);
    }
    abort(404);
});

// Admin routes for document management
Route::middleware(['auth', CheckUserStatus::class, IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    // Document management routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
        Route::get('/user/{userId}', [AdminDocumentController::class, 'userDocuments'])->name('user');
        Route::post('/{document}/update-status', [AdminDocumentController::class, 'updateStatus'])->name('update-status');
        Route::post('/{document}/approve', [AdminDocumentController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [AdminDocumentController::class, 'reject'])->name('reject');
        Route::get('/pending-count', [AdminDocumentController::class, 'pendingCount'])->name('pending-count');
    });
});

Route::fallback(function () {
    return redirect()->route('home');
});