<?php

use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\AuctionController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ProxyController as AdminProxyController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ScrapingLogController as AdminScrapingLogController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\AuctionCatalogController;
use App\Http\Controllers\Auth\Admin\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\User\AuthenticatedSessionController as UserAuthenticatedSessionController;
use App\Http\Controllers\Auth\User\RegisteredUserController;
use App\Http\Controllers\Public\AuctionController as PublicAuctionController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\StaticPageController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\User\AuctionController as UserAuctionController;
use App\Http\Controllers\User\AuctionDetailController as UserAuctionDetailController;
use App\Http\Controllers\User\BidController as UserBidController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SettingsController as UserSettingsController;
use App\Http\Controllers\User\WalletController as UserWalletController;
use App\Http\Controllers\User\WatchlistController as UserWatchlistController;
use App\Http\Controllers\User\WithdrawalController as UserWithdrawalController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/auctions', [AuctionCatalogController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{auction}', [PublicAuctionController::class, 'show'])->name('auctions.show');

Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/how-it-works', [StaticPageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('faq');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::post('/stripe/webhook', StripeWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('stripe.webhook');

Route::middleware('guest:user')->group(function () {
    Route::get('/login', [UserAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [UserAuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth:user')->group(function () {
    Route::post('/logout', [UserAuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('user.dashboard');

    Route::prefix('app')->name('user.')->group(function () {
        Route::get('/auctions', [UserAuctionController::class, 'index'])->name('auctions.index');
        Route::get('/auctions/{auction}', [UserAuctionDetailController::class, 'show'])->name('auctions.show');
        Route::post('/auctions/{auction}/bids', [UserAuctionDetailController::class, 'storeBid'])->name('auctions.bids.store');
        Route::post('/auctions/{auction}/confirm-shipment', [UserAuctionDetailController::class, 'confirmShipment'])->name('auctions.confirm-shipment');
        Route::get('/bids', [UserBidController::class, 'index'])->name('bids.index');
        Route::post('/bids/{bid}/cancel', [UserBidController::class, 'cancel'])->name('bids.cancel');
        Route::get('/watchlist', [UserWatchlistController::class, 'index'])->name('watchlist.index');
        Route::post('/watchlist/{auction}', [UserWatchlistController::class, 'store'])->name('watchlist.store');
        Route::delete('/watchlist/{auction}', [UserWatchlistController::class, 'destroy'])->name('watchlist.destroy');

        Route::get('/wallet', [UserWalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/deposits', [UserWalletController::class, 'storeDeposit'])->name('wallet.deposits.store');
        Route::get('/wallet/deposits/stripe/success', [UserWalletController::class, 'stripeSuccess'])->name('wallet.deposits.stripe.success');
        Route::get('/wallet/deposits/stripe/cancel', [UserWalletController::class, 'stripeCancel'])->name('wallet.deposits.stripe.cancel');

        Route::get('/withdrawals', [UserWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals', [UserWithdrawalController::class, 'store'])->name('withdrawals.store');

        Route::get('/notifications', [UserNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [UserNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

        Route::get('/settings', [UserSettingsController::class, 'index'])->name('settings.index');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'toggleSuspend'])->name('users.suspend');
        Route::post('/users/{user}/multiplier', [AdminUserController::class, 'updateMultiplier'])->name('users.multiplier');

        Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
        Route::post('/deposits/{transaction}', [AdminDepositController::class, 'decide'])->name('deposits.decide');
        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawalRequest}', [AdminWithdrawalController::class, 'decide'])->name('withdrawals.decide');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/support-tickets', [AdminSupportTicketController::class, 'index'])->name('support-tickets.index');
        Route::get('/support-tickets/{supportTicket}', [AdminSupportTicketController::class, 'show'])->name('support-tickets.show');
        Route::post('/support-tickets/{supportTicket}/reply', [AdminSupportTicketController::class, 'reply'])->name('support-tickets.reply');

        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');

        Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
        Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');
        Route::post('/auctions/{auction}/approve-shipment', [AuctionController::class, 'approveShipment'])->name('auctions.approve-shipment');
        Route::post('/auctions/{auction}/reject-shipment', [AuctionController::class, 'rejectShipment'])->name('auctions.reject-shipment');
        Route::get('/proxies', [AdminProxyController::class, 'index'])->name('proxies.index');
        Route::get('/scraping-logs', [AdminScrapingLogController::class, 'index'])->name('scraping-logs.index');
        Route::resource('shipping_rates', \App\Http\Controllers\Admin\ShippingRateController::class);

        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
});
