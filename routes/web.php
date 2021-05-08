<?php

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('su-panel', 'AuthorizationController@suPanel');
Route::get('adm-panel', 'AuthorizationController@admPanel');
Route::get('cs-panel', 'AuthorizationController@csPanel');

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('suadmin')->name('suadmin.')->group(function (){
    Route::get('/', 'AdminDashboardController@index')->name('index');

    Route::namespace('member')->prefix('member')->name('member.')->group(function () {
        Route::get('/', 'MemberDataController@index')->name('index');
        Route::get('edit/{id}', 'MemberDataController@edit')->name('edit');
        Route::get('view/{id}', 'MemberDataController@view')->name('view');
        Route::get('checkin', 'MemberCheckinController@index')->name('checkin');

        Route::namespace('registration')->prefix('registration')->name('registration.')->group(function () {
            Route::get('/', 'MemberRegisterController@index')->name('index');
            Route::get('complete', 'MemberRegisterController@complete')->name('complete');
        });
    });
    Route::namespace('membership')->prefix('membership')->name('membership.')->group(function () {
        Route::get('/', 'MembershipDataController@index')->name('index');
    });
    Route::namespace('pt')->prefix('pt')->name('pt.')->group(function () {
        Route::get('/', 'PTDataController@index')->name('index');
    });
    Route::namespace('marketing')->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/', 'MarketingDataController@index')->name('index');
    });
    Route::namespace('cuti')->prefix('cuti')->name('cuti.')->group(function () {
        Route::get('/', 'CutiController@index')->name('index');
    });
});

Route::prefix('cs')->name('cs.')->group(function (){
    Route::get('/', 'CSDashboardController@index')->name('index');

    Route::namespace('member')->prefix('member')->name('member.')->group(function () {
        Route::get('/', 'MemberDataController@index')->name('index');
        Route::get('edit/{id}', 'MemberDataController@edit')->name('edit');
        Route::get('view/{id}', 'MemberDataController@view')->name('view');
        Route::get('checkin', 'MemberCheckinController@index')->name('checkin');

        Route::namespace('registration')->prefix('registration')->name('registration.')->group(function () {
            Route::get('/', 'MemberRegisterController@index')->name('index');
            Route::get('complete', 'MemberRegisterController@complete')->name('complete');
        });
    });

    Route::namespace('cuti')->prefix('cuti')->name('cuti.')->group(function () {
        Route::get('/', 'CutiController@index')->name('index');
    });
});

Route::namespace('member')->prefix('member')->name('member.')->group(function () {
    Route::get('getMemberData', 'MemberDataController@getMemberData')->name('getMemberData');
    Route::get('requestModalAction', 'MemberDataController@requestModalAction')->name('requestModalAction');
    Route::post('update', 'MemberDataController@update')->name('update');
    Route::get('edit/{id}/getMemberMembership', 'MemberDataController@getMemberMembership')->name('getMemberMembership');
    Route::get('edit/{id}/getMemberPT', 'MemberDataController@getMemberPT')->name('getMemberPT');
    Route::get('edit/{id}/getMemberLog', 'MemberDataController@getMemberLog')->name('getMemberLog');
    Route::get('print/{id}', 'MemberDataController@print')->name('print');
    Route::get('print/registrasi/{id}', 'MemberDataController@printRegister')->name('printRegister');
    Route::post('aktivasi', 'MemberDataController@aktivasi')->name('aktivasi');
    Route::get('preview', 'MemberCheckinController@preview')->name('preview');
    Route::post('checkin', 'MemberCheckinController@checkin')->name('checkin');

    Route::post('addTransaction', 'MemberDataController@addTransaction')->name('addTransaction');

    Route::get('dataChecking', 'MemberDataController@dataChecking')->name('dataChecking');
    Route::get('printPembelianSesi/{id}', 'MemberDataController@printPembelianSesi')->name('printPembelianSesi');

    Route::post('changePT', 'MemberDataController@changePT')->name('changePT');

    Route::namespace('registration')->prefix('registration')->name('registration.')->group(function () {
        Route::post('store', 'MemberRegisterController@store')->name('store');
        Route::get('print/{id}', 'MemberRegisterController@print')->name('print');
    });
});

Route::namespace('membership')->prefix('membership')->name('membership.')->group(function () {
    Route::get('getMembershipData', 'MembershipDataController@getMembershipData')->name('getMembershipData');
    Route::post('store', 'MembershipDataController@store')->name('store');
    Route::get('edit', 'MembershipDataController@edit')->name('edit');
    Route::post('update', 'MembershipDataController@update')->name('update');
    Route::post('destroy', 'MembershipDataController@destroy')->name('destroy');
});

Route::namespace('pt')->prefix('pt')->name('pt.')->group(function () {
    Route::get('getPTData', 'PTDataController@getPTData')->name('getPTData');
    Route::post('store', 'PTDataController@store')->name('store');
    Route::get('edit', 'PTDataController@edit')->name('edit');
    Route::post('update', 'PTDataController@update')->name('update');
    Route::post('destroy', 'PTDataController@destroy')->name('destroy');
});

Route::namespace('marketing')->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('getMarketingData', 'MarketingDataController@getMarketingData')->name('getMarketingData');
    Route::post('store', 'MarketingDataController@store')->name('store');
    Route::get('edit', 'MarketingDataController@edit')->name('edit');
    Route::post('update', 'MarketingDataController@update')->name('update');
    Route::post('destroy', 'MarketingDataController@destroy')->name('destroy');
});

Route::namespace('cuti')->prefix('cuti')->name('cuti.')->group(function () {
    Route::get('getCutiData', 'CutiController@getCutiData')->name('getCutiData');
    Route::get('preview', 'CutiController@preview')->name('preview');
    Route::get('checkCapability', 'CutiController@checkCapability')->name('checkCapability');
    Route::get('abortCuti', 'CutiController@abortCuti')->name('abortCuti');
    Route::post('approve', 'CutiController@approve')->name('approve');
    Route::post('remove', 'CutiController@remove')->name('remove');
});
