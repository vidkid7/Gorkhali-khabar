<?php

use App\Http\Controllers\Api\V1\AdminPrimitiveController;
use App\Http\Controllers\Api\V1\AdvertisementController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookmarkController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CommentVoteController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\GalleryController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\NepseController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\QuickLinkController;
use App\Http\Controllers\Api\V1\RashifalController;
use App\Http\Controllers\Api\V1\ReelController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\SocialAuthController;
use App\Http\Controllers\Api\V1\SportsController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\TrendingController;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/v1/status', static fn () => ApiResponse::success(['service' => 'gorkhali-api']));
Route::get('/v1/articles', [ArticleController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/articles/slug/{slug}', [ArticleController::class, 'showBySlug'])->middleware('throttle:reads');
Route::get('/v1/articles/{id}', [ArticleController::class, 'show'])->middleware('throttle:reads');
Route::post('/v1/articles/{id}/view', [ArticleController::class, 'recordView'])->middleware('throttle:tracking');
Route::post('/v1/articles', [ArticleController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'role:AUTHOR,EDITOR,ADMIN', 'throttle:writes']);
Route::put('/v1/articles/{id}', [ArticleController::class, 'update'])
    ->middleware(['auth:sanctum', 'active.session', 'role:AUTHOR,EDITOR,ADMIN', 'throttle:writes']);
Route::delete('/v1/articles/{id}', [ArticleController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/categories', [CategoryController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/home', [HomeController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/tags', [TagController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/quick-links', [QuickLinkController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/search', [SearchController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/trending', [TrendingController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/settings', [SettingController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/rashifal', [RashifalController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/calendar/holidays', [CalendarController::class, 'holidays'])->middleware('throttle:reads');
Route::get('/v1/calendar/panchang', [CalendarController::class, 'panchang'])->middleware('throttle:reads');
Route::get('/v1/finance/exchange-rates', [FinanceController::class, 'exchangeRates'])->middleware('throttle:reads');
Route::get('/v1/finance/gold-silver', [FinanceController::class, 'goldSilver'])->middleware('throttle:reads');
Route::get('/v1/sports/tournaments', [SportsController::class, 'tournaments'])->middleware('throttle:reads');
Route::get('/v1/sports/matches', [SportsController::class, 'matches'])->middleware('throttle:reads');
Route::get('/v1/nepse', [NepseController::class, 'index'])->middleware('throttle:reads');
Route::post('/v1/sports/tournaments', [SportsController::class, 'storeTournament'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::post('/v1/sports/matches', [SportsController::class, 'storeMatch'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::put('/v1/sports/matches/{id}', [SportsController::class, 'updateMatch'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::post('/v1/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:newsletter');
Route::get('/v1/media', [MediaController::class, 'index'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR,AUTHOR']);
Route::post('/v1/media', [MediaController::class, 'store'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR,AUTHOR', 'throttle:writes']);
Route::put('/v1/media/{id}', [MediaController::class, 'update'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR,AUTHOR', 'throttle:writes']);
Route::delete('/v1/media/{id}', [MediaController::class, 'destroy'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/galleries', [GalleryController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/galleries/{id}', [GalleryController::class, 'show'])->middleware('throttle:reads');
Route::post('/v1/galleries', [GalleryController::class, 'store'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::put('/v1/galleries/{id}', [GalleryController::class, 'update'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::delete('/v1/galleries/{id}', [GalleryController::class, 'destroy'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/reels', [ReelController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/reels/{id}', [ReelController::class, 'show'])->middleware('throttle:reads');
Route::post('/v1/reels', [ReelController::class, 'store'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::put('/v1/reels/{id}', [ReelController::class, 'update'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::delete('/v1/reels/{id}', [ReelController::class, 'destroy'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/ads', [AdvertisementController::class, 'index'])->middleware('throttle:reads');
Route::get('/v1/ads/positions', [AdvertisementController::class, 'positions'])->middleware('throttle:reads');
Route::post('/v1/ads/positions', [AdvertisementController::class, 'storePosition'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::post('/v1/ads/{id}/click', fn (string $id, AdvertisementController $controller) => $controller->track($id, 'clicks'))->middleware('throttle:tracking');
Route::post('/v1/ads/{id}/impression', fn (string $id, AdvertisementController $controller) => $controller->track($id, 'impressions'))->middleware('throttle:tracking');
Route::post('/v1/ads', [AdvertisementController::class, 'store'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::put('/v1/ads/{id}', [AdvertisementController::class, 'update'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::delete('/v1/ads/{id}', [AdvertisementController::class, 'destroy'])->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::post('/v1/categories', [CategoryController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::put('/v1/categories', [CategoryController::class, 'update'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/categories', [CategoryController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/comments', [CommentController::class, 'index'])->middleware('throttle:reads');
Route::post('/v1/comments', [CommentController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'throttle:comments']);
Route::patch('/v1/comments/{id}', [CommentController::class, 'update'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::post('/v1/comments/{id}/vote', [CommentVoteController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'throttle:writes']);
Route::get('/v1/bookmarks', [BookmarkController::class, 'index'])
    ->middleware(['auth:sanctum', 'active.session']);
Route::post('/v1/bookmarks', [BookmarkController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'throttle:writes']);
Route::delete('/v1/bookmarks/{articleId}', [BookmarkController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'active.session', 'throttle:writes']);
Route::get('/v1/admin/rashifal', [RashifalController::class, 'adminIndex'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR']);
Route::post('/v1/admin/rashifal', [RashifalController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::put('/v1/admin/rashifal', [RashifalController::class, 'update'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/admin/rashifal', [RashifalController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/admin/holidays', [CalendarController::class, 'adminIndex'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR']);
Route::post('/v1/admin/holidays', [CalendarController::class, 'store'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::put('/v1/admin/holidays', [CalendarController::class, 'update'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/admin/holidays', [CalendarController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/admin/forex', [FinanceController::class, 'forexIndex'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR']);
Route::post('/v1/admin/forex', [FinanceController::class, 'forexStore'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::put('/v1/admin/forex', [FinanceController::class, 'forexUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/admin/forex', [FinanceController::class, 'forexDestroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/admin/gold-silver', [FinanceController::class, 'metalsIndex'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR']);
Route::post('/v1/admin/gold-silver', [FinanceController::class, 'metalsStore'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::put('/v1/admin/gold-silver', [FinanceController::class, 'metalsUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/admin/gold-silver', [FinanceController::class, 'metalsDestroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::post('/v1/admin/tags', [AdminPrimitiveController::class, 'tagsStore'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::put('/v1/admin/tags', [AdminPrimitiveController::class, 'tagsUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/admin/tags', [AdminPrimitiveController::class, 'tagsDestroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::get('/v1/admin/quick-links', [AdminPrimitiveController::class, 'linksIndex'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR']);
Route::post('/v1/admin/quick-links', [AdminPrimitiveController::class, 'linksStore'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::patch('/v1/admin/quick-links/{id}', [AdminPrimitiveController::class, 'linksUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::delete('/v1/admin/quick-links/{id}', [AdminPrimitiveController::class, 'linksDestroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::get('/v1/admin/settings', [AdminPrimitiveController::class, 'settingsIndex'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN']);
Route::put('/v1/admin/settings', [AdminPrimitiveController::class, 'settingsUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::post('/v1/admin/breaking-news', [AdminPrimitiveController::class, 'breakingNewsStore'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::patch('/v1/admin/breaking-news/{id}', [AdminPrimitiveController::class, 'breakingNewsUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN,EDITOR', 'throttle:writes']);
Route::delete('/v1/admin/breaking-news/{id}', [AdminPrimitiveController::class, 'breakingNewsDestroy'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);
Route::patch('/v1/admin/users/{id}/role', [AdminPrimitiveController::class, 'userRoleUpdate'])
    ->middleware(['auth:sanctum', 'active.session', 'role:ADMIN', 'throttle:writes']);

Route::prefix('/v1/auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:auth');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:auth');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->middleware('throttle:auth');
    Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->middleware('throttle:auth');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/session', [AuthController::class, 'session'])->middleware(['auth:sanctum', 'active.session']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/send-verification', [AuthController::class, 'sendVerification'])
        ->middleware(['auth:sanctum', 'active.session', 'throttle:auth']);
    Route::get('/google/redirect', [SocialAuthController::class, 'redirect'])->middleware('web');
    Route::get('/google/callback', [SocialAuthController::class, 'callback'])->middleware('web');
});
