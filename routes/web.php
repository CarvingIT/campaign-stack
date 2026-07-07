<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OutboundMailAccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\NewsletterController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/mail-accounts',[OutboundMailAccountController::class, 'list'])->name('mail-accounts');
    Route::get('/account-form/{account_id}',[OutboundMailAccountController::class, 'addEditAccount']);
    Route::post('/saveaccount',[OutboundMailAccountController::class, 'save']);
    Route::get('/account/{account_id}',[OutboundMailAccountController::class, 'viewAccount']);
    Route::post('/account/delete',[OutboundMailAccountController::class, 'deleteAccount']);

    Route::get('/tags',[TagController::class, 'list'])->name('tags');
    Route::get('/tag-form/{tag_id}',[TagController::class, 'addEditTag']);
    Route::post('/savetag',[TagController::class, 'save']);
    Route::post('/tag/delete',[TagController::class, 'deleteTag']);

    Route::get('/contacts',[ContactController::class, 'list'])->name('contacts');
    Route::get('/contact-form/{contact_id}',[ContactController::class, 'addEditContact']);
    Route::post('/savecontact',[ContactController::class, 'save']);
    Route::post('/contact/delete',[ContactController::class, 'deleteContact']);

    Route::get('/campaigns',[CampaignController::class, 'list'])->name('campaigns');
    Route::get('/campaign-form/{campaign_id}',[CampaignController::class, 'addEditCampaign']);
    Route::post('/savecampaign',[CampaignController::class, 'save']);
    Route::post('/campaign/delete',[CampaignController::class, 'deleteCampaign']);

    Route::get('/newsletters',[NewsletterController::class, 'list'])->name('newsletters');
    Route::get('/newsletter-form/{newsletter_id}',[NewsletterController::class, 'addEditNewsletter']);
    Route::post('/savenewsletter',[NewsletterController::class, 'save']);
    Route::post('/newsletter/delete',[NewsletterController::class, 'deleteNewsletter']);
});
require __DIR__.'/auth.php';
