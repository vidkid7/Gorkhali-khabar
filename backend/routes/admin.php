<?php

use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookmarkController;
use App\Http\Controllers\Admin\BreakingNewsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GalleryImageController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\MatchRecordController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PanchangController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuickLinkController;
use App\Http\Controllers\Admin\RashifalController;
use App\Http\Controllers\Admin\ReelController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SportsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebStoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Blade UI)
|--------------------------------------------------------------------------
|
| All routes live under a custom ADMIN_PATH prefix (default: gorkhali-admin).
| Uses the "web" middleware group (sessions, CSRF, cookies). Authentication
| is handled by Laravel's session guard; role enforcement is delegated to
| the "role.web" alias.
|
*/

// Public auth endpoints
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected panel
Route::middleware(['auth', 'role.web:ADMIN,EDITOR,AUTHOR'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (any staff member can manage their own account)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Articles — all staff
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::post('/articles/{article}/archive', [ArticleController::class, 'archive'])->name('articles.archive');

    // Categories & tags — ADMIN/EDITOR
    Route::middleware('role.web:ADMIN,EDITOR')->group(function () {
        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->names('categories');

        Route::resource('tags', TagController::class)
            ->except(['show'])
            ->names('tags');

        Route::resource('comments', CommentController::class)
            ->only(['index', 'update', 'destroy'])
            ->names('comments');

        Route::resource('galleries', GalleryController::class)
            ->except(['show'])
            ->names('galleries');

        // Gallery images (nested under galleries)
        Route::resource('gallery-images', GalleryImageController::class)
            ->except(['show'])
            ->names('gallery-images');

        Route::resource('reels', ReelController::class)
            ->except(['show'])
            ->names('reels');

        Route::resource('breaking-news', BreakingNewsController::class)
            ->except(['show'])
            ->parameters(['breaking-news' => 'breakingNews'])
            ->names('breaking-news');

        Route::resource('holidays', HolidayController::class)
            ->except(['show'])
            ->names('holidays');

        Route::resource('rashifal', RashifalController::class)
            ->except(['show'])
            ->names('rashifal');

        Route::resource('quick-links', QuickLinkController::class)
            ->except(['show'])
            ->parameters(['quick-links' => 'quickLink'])
            ->names('quick-links');

        Route::resource('sports', SportsController::class)
            ->except(['show'])
            ->parameters(['sports' => 'sport'])
            ->names('sports');

        // Sports sub-resources
        Route::resource('teams', TeamController::class)
            ->except(['show'])
            ->names('teams');

        Route::resource('matches', MatchRecordController::class)
            ->except(['show'])
            ->names('matches');

        // Web Stories
        Route::resource('web-stories', WebStoryController::class)
            ->except(['show'])
            ->parameters(['web-stories' => 'webStory'])
            ->names('web-stories');

        // Panchang (Nepali calendar)
        Route::resource('panchang', PanchangController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['panchang' => 'panchang'])
            ->names('panchang');

        // Bookmarks (read-mostly analytics)
        Route::resource('bookmarks', BookmarkController::class)
            ->only(['index', 'destroy'])
            ->names('bookmarks');

        // Finance — forex + gold-silver
        Route::get('finance/forex', [FinanceController::class, 'forex'])->name('finance.forex');
        Route::post('finance/forex', [FinanceController::class, 'storeForex'])->name('finance.forex.store');
        Route::delete('finance/forex/{forex}', [FinanceController::class, 'destroyForex'])->name('finance.forex.destroy');

        Route::get('finance/gold-silver', [FinanceController::class, 'goldSilver'])->name('finance.gold-silver');
        Route::post('finance/gold-silver', [FinanceController::class, 'storeGoldSilver'])->name('finance.gold-silver.store');
        Route::delete('finance/gold-silver/{price}', [FinanceController::class, 'destroyGoldSilver'])->name('finance.gold-silver.destroy');

        // Analytics
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        // Media library
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    });

    // ADMIN only
    Route::middleware('role.web:ADMIN')->group(function () {
        Route::resource('users', UserController::class)
            ->except(['show'])
            ->names('users');

        Route::resource('ads', AdvertisementController::class)
            ->except(['show'])
            ->names('ads');

        Route::resource('newsletter', NewsletterController::class)
            ->only(['index', 'destroy'])
            ->names('newsletter');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        // Audit log
        Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });
});