 <?php

    $routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_ecommerce\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

        $routes->get('(:any)/catalogue/product', 'AdminProductController::renderViewList', ['as' => 'product']);
        $routes->get('(:any)/catalogue/product/edit/(:any)', 'AdminProductController::renderForm/$2');
        $routes->post('(:any)/catalogue/product/edit/(:any)', 'AdminProductController::postProcess/$2');
        $routes->get('(:any)/catalogue/product/add', 'AdminProductController::renderForm');
        $routes->post('(:any)/catalogue/product/add', 'AdminProductController::postProcess');
        $routes->get('(:any)/catalogue/product/dupliquer/(:segment)', 'AdminProductController::dupliquer/$2');

        $routes->get('(:any)/catalogue/category', 'AdminCategoryController::renderViewList', ['as' => 'category']);
        $routes->get('(:any)/catalogue/category/edit/(:any)', 'AdminCategoryController::renderForm/$2');
        $routes->post('(:any)/catalogue/category/edit/(:any)', 'AdminCategoryController::postProcess/$2');
        $routes->get('(:any)/catalogue/category/add', 'AdminCategoryController::renderForm');
        $routes->post('(:any)/catalogue/category/add', 'AdminCategoryController::postProcess');
        $routes->get('(:any)/catalogue/category/dupliquer/(:segment)', 'AdminCategoryController::dupliquer/$2');
        // $routes->get('public/blog/settings', 'AdminBlogSettingsController::renderForm');
        // $routes->post('public/blog/settings', 'AdminBlogSettingsController::postProcess');
    });
