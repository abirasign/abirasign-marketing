<?php

use App\Http\Controllers\QuoteCheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\SupportController;

Route::get('/robots.txt', function () {
    $content = app()->environment('production')
        ? "User-agent: *\nDisallow:\n\nSitemap: https://abirasign.com/sitemap.xml"
        : "User-agent: *\nDisallow: /";
    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');

// Marketing pages
Route::get('/', fn() => view('home'))->name('home');
Route::get('/pricing', fn() => view('pricing'))->name('pricing');
Route::get('/terms',           [PolicyController::class, 'terms'])->name('terms');
Route::get('/terms/archive',   [PolicyController::class, 'termsArchive'])->name('terms.archive');
Route::get('/terms/v/{version}', [PolicyController::class, 'termsVersion'])->name('terms.version');
Route::get('/privacy',           [PolicyController::class, 'privacy'])->name('privacy');
Route::get('/privacy/archive',   [PolicyController::class, 'privacyArchive'])->name('privacy.archive');
Route::get('/privacy/v/{version}', [PolicyController::class, 'privacyVersion'])->name('privacy.version');
Route::get('/features', fn() => view('features'))->name('features');
Route::get('/support', [SupportController::class, 'show'])->name('support');
Route::get('/support/kb',                              [SupportController::class, 'kb'])->name('support.kb');
Route::get('/support/kb/search',                       [SupportController::class, 'search'])->name('support.kb.search');
Route::get('/support/kb/category/{slug}',              [SupportController::class, 'category'])->name('support.kb.category');
Route::get('/support/kb/article/{slug}',               [SupportController::class, 'article'])->name('support.kb.article');
Route::get('/support/request', [SupportController::class, 'request'])->name('support.request');
Route::post('/support/request', [SupportController::class, 'submit'])->name('support.submit');
Route::get('/support/thank-you', [SupportController::class, 'thankYou'])->name('support.thankyou');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:5,60')->name('contact.submit');
Route::get('/contact/thank-you', [ContactController::class, 'thankYou'])->name('contact.thankyou');

// Signup flow
Route::get('/signup', [SignupController::class, 'show'])->name('signup');
Route::post('/signup', [SignupController::class, 'submit'])->name('signup.submit');
Route::get('/signup/success', [SignupController::class, 'success'])->name('signup.success');
Route::get('/signup/thank-you', [SignupController::class, 'thankYou'])->name('signup.thankyou');

// Stripe webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Quote checkout (enterprise)
Route::get('/quote-checkout/success',         [QuoteCheckoutController::class, 'success'])->name('quote.checkout.success');
Route::get('/quote-checkout/{token}',         [QuoteCheckoutController::class, 'checkout'])->name('quote.checkout');
