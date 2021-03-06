<?php

use Illuminate\Support\Facades\Route;

Route::prefix('sale')->middleware('auth')->group(function() {
    Route::get('/', 'SaleController@index');

    Route::middleware('permission')->group(function() {
        Route::resource('sale','SaleController');
        Route::post('/invoice-details', 'SaleController@invoiceDetails')->name('invoice.details');
        Route::post('/quotation-to-store', 'SaleController@quotation_to_store')->name('sale.quotation_to_store');
        Route::post('/store-shipping', 'SaleController@storeShipping')->name('store.shipping');
        Route::get('/sale-delete-modal/{id}', 'SaleController@destroy')->name('sale.delete');
    });

    Route::post('/sale_order_details', 'SaleController@orderDetails')->name('get_sale_details');
    Route::post('/sale_order_details_lazada', 'SaleController@orderDetailsLazada')->name('get_sale_details_lazada');
    Route::get('sale-return-create', 'SaleController@make_return_list')->name('sale.sale_return_list');
    Route::get('sale-return-details/{id}', 'SaleController@return_details')->name('sale.return_detail_show');
    Route::get('sale-print-view/{id}', 'SaleController@print_view')->name('sale.print_view');
    Route::get('sale-print-view-pos/{id}', 'SaleController@print_view_pos')->name('sale.print_view_pos');
    Route::get('sale-challan-print-view/{id}', 'SaleController@challan_print_view')->name('sale.challan_print_view');

    Route::get('/sale-pdf/{id}', 'SaleController@getPdf')->name('sale.pdf');
    Route::get('/sale-challan-pdf/{id}', 'SaleController@getChallanPdf')->name('sale.challan_pdf');

    Route::get('/sale-configurations', 'SaleController@saleConfiguration')->name('sale.configurations');
    //Purchase Return List
    Route::post('/product_add','SaleController@storeProduct')->name('sale.product.add');
    Route::get('/sale-return-list', 'SaleController@returnList')->name('sale.return.index')->middleware('permission');
    Route::get('/sale-return/{id}', 'SaleController@saleReturn')->name('sale.return');
    Route::post('/sale-return-update/{id}', 'SaleController@returnItem')->name('sale.return.update');
    Route::get('/sale-return-excelExport', 'SaleController@fileExport')->name('sale.excel');
    Route::post('/customer-details', 'SaleController@customerDetails')->name('customer.details');
    Route::get('/sale-payments/{id}', 'SaleController@payments')->name('sale.payment');
    Route::post('/sale-payments-details', 'SaleController@payments_details_sale')->name('sale.get_sale_payment_details');
    Route::get('/sale-clone/{id}', 'SaleController@cloneSale')->name('sale.clone');
	Route::post('/sale-order-preview', 'SaleController@getPreview')->name('sale.order.preview');
    Route::post('/sale-store-payments/{id}', 'SaleController@savePayment')->name('sale.store.payment');
    Route::get('/sale-send-mail/{id}', 'SaleController@send_mail_quotation')->name('sale.send_mail');
    Route::get('/sale-quotation-convert/{id}','SaleController@convertToQuotation')->name('sale.convertTosale');
    Route::get('/sale-due-list','SaleController@dueList')->name('sale.due.list');
    Route::post('/item-session-delete','SaleController@itemSessionDelete')->name('item.session.delete');

    //Conditional Sale
    Route::get('/conditional-sales-approve/{id}', 'SaleController@statusChange')->name('conditional.sale.approve');
    Route::get('/return-sales-approve/{id}', 'SaleController@returnApprove')->name('return.sale.approve');
    Route::post('/order-sales-receive', 'SaleController@saleOrder')->name('sale.order.receive');
    Route::post('/sale-shipping_info', 'SaleController@shippingInfo')->name('sale.shipping_info');
    Route::post('/sale-item_delete', 'SaleController@itemDestroy')->name('item.delete');
    Route::get('/due/invoice-list', 'SaleController@invoiceList')->name('due.invoice.list');
    Route::post('/lazada-sale-list', 'SaleController@lazadaList')->name('sale.lazada_list');
    Route::post('/lazada-sale-list-reverse', 'SaleController@lazadaListReverse')->name('sale.lazada_list_reverse');
    Route::get('/reverse-order/{id}', 'SaleController@formrevrese')->name('sale.lazada_reverse_form');

    Route::post('/product-modal-for-select', 'SaleController@product_modal_for_select')->name('sale.product_modal_for_select');
    Route::post('/product-modal-for-select-pos', 'SaleController@product_modal_for_select_pos')->name('sale.product_modal_for_select_pos');
});
    Route::get('/conditional-sales', 'SaleController@conditionalSale')->name('conditional.sale.index')->middleware('permission');
    Route::resource('conditional-sale','SaleController');

    
Route::get('/getcombo', 'SaleController@getCombo')->name('getCombo');
Route::post('/settopacked', 'SaleController@setToPacked')->name('setToPacked');
Route::post('/settorts', 'SaleController@setToRts')->name('setToRts');
Route::post('/settocancel', 'SaleController@setToCancel')->name('setToCancel');
// Route::post('/getdocumentlz', 'SaleController@getDocument')->name('getDocumentLz');
Route::get('/orderitem/{ordernumber}/{token}/{url}', 'SaleController@get_orderItem')->name('get_orderItem');
Route::get('/transactions', 'SaleController@get_transaction')->name('get_transaction');
Route::get('/getdocumentlz', 'SaleController@getDocument')->name('getDocumentLz');
Route::get('/getDocumentMultiple', 'SaleController@getDocumentMultiple')->name('getDocumentMultiple');
Route::get('/getMultipleOrderItem', 'SaleController@getMultipleOrderItem')->name('getMultipleOrderItem');
Route::get('/rtsMultiple', 'SaleController@rtsMultiple')->name('rtsMultiple');
Route::get('/packedMultiple', 'SaleController@packedMultiple')->name('packedMultiple');