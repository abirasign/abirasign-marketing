<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\ContactController;


Route::get('/robots.txt', function () {
    $content = app()->environment('production')
        ? "User-agent: *\nDisallow:\n\nSitemap: https://abirasign.com/sitemap.xml"
        : "User-agent: *\nDisallow: /";
    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');

// Marketing pages
Route::get('/', fn() => view('home'))->name('home');
Route::get('/pricing', fn() => view('pricing'))->name('pricing');
Route::get('/terms', fn() => view('legal.terms'))->name('terms');
Route::get('/privacy', fn() => view('legal.privacy'))->name('privacy');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/contact/thank-you', [ContactController::class, 'thankYou'])->name('contact.thankyou');

// Signup flow
// Signup flow
Route::get('/signup', [SignupController::class, 'show'])->name('signup');
Route::post('/signup', [SignupController::class, 'submit'])->name('signup.submit');
Route::get('/signup/success', [SignupController::class, 'success'])->name('signup.success');
Route::get('/signup/thank-you', [SignupController::class, 'thankYou'])->name('signup.thankyou');
