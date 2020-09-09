 <?php

    $routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_ecommerce\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/product', 'AdminProductController::renderViewList', ['as' => 'product']);
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/product/edit/(:any)', 'AdminProductController::renderForm/$1');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/product/edit/(:any)', 'AdminProductController::postProcess/$1');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/product/add', 'AdminProductController::renderForm');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/product/add', 'AdminProductController::postProcess');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/product/dupliquer/(:segment)', 'AdminProductController::dupliquer/$1');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/product/fake/(:segment)', 'AdminProductController::fake/$1');

        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/category', 'AdminCategoryController::renderViewList', ['as' => 'category']);
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/category/edit/(:any)', 'AdminCategoryController::renderForm/$1');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/category/edit/(:any)', 'AdminCategoryController::postProcess/$1');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/category/add', 'AdminCategoryController::renderForm');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/category/add', 'AdminCategoryController::postProcess');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/category/dupliquer/(:segment)', 'AdminCategoryController::dupliquer/$1');


        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/brand', 'AdminBrandController::renderViewList', ['as' => 'brand']);
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/brand/edit/(:any)', 'AdminBrandController::renderForm/$1');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/brand/edit/(:any)', 'AdminBrandController::postProcess/$1');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/brand/add', 'AdminBrandController::renderForm');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/brand/add', 'AdminBrandController::postProcess');

        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/supplier', 'AdminSupplierController::renderViewList', ['as' => 'supplier']);
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/supplier/edit/(:any)', 'AdminSupplierController::renderForm/$1');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/supplier/edit/(:any)', 'AdminSupplierController::postProcess/$1');
        $routes->get(config('Ecommerce')->urlMenuAdmin . '/catalogue/supplier/add', 'AdminSupplierController::renderForm');
        $routes->post(config('Ecommerce')->urlMenuAdmin . '/catalogue/supplier/add', 'AdminSupplierController::postProcess');
    });



    $locale = '/';
    if (service('Settings')->setting_activer_multilangue == true) {
        $locale = '/{locale}';
    }

    //E-commerce
    $routes->get($locale . '/shopping-cart', 'FrontShoppingCartController::ShoppingCart', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);

    $routes->get($locale . '/boutique', 'FrontShopController::index', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);
    $routes->get($locale . '/boutique/categories/(:any)', 'FrontShopController::showCategory/$1', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);

    $routes->post('/shoppingcart/ajax', 'FrontShoppingCartController::ajax', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front', 'as' => 'ajax-shoppingcart']);
