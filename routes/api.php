<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlvckboxController;
use App\Http\Controllers\BlvckcardsController;
use App\Http\Controllers\ContentcardsController as ControllersContentcardsController;
use App\Http\Controllers\dashboard\AuthConteroller;
use App\Http\Controllers\dashboard\BlvckboxController as DashboardBlvckboxController;
use App\Http\Controllers\dashboard\BlvckcardsController as DashboardBlvckcardsController;
use App\Http\Controllers\dashboard\ConclusionController;
use App\Http\Controllers\dashboard\ContentcardsController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\dashboard\SubscriptionsController;
use App\Http\Controllers\dashboard\UserManagementController;
use App\Http\Controllers\dashboard\EditorialController;
use App\Http\Controllers\LogAccessController;
use App\Http\Controllers\SubscriptionsController as ControllersSubscriptionsController;
use App\Models\Contentcard;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\MailchimpController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\EmailTempController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\PayingController;
use App\Http\Controllers\AdminSubscriptionController;






/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Added By Brobot Email thing

// Ended Here

Route::middleware('auth.api')->group(function () {
    Route::get('/track', [LogAccessController::class, 'logAccess']);
    Route::get('/track/stats', [LogAccessController::class, 'index']);
    Route::get('/track/revenue-stats', [LogAccessController::class, 'revenueStats']);

    Route::post('/user/payment-method', [UserController::class, 'updatePaymentMethod']);
    Route::controller(SubscriptionController::class)->group(function () {
        Route::post('/subscribe', 'subscribe');
        Route::post('/subscription/change', 'changeSubscription');
        Route::post('/subscription/cancel', 'cancelSubscription');
    });
    // Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    // Route::post('/subscription/change', [SubscriptionController::class, 'changeSubscription']);
    // Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription']);
});


// Route::middleware('auth:api')->group(function () {
//     Route::post('/user/payment-method', [UserController::class, 'updatePaymentMethod']);
// });

// Features Added

// routes/api.php

Route::prefix('features')->group(function () {
    Route::get('/', [FeatureController::class, 'index']);
    Route::post('/', [FeatureController::class, 'store']);
    Route::get('/{feature}', [FeatureController::class, 'show']);
    Route::put('/{feature}', [FeatureController::class, 'update']);
    Route::delete('/{feature}', [FeatureController::class, 'destroy']);
});

// Ended



// subs started

// Route::middleware(['auth:api'])->group(function () {
    
// });

Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook']);

// subs ended



// Website
Route::post('/send-custom-email', [EmailController::class, 'sendCustomEmail']);
Route::post('/send-activation-email', [EmailController::class, 'sendAuthenticationEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/activate-account/{token}', [AuthController::class, 'activateAccount']);
Route::get('/send-test-email', [AuthController::class, 'sendTestEmail']);
// Route::post('/sendemails', [EmailController::class, 'sendcustomemail']);
Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/validate-reset-token', [AuthController::class, 'validateResetToken']);
Route::post('/change-password', [AuthController::class, 'changePassword']);

// Custome Email
// Route::post('/send-custom-email', [EmailController::class, 'sendcustomemail']);


Route::prefix('email-templates')->group(function () {
    Route::get('/', [EmailTemplateController::class, 'index']); // Get all templates
    Route::get('/{id}', [EmailTemplateController::class, 'show']); // Get a single template by ID
    Route::post('/', [EmailTemplateController::class, 'store']); // Create a new template
    Route::put('/{id}', [EmailTemplateController::class, 'update']); // Update a template
    Route::delete('/{id}', [EmailTemplateController::class, 'destroy']); // Delete a template
});


// demo payment crud
Route::prefix('user/{userId}/payments')->group(function () {
    Route::get('/', [PayingController::class, 'index']);
    Route::get('{id}', [PayingController::class, 'show']);
    Route::post('/', [PayingController::class, 'store']);
    Route::put('{id}', [PayingController::class, 'update']);
    Route::delete('{id}', [PayingController::class, 'destroy']);
});


Route::post('/payment', [PaymentController::class, 'createPayment']);

Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/getuserdata', [AuthController::class, 'getUserData']);
    Route::get('/user', [AuthController::class, 'updateProfile']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::get('/user/role', [AuthController::class, 'getUserRole']);
    Route::post('/update-package', [AuthController::class, 'updatePackage']);
    Route::delete('/user/{id}', [AuthController::class, 'deleteUser']);

});


// email templates
Route::apiResource('/temp/email', EmailTempController::class);

Route::middleware('auth.api')->group(function () {
    // Route::get('/blvckbox', [BlvckboxController::class, 'index']);
    // Route::get('/blvckbox/{box}', [BlvckcardsController::class, 'index']);
});

