<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OutboundMailAccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/emails', [EmailController::class, 'list'])
    ->name('emails');

Route::get('/emails/data', [EmailController::class, 'data'])
    ->name('emails.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\QueueManagerController;

Route::middleware('auth')->group(function () {
    Route::get('/mail-accounts',[OutboundMailAccountController::class, 'list'])->name('mail-accounts');
    Route::get('/account-form/{account_id}',[OutboundMailAccountController::class, 'addEditAccount']);
    Route::post('/saveaccount',[OutboundMailAccountController::class, 'save']);
    Route::get('/account/{account_id}',[OutboundMailAccountController::class, 'viewAccount']);
    Route::post('/account/delete',[OutboundMailAccountController::class, 'deleteAccount']);
    Route::post('/mail-accounts/test-smtp',[OutboundMailAccountController::class, 'testConnection'])->name('mail-accounts.test-smtp');

    Route::get('/tags',[TagController::class, 'list'])->name('tags');
    Route::get('/tag-form/{tag_id}',[TagController::class, 'addEditTag']);
    Route::post('/savetag',[TagController::class, 'save']);
    Route::post('/tag/delete',[TagController::class, 'deleteTag']);

    Route::get('/contacts',[ContactController::class, 'list'])->name('contacts');
    Route::get('/contact-form/{contact_id}',[ContactController::class, 'addEditContact']);
    Route::post('/savecontact',[ContactController::class, 'save']);
    Route::get('/import-contact-form',[ContactController::class, 'importForm']);
    Route::post('/import-contacts',[ContactController::class, 'import']);
    Route::post('/contact/delete',[ContactController::class, 'deleteContact']);

    Route::get('/campaigns',[CampaignController::class, 'list'])->name('campaigns');
    Route::get('/campaign-form/{campaign_id}',[CampaignController::class, 'addEditCampaign']);
    Route::post('/savecampaign',[CampaignController::class, 'save']);
    Route::post('/campaign/delete',[CampaignController::class, 'deleteCampaign']);

    Route::get('/newsletters',[NewsletterController::class, 'list'])->name('newsletters');
    Route::get('/newsletter-form/{newsletter_id}',[NewsletterController::class, 'addEditNewsletter']);
    Route::post('/savenewsletter',[NewsletterController::class, 'save']);
    Route::post('/newsletter/delete',[NewsletterController::class, 'deleteNewsletter']);
    Route::post('/newsletter/{id}/queue-now',[QueueManagerController::class, 'queueSingle'])->name('newsletter.queue-now');

    // Interactive Dispatch Control Center & Queue Engine Routes
    Route::get('/queue/status', [QueueManagerController::class, 'status'])->name('queue.status');
    Route::post('/queue/run-all', [QueueManagerController::class, 'queueAll'])->name('queue.run-all');
    Route::post('/queue/flush-all', [QueueManagerController::class, 'flushQueue'])->name('queue.flush-all');
    Route::match(['get', 'post'], '/queue/flush-stream', [QueueManagerController::class, 'flushStream'])->name('queue.flush-stream');
    Route::post('/queue/retry-failed', [QueueManagerController::class, 'retryFailed'])->name('queue.retry-failed');
    Route::post('/queue/clear', [QueueManagerController::class, 'clearQueue'])->name('queue.clear');

    // Email Inspector Details
    Route::get('/emails/{id}/details', [EmailController::class, 'details'])->name('emails.details');

    // Dedicated Broadcast Dispatch Studio & Audience Pre-Send Workflow
    Route::get('/dispatch', [\App\Http\Controllers\DispatchStudioController::class, 'index'])->name('dispatch');
    Route::get('/dispatch/audience', [\App\Http\Controllers\DispatchStudioController::class, 'getAudience'])->name('dispatch.audience');
    Route::post('/dispatch/queue-custom', [\App\Http\Controllers\DispatchStudioController::class, 'queueCustom'])->name('dispatch.queue-custom');
    Route::post('/dispatch/queue-custom-stream', [\App\Http\Controllers\DispatchStudioController::class, 'queueCustomStream'])->name('dispatch.queue-custom-stream');
    Route::post('/dispatch/update-contact', [\App\Http\Controllers\DispatchStudioController::class, 'updateContactQuick'])->name('dispatch.update-contact');
});
require __DIR__.'/auth.php';
