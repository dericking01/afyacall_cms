<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorAuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');
Route::redirect('/home', '/two-factor-auth');
Auth::routes(['register' => false]);
Route::get('two-factor-auth', [TwoFactorAuthController::class, 'index'])->name('2fa.index');
Route::post('two-factor-auth', [TwoFactorAuthController::class, 'store'])->name('2fa.store');
Route::get('two-factor-auth/resent', [TwoFactorAuthController::class, 'resend'])->name('2fa.resend');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/dashboard', 'HomeController@index')->name('home');
    //subscription controllers
    Route::resource('subscriptions', 'SubscriptionController');
    //afyacall products controller
    Route::resource('products', 'ProductController');
    //afyacall customers
    Route::resource('customers', 'CustomerController');
      Route::post('customers/search', 'CustomerController@search')->name('customer-search');
    //afyacall Transaction
    Route::resource('transactions', 'TransactionController');
    Route::get('doctortranscations', 'TransactionController@doctortranscations')->name('transactions.doctors');
    Route::resource('invoice','InvoiceController');
    //afyacall Content Management
    Route::resource('contents', 'ContentController');
    Route::post('content/import', 'ContentController@import')->name('content-import');
    Route::resource('contentstype', 'ContentTypeController');
    Route::get('contenttype/list','ContentTypeController@getList')->name('contenttype_list');

    //Afyacall Enticement for ICG Products
    Route::resource('enticement', 'EnticementController');
    Route::post('contacts/import', 'EnticementController@import')->name('contant-import');
    Route::get('contacts/enticeallcontacts', 'EnticementController@enticeallcontacts')->name('contant-enticeallcontacts');

    //Afyacall Blacklist number (DND)
    Route::resource('blacklist','BlacklistController');
    Route::post('blacklist/import', 'BlacklistController@import')->name('blacklist-import');

    Route::post('blacklist/search', 'BlacklistController@search')->name('blacklist-search');
    //afyacall Tickets Controller
    Route::resource('ticket','TicketController');
    Route::post('/ticketclose','TicketController@close')->name('ticketclose');
    Route::get('ticket/open/{id}','TicketController@open')->name('ticket.open');
    Route::post('ticket/updatestatus','TicketController@updatestatus')->name('updatestatus');

    //afyacall Backup Controller
    Route::resource('backup','BackupController');
    Route::get('/backup/download/{file_name}', 'BackupController@download')->name('backup.download');

    //afyacall users access
    Route::resource('users', 'UserController');
    Route::get('change-password', 'UserController@changepassword')->name('changepassword');
    Route::get('AccountSetting', 'UserController@setting')->name('setting');
    Route::post('change-password', 'UserController@updatepassword')->name('change.password');
    Route::post('change-user-password', 'UserController@adminchangepassword')->name('resetnewpassword');
    Route::resource('roles', 'RoleController');

    //graphs api
    Route::get('graphs/smschartdata', 'HomeController@getSmsChartData')->name('graphs_smschartsdata');
    Route::get('graphs/subscriptiondata', 'HomeController@getSubscriptionData')->name('graphs_subscriptiondata');

    //subscribe customer
    Route::post('/blacklistcustomer','BlacklistController@blacklistcustomer')->name('blacklistcustomer');
    Route::post('/subscribecustomer','CustomerController@subscribecustomer')->name('subscribecustomer');
    Route::get('/checkbalance','CustomerController@checkbalance')->name('checkbalance');


    //general for sms 
    Route::get('/report/sms/','ReportController@sms')->name('report.sms');
    Route::post('/report/daterange/sms/','ReportController@daterange')->name('daterange.sms');
    //general for ivr
    Route::get('/report/ivr/','ReportController@ivr')->name('report.ivr');
    Route::post('/report/daterange/ivr/','ReportController@daterangeforivr')->name('daterange.ivr');
    
 
    //Number search
    Route::get('/report/NumberSearch','ReportController@numbersearch')->name('report_numbersearch');
    Route::get('/report/NumberSearch/results','ReportController@findnumber')->name('report_numbersearch_results');

    //customer reports
     Route::get('/reports/customer','CustomerReports@index')->name('customer.report');
     Route::post('/reports/customer/show','CustomerReports@customerList')->name('customer_list');
    Route::get('/reports/Monthly-revenue-views','ReportController@monthlyrevenueReport')->name('monthlyrevenueview');
     //blacklist reports
     Route::get('/reports/blacklist','CustomerReports@blacklist')->name('blacklist');
     Route::post('/reports/blacklist/search','CustomerReports@blacklistSearch')->name('blacklistSearch');
     
     //ticketing reports
     Route::get('/reports/ticket','CustomerReports@ticketreport')->name('ticketreport');
     Route::post('/reports/ticket/search','CustomerReports@ticketingSearch')->name('ticketingSearch');

     //content reports
     Route::get('/reports/contents-views','ReportController@contentview')->name('contentview');

     //mpesa analytics
     Route::get('/reports/mpesa-analytics','TransactionController@mpesa_analytics')->name('mpesa_analytics');
     Route::get('/reports/get_mpesa-analytics','TransactionController@get_mpesa_analytics')->name('get_mpesa_analytics');
     

     //revenue reports
     Route::get('/reports/revenue-views','ReportController@revenueview')->name('revenueview');
     Route::post('/reports/revenue/search','ReportController@revenueSearch')->name('revenueSearch');
     

     //message reports
     Route::get('/reports/message-sents-views','ReportController@messagereportview')->name('messagereportview');
     Route::post('/reports/message/search','ReportController@messagereports')->name('messagereports');
     
     
     //billing invoice
     Route::get('/billing/details','BillingController@index')->name('billing');
     Route::post('/billing/updates','BillingController@update')->name('billing.update');

     //contacts upload
     Route::get('/contact/upload','ContactController@create')->name('contact.upload');
     Route::post('contact/upload-file','ContactController@fileUpload')->name('fileUpload');

     //compaing 
     Route::get('/contact/campaign','ContactController@index')->name('contact.campaign');
     Route::get('/contact/compaingservice','ContactController@compaingservice')->name('contact.compaingservice');
     Route::post('/contact/pushcompaignservice','ContactController@pushcompaignservice')->name('pushcompaignservice');
     Route::get('/contact/showcontacts/{id}','ContactController@showcontact')->name('contact.showcontacts');
     Route::get('/contact/campaigndetails/{id}','ContactController@campaigndetails')->name('contact.campaigndetails');

     //groups
     Route::get('/contact/groups','ContactController@groups')->name('contact.groups');
     Route::post('/contact/group/store','ContactController@groups_store')->name('contact.group.store');
     Route::get('/contact/groups/import/{id}','ContactController@groupImport')->name('contact.groups.import');
     Route::post('/contact/group/store/contacts','ContactController@importContactGroup')->name('contact.group.store.contacts');
 
     //delete contact in a group
     Route::delete('/contact/group/contacts/destroy{id}', 'ContactController@destroyContact')->name('contact.group.contacts.destroy');

     //delete group with all contacts
     Route::delete('/contact/group/destroy{id}', 'ContactController@destroyGroup')->name('contact.group.destroy');
          //outbound Campagin
     Route::post('/contact/outboundcampaing/store','OutBoundCampaign@store')->name('contact.outboundcampaing.store');
     Route::get('/contact/outboundcampaing/create','OutBoundCampaign@create')->name('contact.outboundcampaing.create');
     Route::get('/contact/outboundcampaing','OutBoundCampaign@index')->name('contact.outboundcampaing');
      Route::get('/contact/outboundcampaing/show/{id}','OutBoundCampaign@show')->name('contact.outboundcampaign.show');


      //promotions
      Route::resource('promotions', 'PromotionController');
      Route::post('promotions/import', 'PromotionController@import')->name('promotion-import');

});
