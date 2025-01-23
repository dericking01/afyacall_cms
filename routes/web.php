<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ContentTypeController;
use App\Http\Controllers\Admin\EnticementController;
use App\Http\Controllers\Admin\BlacklistController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerReports;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\OutBoundCampaign;
use App\Http\Controllers\Admin\PromotionController;
use Illuminate\Support\Facades\Redis;



Route::redirect('/', '/login');
Route::redirect('/home', '/two-factor-auth');
Auth::routes(['register' => false]);

Route::get('two-factor-auth', [TwoFactorAuthController::class, 'index'])->name('2fa.index');
Route::post('two-factor-auth', [TwoFactorAuthController::class, 'store'])->name('2fa.store');
Route::get('two-factor-auth/resent', [TwoFactorAuthController::class, 'resend'])->name('2fa.resend');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

    // Subscription controllers
    Route::resource('subscriptions', SubscriptionController::class);
    
    // Afyacall products controller
    Route::resource('products', ProductController::class);
    
    // Afyacall customers
    Route::resource('customers', CustomerController::class);
    Route::post('customers/search', [CustomerController::class, 'search'])->name('customer-search');
    
    // Afyacall transactions
    Route::resource('transactions', TransactionController::class);
    Route::get('doctortranscations', [TransactionController::class, 'doctortranscations'])->name('transactions.doctors');
    
    // Afyacall invoice
    Route::resource('invoice', InvoiceController::class);
    
    // Afyacall content management
    Route::resource('contents', ContentController::class);
    Route::post('content/import', [ContentController::class, 'import'])->name('content-import');
    Route::resource('contentstype', ContentTypeController::class);
    Route::get('contenttype/list', [ContentTypeController::class, 'getList'])->name('contenttype_list');

    // Afyacall enticement for ICG products
    Route::resource('enticement', EnticementController::class);
    Route::post('contacts/import', [EnticementController::class, 'import'])->name('contant-import');
    Route::get('contacts/enticeallcontacts', [EnticementController::class, 'enticeallcontacts'])->name('contant-enticeallcontacts');

    // Afyacall blacklist number (DND)
    Route::resource('blacklist', BlacklistController::class);
    Route::post('blacklist/import', [BlacklistController::class, 'import'])->name('blacklist-import');
    Route::post('blacklist/search', [BlacklistController::class, 'search'])->name('blacklist-search');

    // Afyacall tickets controller
    Route::resource('ticket', TicketController::class);
    Route::post('/ticketclose', [TicketController::class, 'close'])->name('ticketclose');
    Route::get('ticket/open/{id}', [TicketController::class, 'open'])->name('ticket.open');
    Route::post('ticket/updatestatus', [TicketController::class, 'updatestatus'])->name('updatestatus');

    // Afyacall backup controller
    Route::resource('backup', BackupController::class);
    Route::get('/backup/download/{file_name}', [BackupController::class, 'download'])->name('backup.download');

    // Afyacall users access
    Route::resource('users', UserController::class);
    Route::get('change-password', [UserController::class, 'changepassword'])->name('changepassword');
    Route::get('AccountSetting', [UserController::class, 'setting'])->name('setting');
    Route::post('change-password', [UserController::class, 'updatepassword'])->name('change.password');
    Route::post('change-user-password', [UserController::class, 'adminchangepassword'])->name('resetnewpassword');
    Route::resource('roles', RoleController::class);

    // Graphs API
    Route::get('graphs/smschartdata', [HomeController::class, 'getSmsChartData'])->name('graphs_smschartsdata');
    Route::get('graphs/subscriptiondata', [HomeController::class, 'getSubscriptionData'])->name('graphs_subscriptiondata');

    // Subscribe customer
    Route::post('/blacklistcustomer', [BlacklistController::class, 'blacklistcustomer'])->name('blacklistcustomer');
    Route::post('/subscribecustomer', [CustomerController::class, 'subscribecustomer'])->name('subscribecustomer');

    // General for SMS
    Route::get('/report/sms/', [ReportController::class, 'sms'])->name('report.sms');
    Route::post('/report/daterange/sms/', [ReportController::class, 'daterange'])->name('daterange.sms');

    // General for IVR
    Route::get('/report/ivr/', [ReportController::class, 'ivr'])->name('report.ivr');
    Route::post('/report/daterange/ivr/', [ReportController::class, 'daterangeforivr'])->name('daterange.ivr');

    // Number search
    Route::get('/report/NumberSearch', [ReportController::class, 'numbersearch'])->name('report_numbersearch');
    Route::get('/report/NumberSearch/results', [ReportController::class, 'findnumber'])->name('report_numbersearch_results');

    // Customer reports
    Route::get('/reports/customer', [CustomerReports::class, 'index'])->name('customer.report');
    Route::post('/reports/customer/show', [CustomerReports::class, 'customerList'])->name('customer_list');
    Route::get('/reports/Monthly-revenue-views', [ReportController::class, 'monthlyrevenueReport'])->name('monthlyrevenueview');

    // Blacklist reports
    Route::get('/reports/blacklist', [CustomerReports::class, 'blacklist'])->name('blacklist');
    Route::post('/reports/blacklist/search', [CustomerReports::class, 'blacklistSearch'])->name('blacklistSearch');

    // Ticketing reports
    Route::get('/reports/ticket', [CustomerReports::class, 'ticketreport'])->name('ticketreport');
    Route::post('/reports/ticket/search', [CustomerReports::class, 'ticketingSearch'])->name('ticketingSearch');

    // Content reports
    Route::get('/reports/contents-views', [ReportController::class, 'contentview'])->name('contentview');

    // Mpesa analytics
    Route::get('/reports/mpesa-analytics', [TransactionController::class, 'mpesa_analytics'])->name('mpesa_analytics');
    Route::get('/reports/get_mpesa-analytics', [TransactionController::class, 'get_mpesa_analytics'])->name('get_mpesa_analytics');

    // Revenue reports
    Route::get('/reports/revenue-views', [ReportController::class, 'revenueview'])->name('revenueview');
    Route::post('/reports/revenue/search', [ReportController::class, 'revenueSearch'])->name('revenueSearch');

    // Message reports
    Route::get('/reports/message-sents-views', [ReportController::class, 'messagereportview'])->name('messagereportview');
    Route::post('/reports/message/search', [ReportController::class, 'messagereports'])->name('messagereports');

    // Billing invoice
    Route::get('/billing/details', [BillingController::class, 'index'])->name('billing');
    Route::post('/billing/updates', [BillingController::class, 'update'])->name('billing.update');

    // Contacts upload
    Route::get('/contact/upload', [ContactController::class, 'create'])->name('contact.upload');
    Route::post('contact/upload-file', [ContactController::class, 'fileUpload'])->name('fileUpload');

    // Campaign
    Route::get('/contact/campaign', [ContactController::class, 'index'])->name('contact.campaign');
    Route::get('/contact/compaingservice', [ContactController::class, 'compaingservice'])->name('contact.compaingservice');
    Route::post('/contact/pushcompaignservice', [ContactController::class, 'pushcompaignservice'])->name('pushcompaignservice');
    Route::get('/contact/showcontacts/{id}', [ContactController::class, 'showcontact'])->name('contact.showcontacts');
    Route::get('/contact/campaigndetails/{id}', [ContactController::class, 'campaigndetails'])->name('contact.campaigndetails');

    // Groups
    Route::get('/contact/groups', [ContactController::class, 'groups'])->name('contact.groups');
    Route::get('/contact/groupsdata/{id}', [ContactController::class, 'groupsdata'])->name('contact.groupsdata');
    Route::get('/contact/enticegroups/{id}', [ContactController::class, 'enticegroups'])->name('contact.enticegroups');
    Route::post('/contact/enticegroupsprocess', [ContactController::class, 'enticegroupsprocess'])->name('contact.enticegroupsprocess');
    Route::get('/contact/groupsdetails/{id}', [ContactController::class, 'groupsdetails'])->name('contact.groupsdetails');
    Route::post('/contact/groupsupdates', [ContactController::class, 'groupsupdates'])->name('contact.groupsupdates');
    Route::post('/contact/group/store',[ContactController::class, 'groups_store'])->name('contact.group.store');
    Route::get('/contact/groups/import/{id}',[ContactController::class,'groupImport'])->name('contact.groups.import');
    Route::post('/contact/group/store/contacts',[ContactController::class, 'importContactGroup'])->name('contact.group.store.contacts');



    //delete contact in a group
    Route::delete('/contact/group/contacts/destroy{id}', [ContactController::class,'destroyContact'])->name('contact.group.contacts.destroy');

    //delete group with all contacts
    Route::delete('/contact/group/destroy{id}', [ContactController::class,'destroyGroup'])->name('contact.group.destroy');


    // Campaigns list
    // Outbound Campaign Routes
    Route::post('/contact/outboundcampaing/store', [OutBoundCampaign::class, 'store'])->name('contact.outboundcampaing.store');
    Route::get('/contact/outboundcampaing/create', [OutBoundCampaign::class, 'create'])->name('contact.outboundcampaing.create');
    Route::get('/contact/outboundcampaing', [OutBoundCampaign::class, 'index'])->name('contact.outboundcampaing');
    Route::get('/contact/outboundcampaing/show/{id}', [OutBoundCampaign::class, 'show'])->name('contact.outboundcampaign.show');

    Route::get('/contact/reportscampaign/{id}', [ContactController::class, 'reportscampaign'])->name('contact.reportscampaign');
    Route::get('/contact/reportsgroups/{id}', [ContactController::class, 'reportsgroups'])->name('contact.reportsgroups');

   // Promotions
    Route::resource('promotions', PromotionController::class);
    Route::post('promotions/import', [PromotionController::class, 'import'])->name('promotion-import');

    

});

Route::get('/error', function () {
    abort(500);
});


Route::get('/test-redis', function () {
    Redis::set('test_key', 'test_value');
    return Redis::get('test_key');
});
