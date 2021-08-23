<?php

use Illuminate\Support\Facades\Route;

Route::prefix('lazadatoken')->middleware('auth')->group(function() {
    Route::resource('/','LazadaTokenController');
    Route::get('/generatetoken', 'LazadaTokenController@generate_token')->name('generate_token');
    Route::get('/refreshtoken', 'LazadaTokenController@refresh_token')->name('refresh_token');
    Route::get('/refreshtoken/all', 'LazadaTokenController@refresh_token_all')->name('refresh_token_all');
});