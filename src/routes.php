<?php

//use Illuminate\Support\Facades\Route;

Route::group(
    ['namespace' => 'AdminPanel\Controllers', 'prefix' => 'admin', 'middleware' => ['web']], function () {

        Route::get('/orders', 'AdminController@index')->name('admin orders');
        //страница с пакетами
        Route::get('/packs', 'AdminController@getPacks');
        //добавление пакета
        Route::post('/create-pack', 'AdminController@createPack')->name('create new pack');
        //удаление пакета
        Route::post('/delete-pack/{packID}', 'AdminController@deletePack');
        //удаление отзыва
        Route::post('/delete-feedback/{feedbackID}', 'AdminController@deleteFeedback');
        //обновление цен на услугу во всех странах
        Route::post('/update-prices', 'AdminController@updatePrices');
        //добавить комментарий к заказу
        Route::post('/update-comment-order', 'AdminController@updateCommentOrder')->name('comment');
        //добавить комментарий к отзыву
        Route::post('/update-comment-feedback', 'AdminController@updateCommentFeedback')->name('comment to feedback');
        //страница с обратной связью
        Route::get('/feedback', 'AdminController@feedback')->name('feedback');
        //перещелкнуть фильтр [только оплаченные/все]
        Route::get('/set-is_paid', 'AdminController@isPaid');
        //поиск по 3 колонкам
        Route::get('/search', 'AdminController@search');
        //страница с прайсами для выбранной услуги
        Route::get('/prices/{service}', 'AdminController@prices')->name('prices');
        //страница создания промокода
        Route::get('/promocode', 'AdminController@promocode')->name('promocode');
        //генерирование промокода
        Route::post('/promocode', 'AdminController@generatePromocode')->name('generate promocode');
        //статистика
        Route::get('/stats', 'AdminController@stats')->name('stats');

    }
);
