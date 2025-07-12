<?php

Route::group([
    'namespace' => 'CryptaTech\Seat\Fitting\Http\Controllers',
    'middleware' => ['web', 'auth'],
    'prefix' => 'api/v2/fitting/web',
], function () {
    Route::get('/fitting/list', [
        'as' => 'cryptafitting::api.web.fitting.list',
        'uses' => 'ApiFittingController@getFittingList',
    ]);
    Route::get('/fitting/get/{id}', [
        'as' => 'cryptafitting::api.web.fitting.get',
        'uses' => 'ApiFittingController@getFittingById',
    ]);
    Route::get('/doctrine/list', [
        'as' => 'cryptafitting::api.web.doctrine.list',
        'uses' => 'ApiFittingController@getDoctrineList',
    ]);
    Route::get('/doctrine/get/{id}', [
        'as' => 'cryptafitting::api.web.doctrine.get',
        'uses' => 'ApiFittingController@getDoctrineById',
    ]);
});

Route::group([
    'namespace' => 'CryptaTech\Seat\Fitting\Http\Controllers',
    'prefix' => 'fitting',
], function () {
    Route::group([
        'middleware' => ['web', 'auth', 'locale'],
    ], function () {
        Route::get('/', [
            'as' => 'cryptafitting::view',
            'uses' => 'FittingController@getFittingView',
            'middleware' => 'can:fitting.view',
        ]);
        Route::get('/about', [
            'as' => 'cryptafitting::about',
            'uses' => 'FittingController@getAboutView',
            'middleware' => 'can:fitting.view',
        ]);
        Route::get('/settings', [
            'as' => 'fitting.settings',
            'uses' => 'FittingController@getSettings',
            'middleware' => 'can:fitting.settings',
        ]);
        Route::post('/settings', [
            'as' => 'fitting.saveSettings',
            'uses' => 'FittingController@saveSettings',
            'middleware' => 'can:fitting.settings',
        ]);
        Route::post('/postfitting', [
            'as' => 'cryptafitting::postFitting',
            'uses' => 'FittingController@postFitting',
            'middleware' => 'can:fitting.view',
        ]);
        Route::post('/postskills', [
            'as' => 'cryptafitting::postSkills',
            'uses' => 'FittingController@postSkills',
            'middleware' => 'can:fitting.view',
        ]);
        Route::post('/savefitting', [
            'as' => 'cryptafitting::saveFitting',
            'uses' => 'FittingController@saveFitting',
            'middleware' => 'can:fitting.create',
        ]);
        Route::get('/getfittingbyid/{id}', [
            'uses' => 'FittingController@getFittingById',
            'middleware' => 'can:fitting.doctrineview',
        ]);
        Route::get('/getfittingcostbyid/{id}', [
            'as' => 'cryptafitting::appraiseFitting',
            'uses' => 'FittingController@getFittingCostById',
            'middleware' => 'can:fitting.doctrineview',
        ]);
        Route::get('/getdoctrinebyid/{id}', [
            'as' => 'cryptafitting::getDoctrineById',
            'uses' => 'FittingController@getDoctrineById',
            'middleware' => 'can:fitting.doctrineview',
        ]);
        Route::get('/geteftfittingbyid/{id}', [
            'uses' => 'FittingController@getEftFittingById',
            'middleware' => 'can:fitting.view',
        ]);
        Route::get('/getskillsbyfitid/{id}', [
            'uses' => 'FittingController@getSkillsByFitId',
            'middleware' => 'can:fitting.doctrineview',
        ]);
        Route::get('/delfittingbyid/{id}', [
            'uses' => 'FittingController@deleteFittingById',
            'middleware' => 'can:fitting.create',
        ]);
        Route::get('/doctrine', [
            'as' => 'cryptafitting::doctrineview',
            'uses' => 'FittingController@getDoctrineView',
            'middleware' => 'can:fitting.doctrineview',
        ]);
        Route::get('/doctrine/{doctrine_id}', [
            'as' => 'fitting.doctrineviewdetails',
            'uses' => 'FittingController@getDoctrineView',
            'middleware' => 'can:fitting.doctrineview',
        ]);
        Route::get('/fittinglist', [
            'as' => 'cryptafitting::fitlist',
            'uses' => 'FittingController@getFittingList',
            'middleware' => 'can:fitting.view',
        ]);
        Route::post('/addDoctrine', [
            'as' => 'cryptafitting::addDoctrine',
            'uses' => 'FittingController@saveDoctrine',
            'middleware' => 'can:fitting.create',
        ]);
        Route::get('/getdoctrineedit/{id}', [
            'as' => 'cryptafitting::getDoctrineEdit',
            'uses' => 'FittingController@getDoctrineEdit',
            'middleware' => 'can:fitting.create',
        ]);
        Route::get('/deldoctrinebyid/{id}', [
            'as' => 'cryptafitting::delDoctrineById',
            'uses' => 'FittingController@delDoctrineById',
            'middleware' => 'can:fitting.create',
        ]);
        Route::get('/doctrineReport', [
            'as' => 'cryptafitting::doctrinereport',
            'uses' => 'FittingController@viewDoctrineReport',
            'middleware' => 'can:fitting.reportview',
        ]);
        Route::post('/runReport', [
            'as' => 'cryptafitting::runreport',
            'uses' => 'FittingController@runReport',
            'middleware' => 'can:fitting.reportview',
        ]);
    });
});
