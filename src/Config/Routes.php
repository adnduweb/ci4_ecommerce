 <?php

    $routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_ecommerce\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

        $routes->get('(:any)/catalogue/product', 'AdminProductController::renderViewList', ['as' => 'product']);
        $routes->get('(:any)/catalogue/product/edit/(:any)', 'AdminProductController::renderForm/$2');
        $routes->post('(:any)/catalogue/product/edit/(:any)', 'AdminProductController::postProcess/$2');
        $routes->get('(:any)/catalogue/product/add', 'AdminProductController::renderForm');
        $routes->post('(:any)/catalogue/product/add', 'AdminProductController::postProcess');
        $routes->get('(:any)/catalogue/product/dupliquer/(:segment)', 'AdminProductController::dupliquer/$2');
        $routes->get('(:any)/catalogue/product/fake/(:segment)', 'AdminProductController::fake/$2');

        $routes->get('(:any)/catalogue/category', 'AdminCategoryController::renderViewList', ['as' => 'category']);
        $routes->get('(:any)/catalogue/category/edit/(:any)', 'AdminCategoryController::renderForm/$2');
        $routes->post('(:any)/catalogue/category/edit/(:any)', 'AdminCategoryController::postProcess/$2');
        $routes->get('(:any)/catalogue/category/add', 'AdminCategoryController::renderForm');
        $routes->post('(:any)/catalogue/category/add', 'AdminCategoryController::postProcess');
        $routes->get('(:any)/catalogue/category/dupliquer/(:segment)', 'AdminCategoryController::dupliquer/$2');


        $routes->get('(:any)/catalogue/brand', 'AdminBrandController::renderViewList', ['as' => 'brand']);
        $routes->get('(:any)/catalogue/brand/edit/(:any)', 'AdminBrandController::renderForm/$2');
        $routes->post('(:any)/catalogue/brand/edit/(:any)', 'AdminBrandController::postProcess/$2');
        $routes->get('(:any)/catalogue/brand/add', 'AdminBrandController::renderForm');
        $routes->post('(:any)/catalogue/brand/add', 'AdminBrandController::postProcess');

        $routes->get('(:any)/catalogue/supplier', 'AdminSupplierController::renderViewList', ['as' => 'supplier']);
        $routes->get('(:any)/catalogue/supplier/edit/(:any)', 'AdminSupplierController::renderForm/$2');
        $routes->post('(:any)/catalogue/supplier/edit/(:any)', 'AdminSupplierController::postProcess/$2');
        $routes->get('(:any)/catalogue/supplier/add', 'AdminSupplierController::renderForm');
        $routes->post('(:any)/catalogue/supplier/add', 'AdminSupplierController::postProcess');
    });



    $locale = '/';
    if (service('Settings')->setting_activer_multilangue == true) {
        $locale = '/{locale}';
    }

    //E-commerce
    $routes->get($locale . '/logout', 'FrontAuthenticationController::logout', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front', 'as' => 'logout-customer']);
    $routes->get($locale . '/signin', 'FrontAuthenticationController::SignIn', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front', 'as' => 'signin']);
    $routes->post($locale . '/signin', 'FrontAuthenticationController::postProcessSignIn', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);
    $routes->get($locale . '/signup', 'FrontAuthenticationController::SignUp', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);
    $routes->post($locale . '/signup', 'FrontAuthenticationController::postProcessSignUp', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);
    $routes->get($locale . '/activate-account-customer', 'FrontAuthenticationController::ActivateAccount', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);
    $routes->get($locale . '/my-account', 'FrontAccountController::index', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front', 'filter' => 'loginCustomer']);
    $routes->get($locale . '/shopping-cart', 'FrontShoppingCartController::ShoppingCart', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);

    $routes->get($locale . '/boutique', 'FrontShopController::index', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);
    $routes->get($locale . '/boutique/categories/(:any)', 'FrontShopController::showCategory/$1', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front']);

    $routes->post('/shoppingcart/ajax', 'FrontShoppingCartController::ajax', ['namespace' => '\Adnduweb\Ci4_ecommerce\Controllers\Front', 'as' => 'ajax-shoppingcart']);
