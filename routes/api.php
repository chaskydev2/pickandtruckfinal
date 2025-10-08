<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\OfertaRutaController;
use App\Http\Controllers\Api\OfertaCargaController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rutas de la API para la aplicación.
|
*/

// Grupo de rutas de la API con prefijo 'api' (configurado en RouteServiceProvider)
// y middleware 'api' (configurado en Kernel.php)
Route::middleware('api')->group(function () {
    // Rutas de autenticación de broadcasting
    Broadcast::routes(['middleware' => ['auth:sanctum']]);
    
    // Ruta para verificar el estado de la API
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toDateTimeString(),
            'environment' => config('app.env')
        ]);
    });

    // Rutas públicas - Accesibles sin autenticación
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout');

    // Rutas públicas - Ofertas
    Route::apiResource('ofertas', OfertaRutaController::class)->only(['index', 'show']);
    Route::apiResource('ofertas_carga', OfertaCargaController::class)->only(['index', 'show'])->parameters(['ofertas_carga' => 'oferta']);
    Route::get('/cargo-types', [OfertaCargaController::class, 'getCargoTypes']);
    Route::get('/truck-types', [OfertaRutaController::class, 'getTruckTypes']);

    Route::post('auth/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::post('auth/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('documents')->group(function () {
            Route::get('/my', [DocumentController::class, 'myDocuments'])
                ->name('api.documents.my');

            Route::post('/upload-batch', [DocumentController::class, 'uploadBatch'])
                ->name('api.documents.uploadBatch');

            Route::post('/upload', [DocumentController::class, 'uploadSingle'])
                ->name('api.documents.uploadSingle');
        });

        Route::put('auth/update-profile', [ProfileController::class, 'update']);
        Route::post('auth/change-password', [\App\Http\Controllers\Api\Auth\PasswordController::class, 'update']);

        Route::get('/notifications/check', [\App\Http\Controllers\Api\NotificationController::class, 'check'])->name('api.notifications.check');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('api.notifications.markAllRead');
    });

    // Rutas protegidas con autenticación Sanctum
    Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckUserStatus::class])->group(function () {
        // Rutas de imágenes
        Route::prefix('images')->group(function () {
            // Obtener una imagen por ID (solo para administradores)
            Route::get('/{id}', [ImageController::class, 'getImage'])
                ->name('api.images.get');
                
            // Verificar si una imagen existe y obtener sus metadatos (solo para administradores)
            Route::get('/{id}/check', [ImageController::class, 'checkImage'])
                ->name('api.images.check');
        });
        
        // Rutas de Bids que requieren autenticación
        Route::get('/bids/received', [BidController::class, 'received']);
        // Usuario autenticado
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Rutas protegidas de ofertas
        Route::apiResource('ofertas', OfertaRutaController::class)->except(['index', 'show']);
        Route::apiResource('ofertas_carga', OfertaCargaController::class)->except(['index', 'show'])->parameters(['ofertas_carga' => 'oferta']);
        
        // Rutas protegidas de Bids
        Route::get('/bids', [BidController::class, 'index']);
        Route::post('/bids', [BidController::class, 'store']);
        Route::put('/bids/{bid}', [BidController::class, 'update']);
        Route::post('/bids/{bid}/status', [BidController::class, 'updateStatus']);
        Route::post('/bids/{bid}/accept', [BidController::class, 'accept']);
        Route::post('/bids/{bid}/reject', [BidController::class, 'reject']);
        Route::delete('/bids/{bid}', [BidController::class, 'destroy']);
        
        // Rutas de Chat
        Route::prefix('chats')->group(function () {
            Route::get('/', [ChatController::class, 'index']);
            Route::get('/{chat}', [ChatController::class, 'show']);
            Route::post('/{chat}/message', [ChatController::class, 'message']);
            Route::get('/{chat}/messages', [ChatController::class, 'getNewMessages']);
        });

        Route::prefix('work')->group(function () {
            Route::get('/{bid}', [\App\Http\Controllers\Api\WorkProgressController::class, 'show'])
                ->name('api.work.show');
            Route::get('/{bid}/check-status', [\App\Http\Controllers\Api\WorkProgressController::class, 'checkStatus'])
                ->name('api.work.check');
            Route::post('/{bid}/request-completion', [\App\Http\Controllers\Api\WorkProgressController::class, 'requestCompletion'])
                ->name('api.work.request');
            Route::post('/{bid}/confirm-completion', [\App\Http\Controllers\Api\WorkProgressController::class, 'confirmCompletion'])
                ->name('api.work.confirm');
            Route::post('/{bid}/reject-completion', [\App\Http\Controllers\Api\WorkProgressController::class, 'rejectCompletion'])
                ->name('api.work.reject');
        });
        
        // Cerrar sesión
        Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout');
    });
});