<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\IPGController;
use App\Http\Controllers\Api\v1\IVRController;
use App\Http\Controllers\Api\v1\SMSController;
use App\Http\Controllers\Api\v1\RatingController;
use App\Http\Controllers\Api\v1\ApiAuthController;
use App\Http\Controllers\Api\v1\CallBackController;
use App\Http\Controllers\Api\v1\CallLogsController;
use App\Http\Controllers\Api\v1\DoctorApiController;
use App\Http\Controllers\Api\v1\WebsiteApiController;
use App\Http\Controllers\Api\v1\DeliverySMSCallbackController;

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

Route::post('afyacall/register', [ApiAuthController::class, 'registerExternalUser']);
Route::post('afyacall/login', [ApiAuthController::class, 'loginExternalUser']);
//api for ICG and Mpesa Charges
Route::middleware('api')->post('afyacall/requests-callbacks', [CallBackController::class, 'callbacksrequests']);
Route::middleware('api')->post('afyacall/TransactionListener2', [IPGController::class, 'callbacksrequests']);
Route::middleware('api')->post('afyacall/transactionRequest', [IPGController::class, 'transactionRequest']);
Route::middleware('api')->get('afyacall/doctorstatus', [IPGController::class, 'subscriptiondoctorstatus']);
Route::middleware('api')->post('afyacall/livecalldoctorstastics', [CallLogsController::class, 'livecalldoctorstastics']);

Route::post('sms/receivedsms', [SMSController::class, 'receivedsmsfromkannel']);
Route::get('sms/deliveryreport', [DeliverySMSCallbackController::class, 'deliveryreport']);
Route::get('sms/dailydeliveryreport', [DeliverySMSCallbackController::class, 'dailydeliveryreport']);
Route::get('sms/notifysms', [DeliverySMSCallbackController::class, 'notifysms']);
Route::post('ivr/ivrsavestatistics', [CallLogsController::class, 'ivrsavestatistics']);
Route::post('ivr/unsubscribeafterlisten', [CallLogsController::class, 'unsubscribeafterlisten']);
Route::get('/ivr/language', [CallLogsController::class, 'language']);
Route::get('/ivr/subscription-status', [IVRController::class, 'subscriptionstatus']);
Route::post('/ivr/airtime-mpesa-charge', [IVRController::class, 'chargeMpesaAirtimeIvr']);
Route::post('ivr/deactivate', [IVRController::class, 'deactivate']);
Route::post('ivr/chargempesa', [IVRController::class, 'chargempesa']);
Route::get('ivr/sendsms', [IVRController::class, 'sendsms']);
Route::post('ivr/acceptRequestFromPBX', [IVRController::class, 'acceptRequestFromPBX']);
Route::middleware('api')->post('afyacall/campaign/obd', [DeliverySMSCallbackController::class, 'deliveryobd']);

//customer experience and rating
Route::middleware('api')->post('afyacall/customerrating', [RatingController::class, 'rating']);

//charge doctor bundle using Airtime
Route::post('/ivr/airtime-doctor-charge', [DoctorApiController::class, 'chargeDoctorAirtime']);

//charge doctor subscription
Route::post('doctor/subscription/charge', [DoctorApiController::class, 'chargedoctorrequestfrompbx']);
Route::post('doctor/subscription/removeseconds', [DoctorApiController::class, 'removeseconds']);
Route::get('doctor/subscription/status', [DoctorApiController::class, 'doctorsubscriptionstatus']);

//afyacall promotions
Route::get('promotions/status', [IVRController::class, 'promotionstatus']);

//afyacall api for website
Route::middleware('auth:api')->group(function () {
    // our routes to be protected will go in here
    Route::post('/sms/afyacall/enticement', [WebsiteApiController::class, 'websiteEnticement']);
});
