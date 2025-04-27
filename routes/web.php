<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Strona główna
Route::get('/', function () {
    return view('welcome');
});

// Autoryzacja (Laravel Breeze / Jetstream / Fortify)
Auth::routes();

// Panel użytkownika (po zalogowaniu)
Route::get('/home', [HomeController::class, 'index'])->name('home');

//  **Profil użytkownika (tylko dla zalogowanych)**
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Zmiana hasła
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change_password');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.update_password');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.delete');
});

//  **Placówki (tylko dla zalogowanych)**
Route::middleware(['auth'])->group(function () {
    Route::resource('facilities', FacilityController::class)->except(['index', 'show']);
});
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

//  **Forum**
Route::prefix('forum')->group(function () {
    Route::get('/', [ForumTopicController::class, 'index'])->name('forum.index');
    Route::middleware(['auth'])->group(function () {
        Route::post('/topics', [ForumTopicController::class, 'store'])->name('forum.store');
        Route::delete('/topics/{topic}', [ForumTopicController::class, 'destroy'])->name('forum.destroy');

        Route::post('/posts', [ForumPostController::class, 'store'])->name('forum.post.store');
        Route::delete('/posts/{post}', [ForumPostController::class, 'destroy'])->name('forum.post.destroy');
    });
});

//  **Specjaliści**
Route::get('/specialists', [SpecialistController::class, 'index'])->name('specialists.index');
Route::get('/specialists/{id}', [SpecialistController::class, 'show'])->name('specialists.show');

// **Recenzje placówek**
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index'); 
Route::middleware(['auth'])->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
//  **Reakcje (like/dislike)**
    Route::middleware(['auth'])->group(function () {
        Route::get('/reactions', [ReactionController::class, 'index'])->name('reactions.index'); 
        Route::post('/reactions', [ReactionController::class, 'store'])->name('reactions.store');
        Route::delete('/reactions/{reaction}', [ReactionController::class, 'destroy'])->name('reactions.destroy');
    });
//  **Wylogowanie użytkownika**
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
