<?php

use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\Seller\AdsPackageController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\CommisionPackageController;
use App\Http\Controllers\Seller\PosController;
use App\Http\Controllers\SellerPackageController;

//Upload
Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user', 'prevent-back-history'], 'as' => 'seller.'], function () {
    Route::controller(AizUploadController::class)->group(function () {
        Route::any('/uploads', 'index')->name('uploaded-files.index');
        Route::any('/uploads/create', 'create')->name('uploads.create');
        Route::any('/uploads/file-info', 'file_info')->name('my_uploads.info');
        Route::get('/uploads/destroy/{id}', 'destroy')->name('my_uploads.destroy');
        Route::post('/bulk-uploaded-files-delete', 'bulk_uploaded_files_delete')->name('bulk-uploaded-files-delete');
    });
});

Route::group(['namespace' => 'App\Http\Controllers\Seller', 'prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user', 'prevent-back-history'], 'as' => 'seller.'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });
    Route::get('/orders/count', [App\Http\Controllers\Seller\OrderController::class, 'countOrders'])->name('orders.count');
    Route::get('/conversations/count', [App\Http\Controllers\Seller\ConversationController::class, 'countConversations'])->name('conversations.count');
    Route::get('/dashboard/count', [App\Http\Controllers\Seller\DashboardController::class, 'countSidebarNotification'])->name('dashboard.count');

    // Product
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products');
        Route::get('/product/create', 'create')->name('products.create');
        Route::post('/products/store/', 'store')->name('products.store');
        Route::get('/product/{id}/edit', 'edit')->name('products.edit');
        Route::post('/products/update/{product}', 'update')->name('products.update');
        Route::get('/products/duplicate/{id}', 'duplicate')->name('products.duplicate');
        Route::post('/products/sku_combination', 'sku_combination')->name('products.sku_combination');
        Route::post('/products/sku_combination_edit', 'sku_combination_edit')->name('products.sku_combination_edit');
        Route::post('/products/add-more-choice-option', 'add_more_choice_option')->name('products.add-more-choice-option');
        Route::post('/products/seller/featured', 'updateFeatured')->name('products.featured');
        Route::post('/products/published', 'updatePublished')->name('products.published');
        Route::get('/products/destroy/{id}', 'destroy')->name('products.destroy');
        Route::post('/products/bulk-delete', 'bulk_product_delete')->name('products.bulk-delete');
        Route::post('/product-search', 'product_search')->name('product.search');
        Route::post('/get-selected-products', 'get_selected_products')->name('get-selected-products');

        // category-wise discount set
        Route::get('/categories-wise-product-discount', 'categoriesWiseProductDiscount')->name('categories_wise_product_discount');
        Route::post('/set-product-discount', 'setProductDiscount')->name('set_product_discount');
    });

    // Product Bulk Upload
    Route::controller(ProductBulkUploadController::class)->group(function () {
        Route::get('/product-bulk-upload/index', 'index')->name('product_bulk_upload.index');
        Route::post('/product-bulk-upload/store', 'bulk_upload')->name('bulk_product_upload');
        Route::group(['prefix' => 'bulk-upload/download'], function () {
            Route::get('/category', 'pdf_download_category')->name('pdf.download_category');
            Route::get('/brand', 'pdf_download_brand')->name('pdf.download_brand');
        });
    });

    // Digital Product
    Route::controller(DigitalProductController::class)->group(function () {
        Route::get('/digitalproducts', 'index')->name('digitalproducts');
        Route::get('/digitalproducts/create', 'create')->name('digitalproducts.create');
        Route::post('/digitalproducts/store', 'store')->name('digitalproducts.store');
        Route::get('/digitalproducts/{id}/edit', 'edit')->name('digitalproducts.edit');
        Route::post('/digitalproducts/update/{product}', 'update')->name('digitalproducts.update');
        Route::get('/digitalproducts/destroy/{id}', 'destroy')->name('digitalproducts.destroy');
        Route::get('/digitalproducts/download/{id}', 'download')->name('digitalproducts.download');
    });

    // Note
    Route::resource('note', NoteController::class);
    Route::controller(NoteController::class)->group(function () {
        Route::get('/note/edit/{id}', 'edit')->name('note.edit');
        Route::get('note/delete/{note}', 'destroy')->name('note.delete');
    });

    //Coupon
    Route::resource('coupon', CouponController::class);
    Route::controller(CouponController::class)->group(function () {
        Route::post('/coupon/get_form', 'get_coupon_form')->name('coupon.get_coupon_form');
        Route::post('/coupon/get_form_edit', 'get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
        Route::get('/coupon/destroy/{id}', 'destroy')->name('coupon.destroy');
    });

    //Order
    Route::resource('orders', OrderController::class);
    Route::controller(OrderController::class)->group(function () {
        // Route::post('/orders/update_delivery_status', 'update_delivery_status')->name('orders.update_delivery_status');
        // Route::post('/orders/update_payment_status', 'update_payment_status')->name('orders.update_payment_status');
        Route::post('/process-order', 'processOrder')->name('process-order');

        // Order bulk export
        Route::get('/order-bulk-export', 'orderBulkExport')->name('order-bulk-export');
    });

    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice/{order_id}', 'invoice_download')->name('invoice.download');
    });

    //Review
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/product-reviews', 'index')->name('product-reviews');
        Route::get('/product/detail-reviews/{id}', 'detailReviews')->name('detail-reviews');
    });

    //Shop
    Route::controller(ShopController::class)->group(function () {
        Route::get('/shop', 'index')->name('shop.index');
        Route::post('/shop/update', 'update')->name('shop.update');
        Route::post('/shop/banner-update', 'bannerUpdate')->name('shop.banner.update');
        Route::get('/shop/apply-for-verification', 'verify_form')->name('shop.verify');
        Route::post('/shop/verification_info_store', 'verify_form_store')->name('shop.verify.store');
    });

    //Payments
    Route::resource('payments', PaymentController::class);

    // Profile Settings
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::post('/profile/update/{id}', 'update')->name('profile.update');
    });

    // Address
    Route::resource('addresses', AddressController::class);
    Route::controller(AddressController::class)->group(function () {
        Route::post('/get-states', 'getStates')->name('get-state');
        Route::post('/get-cities', 'getCities')->name('get-city');
        Route::post('/address/update/{id}', 'update')->name('addresses.update');
        Route::get('/addresses/destroy/{id}', 'destroy')->name('addresses.destroy');
        Route::get('/addresses/set_default/{id}', 'set_default')->name('addresses.set_default');
    });

    // Money Withdraw Requests
    Route::controller(SellerWithdrawRequestController::class)->group(function () {
        Route::get('/money-withdraw-requests', 'index')->name('money_withdraw_requests.index');
        Route::post('/money-withdraw-request/store', 'store')->name('money_withdraw_request.store');
    });

    // Money Deposit Requests
    Route::controller(SellerDepositRequestController::class)->group(function () {
        Route::get('/money-deposit-requests', 'index')->name('money_deposit_requests.index');
        Route::post('/money-deposit-request/store', 'store')->name('money_deposit_request.store');
    });

    // Commission History
    Route::controller(CommissionHistoryController::class)->group(function () {
        Route::get('/commission-history', 'index')->name('commission-history.index');
    });

    // Ads History
    Route::controller(AdsHistoryController::class)->group(function () {
        Route::get('/ads-history', 'index')->name('ads-history.index');
    });

    //Conversations
    Route::controller(ConversationController::class)->group(function () {
        Route::get('/conversations', 'index')->name('conversations.index');
        Route::get('/conversations/show/{id}', 'show')->name('conversations.show');
        Route::post('conversations/refresh', 'refresh')->name('conversations.refresh');
        Route::post('conversations/message/store', 'message_store')->name('conversations.message_store');
    });

    // product query (comments) show on seller panel
    Route::controller(ProductQueryController::class)->group(function () {
        Route::get('/product-queries', 'index')->name('product_query.index');
        Route::get('/product-queries/{id}', 'show')->name('product_query.show');
        Route::put('/product-queries/{id}', 'reply')->name('product_query.reply');
    });

    // Support Ticket
    Route::controller(SupportTicketController::class)->group(function () {
        Route::get('/support_ticket', 'index')->name('support_ticket.index');
        Route::post('/support_ticket/store', 'store')->name('support_ticket.store');
        Route::get('/support_ticket/show/{id}', 'show')->name('support_ticket.show');
        Route::post('/support_ticket/reply', 'ticket_reply_store')->name('support_ticket.reply_store');
    });

    // Notifications
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/all-notification', 'index')->name('all-notification');
        Route::post('/notifications/bulk-delete', 'bulkDelete')->name('notifications.bulk_delete');
        Route::get('/notification/read-and-redirect/{id}', 'readAndRedirect')->name('notification.read-and-redirect');
    });

    // Seller Package Purchase
    Route::get('/seller-packages', [SellerPackageController::class, 'seller_packages_list'])->name('seller_packages_list');
    Route::post('/seller-packages/purchase', [SellerPackageController::class, 'purchase_package'])->name('purchase_package');

    // POS System
    Route::controller(PosController::class)->group(function () {
        Route::get('/pos', 'index')->name('pos.index');
        Route::post('/pos/add-to-cart', 'addToCart')->name('pos.addToCart');
        Route::post('/pos/update-quantity', 'updateQuantity')->name('pos.updateQuantity');
        Route::post('/pos/remove-from-cart', 'removeFromCart')->name('pos.removeFromCart');
        Route::get('/pos/get-cart', 'getCart')->name('pos.getCart');
        Route::post('/pos/set-discount', 'setDiscount')->name('pos.setDiscount');
        Route::post('/pos/set-shipping', 'setShipping')->name('pos.setShipping');
        Route::post('/pos/checkout', 'checkout')->name('pos.checkout');
        Route::get('/pos/products', 'productList')->name('pos.products');
    });


    //Commision Package
    Route::prefix('commission-packages')->group(function () {
        Route::get('/', [CommisionPackageController::class, 'index'])->name('commission-packages.index');
        Route::get('/register/{id}', [CommisionPackageController::class, 'register'])->name('commission-packages.register');
    });

    //Ads Package
    Route::prefix('ads-packages')->group(function () {
        Route::get('/', [AdsPackageController::class, 'index'])->name('ads-packages.index');
        Route::get('/register/{id}', [AdsPackageController::class, 'register'])->name('ads-packages.register');
    });
});
