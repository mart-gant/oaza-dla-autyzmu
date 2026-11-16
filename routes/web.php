<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AboutController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Strona główna
Route::get('/', function () {
    return view('welcome');
});

// Static pages
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

// Health check endpoint
Route::get('/health', function () {
    $db = [
        'driver' => config('database.default'),
        'ok' => false,
    ];
    try {
        DB::connection()->getPdo();
        $db['ok'] = true;
    } catch (\Throwable $e) {
        $db['error'] = 'DB connection failed';
    }

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'env' => config('app.env'),
        'db' => $db,
        'time' => now()->toIso8601String(),
    ]);
})->name('health');

// Panel użytkownika (po zalogowaniu)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Autoryzacja
require __DIR__.'/auth.php';

// Export danych użytkownika (RODO)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/export/my-data', [\App\Http\Controllers\DataExportController::class, 'exportUserData'])->name('export.user-data');
});

// Panel użytkownika (po zalogowaniu)
Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

//  **Profil użytkownika (tylko dla zalogowanych i zweryfikowanych)**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change_password');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.update_password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Two Factor Authentication routes
    Route::post('/user/two-factor-authentication', [\App\Http\Controllers\Profile\TwoFactorAuthenticationController::class, 'store']);
    Route::delete('/user/two-factor-authentication', [\App\Http\Controllers\Profile\TwoFactorAuthenticationController::class, 'destroy']);
    Route::post('/user/two-factor-recovery-codes', [\App\Http\Controllers\Profile\TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes']);
    Route::get('/user/two-factor-recovery-codes', [\App\Http\Controllers\Profile\TwoFactorAuthenticationController::class, 'showRecoveryCodes']);
});

// **Panel admina (tylko dla adminów i zweryfikowanych)**
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Admin management routes
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::post('/users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');
    Route::get('/stop-impersonate', [AdminUserController::class, 'stopImpersonate'])->name('users.stopImpersonate');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend');

    Route::get('/facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
    Route::delete('/facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('facilities.destroy');
    
    // Admin audit logs
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [\App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('audit-logs.export');

    // API-style admin routes (session-authenticated JSON endpoints)
    Route::prefix('api/admin')->name('api.admin.')->middleware(['auth:sanctum', 'api'])->group(function () {
        Route::get('/users', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::post('/users/{user}/role', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateRole']);
        Route::delete('/users/{user}', [\App\Http\Controllers\Api\Admin\UserController::class, 'destroy']);
        Route::post('/users/{user}/suspend', [\App\Http\Controllers\Api\Admin\UserController::class, 'suspend']);
        Route::post('/users/{user}/unsuspend', [\App\Http\Controllers\Api\Admin\UserController::class, 'unsuspend']);

        Route::get('/facilities', [\App\Http\Controllers\Api\Admin\FacilityController::class, 'index']);
        Route::delete('/facilities/{facility}', [\App\Http\Controllers\Api\Admin\FacilityController::class, 'destroy']);
    });
});

//  **Placówki**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('facilities', FacilityController::class)->except(['index', 'show']);
});
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

//  **Wydarzenia (Kalendarz)**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('events', \App\Http\Controllers\EventController::class)->except(['index', 'show']);
});
Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');

//  **Artykuły (Poradnik wiedzy)**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('articles', ArticleController::class)->except(['index', 'show']);
});
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

//  **Admin - Forum Categories**
Route::middleware(['auth', 'verified'])->prefix('admin/forum')->name('admin.forum.')->group(function () {
    Route::resource('categories', \App\Http\Controllers\Admin\ForumCategoryController::class);
});

//  **Forum**
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [ForumTopicController::class, 'categories'])->name('categories');
    Route::get('/category/{category}', [ForumTopicController::class, 'index'])->name('index');
    Route::get('/topic/{topic}', [ForumTopicController::class, 'show'])->name('show');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [ForumTopicController::class, 'create'])->name('create');
        Route::post('/', [ForumTopicController::class, 'store'])->name('store');
        Route::get('/topic/{topic}/edit', [ForumTopicController::class, 'edit'])->name('edit');
        Route::put('/topic/{topic}', [ForumTopicController::class, 'update'])->name('update');
        Route::delete('/topic/{topic}', [ForumTopicController::class, 'destroy'])->name('destroy');

        Route::post('/post', [ForumPostController::class, 'store'])->name('post.store');
        Route::get('/post/{post}/edit', [ForumPostController::class, 'edit'])->name('post.edit');
        Route::put('/post/{post}', [ForumPostController::class, 'update'])->name('post.update');
        Route::delete('/post/{post}', [ForumPostController::class, 'destroy'])->name('post.destroy');
    });
});


//  **Specjaliści**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('specialists', SpecialistController::class, [
        'parameters' => ['specialists' => 'id']
    ])->except(['index', 'show']);
});
Route::get('/specialists', [SpecialistController::class, 'index'])->name('specialists.index');
Route::get('/specialists/{id}', [SpecialistController::class, 'show'])->name('specialists.show');


// **Recenzje placówek**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

//  **Reakcje (like/dislike)**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/reactions', [ReactionController::class, 'index'])->name('reactions.index');
    Route::post('/reactions', [ReactionController::class, 'store'])->name('reactions.store');
    Route::delete('/reactions/{reaction}', [ReactionController::class, 'destroy'])->name('reactions.destroy');
});

// Moje wizyty (tylko dla zalogowanych i zweryfikowanych)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-visits', [VisitController::class, 'myVisits'])->name('my-visits');
    Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    Route::get('/visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
    Route::put('/visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
    Route::delete('/visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
});

// **Wiadomości prywatne**
Route::middleware(['auth', 'verified'])->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\MessageController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\MessageController::class, 'store'])->name('store');
    Route::get('/{conversation}', [\App\Http\Controllers\MessageController::class, 'show'])->name('show');
    Route::post('/{message}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('read');
    Route::get('/api/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount'])->name('unread-count');
});

// Kontakt
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle.contact')->name('contact.send');

// O projekcie
Route::get('/about', [AboutController::class, 'index'])->name('about');
