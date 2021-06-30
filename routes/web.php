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
    Route::get('getMemberData', 'AdminDashboardController@getMemberData')->name('getMemberData');

    Route::namespace('management')->prefix('management')->name('management.')->group(function () {
        Route::get('users/', 'UserManagementController@index')->name('index');
        Route::get('userManagementData', 'UserManagementController@userManagementData')->name('userManagementData');
        Route::get('getUserData', 'UserManagementController@getUserData')->name('getUserData');
        Route::get('checkIsUserAvailable', 'UserManagementController@checkIsUserAvailable')->name('checkIsUserAvailable');
        Route::post('addUser', 'UserManagementController@addUser')->name('addUser');
        Route::post('editUser', 'UserManagementController@editUser')->name('editUser');
        Route::post('deleteUser', 'UserManagementController@deleteUser')->name('deleteUser');

        Route::get('account/', 'UserDataController@account')->name('account');
    });

    Route::namespace('member')->prefix('member')->name('member.')->group(function () {
        Route::get('/', 'MemberDataController@index')->name('index');
        Route::get('edit/{id}', 'MemberDataController@edit')->name('edit');
        Route::get('view/{id}', 'MemberDataController@view')->name('view');
        Route::get('checkin', 'MemberCheckinController@index')->name('checkin');
        Route::get('checkout', 'MemberCheckoutController@index')->name('checkout');

        Route::namespace('registration')->prefix('registration')->name('registration.')->group(function () {
            Route::get('/', 'MemberRegisterController@index')->name('index');
            Route::get('complete', 'MemberRegisterController@complete')->name('complete');
        });

        Route::namespace('cicilan')->prefix('cicilan')->name('cicilan.')->group(function () {
            Route::get('/', 'CicilanManagerController@index')->name('index');
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
    Route::namespace('sesi')->prefix('sesi')->name('sesi.')->group(function () {
        Route::get('/', 'SesiUseController@index')->name('index');
        Route::get('manager/', 'SesiManagerController@manager')->name('manager');
    });
    Route::namespace('report')->prefix('report/')->name('report.')->group(function(){
        Route::get('getSpecifyMemberSpending','ReportGraphController@getSpecifyMemberSpending')->name('getSpecifyMemberSpending');
        Route::get('/','ReportGraphController@index')->name('index');
    });
});

Route::prefix('cs')->name('cs.')->group(function (){
    Route::get('/', 'CSDashboardController@index')->name('index');

    Route::namespace('management')->prefix('management')->name('management.')->group(function () {

        Route::get('account/', 'UserDataController@account')->name('account');
    });

    Route::namespace('member')->prefix('member')->name('member.')->group(function () {
        Route::get('/', 'MemberDataController@index')->name('index');
        Route::get('edit/{id}', 'MemberDataController@edit')->name('edit');
        Route::get('view/{id}', 'MemberDataController@view')->name('view');
        Route::get('checkin', 'MemberCheckinController@index')->name('checkin');
        Route::get('checkout', 'MemberCheckoutController@index')->name('checkout');

        Route::namespace('registration')->prefix('registration')->name('registration.')->group(function () {
            Route::get('/', 'MemberRegisterController@index')->name('index');
            Route::get('complete', 'MemberRegisterController@complete')->name('complete');
        });
    });

    Route::namespace('cuti')->prefix('cuti')->name('cuti.')->group(function () {
        Route::get('/', 'CutiController@index')->name('index');
    });
    Route::namespace('sesi')->prefix('sesi')->name('sesi.')->group(function () {
        Route::get('/', 'SesiUseController@index')->name('index');
    });
});

Route::namespace('member')->prefix('member')->name('member.')->group(function () {
    Route::get('getMemberData', 'MemberDataController@getMemberData')->name('getMemberData');
    Route::get('requestModalAction', 'MemberDataController@requestModalAction')->name('requestModalAction');
    Route::post('update', 'MemberDataController@update')->name('update');
    Route::post('deleteMember', 'MemberDataController@deleteMember')->name('deleteMember');
    Route::get('edit/{id}/getMemberMembership', 'MemberDataController@getMemberMembership')->name('getMemberMembership');
    Route::get('edit/{id}/getMemberPT', 'MemberDataController@getMemberPT')->name('getMemberPT');
    Route::get('edit/{id}/getMemberLog', 'MemberDataController@getMemberLog')->name('getMemberLog');
    Route::get('print/{id}', 'MemberDataController@print')->name('print');
    Route::get('print/registrasi/{id}', 'MemberDataController@printRegister')->name('printRegister');
    Route::post('aktivasi', 'MemberDataController@aktivasi')->name('aktivasi');
    Route::get('preview', 'MemberCheckinController@preview')->name('preview');
    Route::post('checkin', 'MemberCheckinController@checkin')->name('checkin');
    Route::get('getCheckinMemberData', 'MemberCheckoutController@getCheckinMemberData')->name('getCheckinMemberData');
    Route::get('getCheckoutMemberData', 'MemberCheckoutController@getCheckoutMemberData')->name('getCheckoutMemberData');
    Route::post('checkoutMember', 'MemberCheckoutController@checkoutMember')->name('checkoutMember');
    Route::post('forceChangeStatus', 'MemberDataController@forceChangeStatus')->name('forceChangeStatus');

    Route::post('addTransaction', 'MemberDataController@addTransaction')->name('addTransaction');

    Route::get('dataChecking', 'MemberDataController@dataChecking')->name('dataChecking');
    Route::get('printPembelianSesi/{id}', 'MemberDataController@printPembelianSesi')->name('printPembelianSesi');

    Route::post('changePT', 'MemberDataController@changePT')->name('changePT');

    Route::get('exportExcelData', 'MemberDataController@exportExcelData')->name('exportExcelData');

    Route::namespace('registration')->prefix('registration')->name('registration.')->group(function () {
        Route::post('store', 'MemberRegisterController@store')->name('store');
        Route::get('print/{id}', 'MemberRegisterController@print')->name('print');
    });

    Route::namespace('cicilan')->prefix('cicilan')->name('cicilan.')->group(function () {
        Route::get('cicilanMemberData', 'CicilanManagerController@cicilanMemberData')->name('cicilanMemberData');
        Route::get('getMemberCicilanData', 'CicilanManagerController@getMemberCicilanData')->name('getMemberCicilanData');
        Route::post('bayarCicilan', 'CicilanManagerController@bayarCicilan')->name('bayarCicilan');
    });
});

Route::namespace('membership')->prefix('membership')->name('membership.')->group(function () {
    Route::get('getMembershipData', 'MembershipDataController@getMembershipData')->name('getMembershipData');
    Route::post('store', 'MembershipDataController@store')->name('store');
    Route::get('edit', 'MembershipDataController@edit')->name('edit');
    Route::post('update', 'MembershipDataController@update')->name('update');
    Route::post('destroy', 'MembershipDataController@destroy')->name('destroy');

    Route::get('dataChecking', 'MembershipDataController@dataChecking')->name('dataChecking');
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

Route::namespace('report')->prefix('report/')->name('report.')->group(function(){
    Route::get('getSpecifyMemberSpending','ReportGraphController@getSpecifyMemberSpending')->name('getSpecifyMemberSpending');
    Route::get('performaMember','ReportGraphController@performaMember')->name('performaMember');
    Route::get('performaCuti','ReportGraphController@performaCuti')->name('performaCuti');
    Route::get('performaRevenue','ReportGraphController@performaRevenue')->name('performaRevenue');
});

//
Route::namespace('cuti')->prefix('cuti')->name('cuti.')->group(function () {
    Route::get('getCutiData', 'CutiController@getCutiData')->name('getCutiData');
    Route::get('preview', 'CutiController@preview')->name('preview');
    Route::get('checkCapability', 'CutiController@checkCapability')->name('checkCapability');
    Route::get('abortCuti', 'CutiController@abortCuti')->name('abortCuti');
    Route::post('approve', 'CutiController@approve')->name('approve');
    Route::post('remove', 'CutiController@remove')->name('remove');
});

Route::namespace('sesi')->prefix('sesi')->name('sesi.')->group(function () {
    Route::get('getMemberData', 'SesiUseController@getMemberData')->name('getMemberData');
    Route::post('useSesi', 'SesiUseController@useSesi')->name('useSesi');
});

Route::namespace('management')->prefix('management')->name('management.')->group(function () {
    Route::get('getAccountData', 'UserDataController@getAccountData')->name('getAccountData');
    Route::post('editAccountData', 'UserDataController@editAccountData')->name('editAccountData');
});

Route::namespace('sesi')->prefix('sesi')->name('sesi.')->group(function () {
    Route::get('getSesiData', 'SesiManagerController@getSesiData')->name('getSesiData');
    Route::post('createSesi', 'SesiManagerController@createSesi')->name('createSesi');
    Route::get('edit', 'SesiManagerController@edit')->name('edit');
    Route::post('update', 'SesiManagerController@update')->name('update');
    Route::post('deleteSesi', 'SesiManagerController@deleteSesi')->name('deleteSesi');
});
