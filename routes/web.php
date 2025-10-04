<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Strona główna
Route::get('/', function () {
    return view('welcome');
});

// Panel użytkownika (po zalogowaniu)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Autoryzacja
require __DIR__.'/auth.php';

// Panel użytkownika (po zalogowaniu)
Route::get('/home', [HomeController::class, 'index'])->name('home');

//  **Profil użytkownika (tylko dla zalogowanych)**
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change_password');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.update_password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//  **Placówki**
Route::middleware(['auth'])->group(function () {
    Route::resource('facilities', FacilityController::class)->except(['index', 'show']);
});
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

//  **Forum**
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [ForumTopicController::class, 'categories'])->name('categories');
    Route::get('/category/{category}', [ForumTopicController::class, 'index'])->name('index');
    Route::get('/topic/{topic}', [ForumTopicController::class, 'show'])->name('show');

    Route::middleware(['auth'])->group(function () {
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
Route::middleware(['auth'])->group(function () {
    Route::resource('specialists', SpecialistController::class, [
        'parameters' => ['specialists' => 'id']
    ])->except(['index', 'show']);
});
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

// Moje wizyty (tylko dla zalogowanych)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-visits', [VisitController::class, 'myVisits'])->name('my-visits');
});

//  **Wylogowanie użytkownika**
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Kontakt
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