Route::get('/blvckboxes', [BlvckboxController::class, 'index']);
Route::post('/blvckboxes', [BlvckboxController::class, 'store']);
Route::get('/contentcards/{slug}', [ControllersContentcardsController::class, 'index']);
Route::get('/blvckcards/{slug}', [BlvckcardsController::class, 'index']);
Route::get('blvckcards/show/{slug}', [BlvckcardsController::class, 'show']);
Route::put('/blvckboxes/{id}', [BlvckboxController::class, 'update']);
Route::delete('/blvckboxes/{id}', [BlvckboxController::class, 'destroy']);
Route::get('/packages', [ControllersSubscriptionsController::class, 'index']);



Route::middleware('auth.api')->group(function () {
    Route::post('dashboard/createModerator', [AuthConteroller::class, 'createModerator']);
    Route::get('/dashboard/users', [UserManagementController::class, 'index']);
    Route::put('/dashboard/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/dashboard/users/{id}', [UserManagementController::class, 'destroy']);

    Route::get('dashboard/packages', [SubscriptionsController::class, 'index']);
    Route::post('dashboard/packages', [SubscriptionsController::class, 'store']);
    Route::get('dashboard/packages/{id}', [SubscriptionsController::class, 'show']);
    Route::put('dashboard/packages/{id}', [SubscriptionsController::class, 'update']);
    Route::delete('dashboard/packages/{id}', [SubscriptionsController::class, 'destroy']);

    Route::get('/dashboard/blvckboxes', [DashboardBlvckboxController::class, 'index'])->name('blvckboxes.index');
    Route::post('/dashboard/blvckboxes', [DashboardBlvckboxController::class, 'store'])->name('blvckboxes.store');
    Route::get('/dashboard/blvckboxes/{id}', [DashboardBlvckboxController::class, 'show'])->name('blvckboxes.show');
    Route::post('/dashboard/blvckboxes/{id}', [DashboardBlvckboxController::class, 'update'])->name('blvckboxes.update');
    Route::delete('/dashboard/blvckboxes/{id}', [DashboardBlvckboxController::class, 'destroy'])->name('blvckboxes.destroy');

    Route::get('/dashboard/blvckcards', [DashboardBlvckcardsController::class, 'index'])->name('blvckcards.index');
    Route::post('/dashboard/blvckcards', [DashboardBlvckcardsController::class, 'store'])->name('blvckcards.store');
    Route::get('/dashboard/blvckcards/{id}', [DashboardBlvckcardsController::class, 'show'])->name('blvckcards.show');
    Route::post('/dashboard/blvckcards/{id}', [DashboardBlvckcardsController::class, 'update'])->name('blvckcards.update');
    Route::delete('/dashboard/blvckcards/{id}', [DashboardBlvckcardsController::class, 'destroy'])->name('blvckcards.destroy');
    Route::delete('/dashboard/blvckcards/images/{id}', [DashboardBlvckcardsController::class, 'deleteImage']);

    Route::get('/dashboard/contentcards', [ContentcardsController::class, 'index']);
    Route::get('/dashboard/contentcards/{id}', [ContentcardsController::class, 'show']);
    Route::post('/dashboard/contentcards', [ContentcardsController::class, 'store']);
    Route::post('/dashboard/contentcards/{id}', [ContentcardsController::class, 'update']);
    Route::delete('/dashboard/contentcards/{id}', [ContentcardsController::class, 'destroy']);

    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    Route::get('/dashboard/blvckbox/{slug}/editorial', [EditorialController::class, 'getEditorial']);
    Route::post('/dashboard/blvckbox/{slug}/editorial', [EditorialController::class, 'storeOrUpdate']);

    Route::get('/dashboard/blvckbox/{slug}/conclusion', [ConclusionController::class, 'getEditorial']);
    Route::post('/dashboard/blvckbox/{slug}/conclusion', [ConclusionController::class, 'storeOrUpdate']);
});


// webhook endpoints
Route::group(['prefix' => 'webhooks'], function () {
    Route::group(['prefix' => 'stripe'], function () {
        Route::post('/', [WebhookController::class, 'handleStripeWebhook']);
        Route::post('/create', [WebhookController::class, 'handleCreateStripeWebhookEndpoint']);
        Route::get('/list', [WebhookController::class, 'listStripeWebhookEndpoints']);
        Route::delete('/delete/{id}', [WebhookController::class, 'deleteStripeWebhookEndpoint']);
    });
    Route::get('/test', [WebhookController::class, 'test']);
});

// mailchimp
Route::group(['prefix' => 'mailchimp'], function () {
    Route::post('/audience', [MailchimpController::class, 'createMailchimpAudience']);
    Route::post('/audience/add-member', [MailchimpController::class, 'addMemberToMailchimpAudience']);
    Route::get('/audience/members', [MailchimpController::class, 'listMailchimpAudienceMembers']);
});

Route::prefix('admin/subscriptions')->group(function () {
    Route::post('/', [AdminSubscriptionController::class, 'createSub']);
    Route::get('/{id}', [AdminSubscriptionController::class, 'show']);
    Route::put('/{id}', [AdminSubscriptionController::class, 'update']);
    Route::delete('/{id}', [AdminSubscriptionController::class, 'destroy']);
});

Route::post('/subscription/payment', [PaymentController::class, 'createSubscriptionPayment']);
