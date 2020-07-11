<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_ecommerce\Entities\Cart;
use Adnduweb\Ci4_ecommerce\Entities\Order;
use Adnduweb\Ci4_ecommerce\Entities\Carrier;
use Adnduweb\Ci4_customer\Entities\Customer;
use App\Models\CurrencyModel;
use Adnduweb\Ci4_ecommerce\Models\CartModel;
use Adnduweb\Ci4_ecommerce\Models\OrderModel;
use Adnduweb\Ci4_ecommerce\Models\CarriersModel;
use Adnduweb\Ci4_customer\Models\CustomerModel;
use Adnduweb\Ci4_ecommerce\Models\ProductModel;
use Adnduweb\Ci4_ecommerce\Models\CategoryModel;
use Adnduweb\Ci4_page\Libraries\PageDefault;

class FrontShopController extends \App\Controllers\Front\FrontController
{

    public function __construct()
    {
        helper('number');
        parent::__construct();
        $this->data['devise'] = service('Currency')->getDevise();
    }
    public function index()
    {
        // Load Header
        $header_parameter = array(
            'title' => lang('Front_default.categorie'),
            'meta_title' => lang('Front_default.shop_meta_title'),
            'meta_description' => lang('Front_default.shop_meta_description'),
            'url' => [1 => ['slug' => 'categories'], 2 => ['slug' => 'categories']],
        );

        $currPage                          = $this->request->getGet('page');
        $this->data['currPage']            = ( isset($currPage) ) ? $currPage : 1;
        $this->data ['page']               = new PageDefault($header_parameter);
        $this->data ['no_follow_no_index'] = 'index follow';
        $this->data ['id']                 = 'shop';
        $this->data ['class']              = $this->data['class'] . ' shop';
        $this->data ['meta_title']         = '';
        $this->data ['meta_description']   = '';
        //$this->data['products'] = (new ProductModel)->getListProduct();
        $this->data['categories'] = (new CategoryModel())->getCategoriesNavbar('desc');
        //print_r($this->data['categories']); exit;

        $model = new ProductModel();
        $categoryModel = new CategoryModel();

        $this->data['products'] = $model->getPaginate(config('Ecommerce')->pagination, $this->request->getGet());
        $this->data['pager'] = $model->pager;
        $this->data['countAll'] = $model->where('active', 1)->countAllResults();

        return view($this->get_current_theme_view('__template_part/ecommerce/my-shop', 'default'), $this->data);
    }

    public function showCategory($slug)
    {

        $pageLight = (new CategoryModel())->getIdPageBySlug($slug);
        if (empty($pageLight)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Core.Cannot find the page item : {0}', [$slug]));
        }
        $category = (new CategoryModel())->where('id', $pageLight->id)->first();

        // Load Header
        $header_parameter = array(
            'title' => $category->getBName(),
            'meta_title' => lang('Front_default.shop_meta_title'),
            'meta_description' => lang('Front_default.shop_meta_description'),
            'url' => [1 => ['slug' => 'categories'], 2 => ['slug' => 'categories']],
        );

        $currPage                          = $this->request->getGet('page');
        $this->data['currPage']            = ( isset($currPage) ) ? $currPage : 1;
        $this->data ['page']               = new PageDefault($header_parameter);
        $this->data ['no_follow_no_index'] = 'index follow';
        $this->data ['id']                 = '(new CategoryModel())';
        $this->data ['class']              = $this->data['class'] . ' (new CategoryModel())';
        $this->data ['meta_title']         = '';
        $this->data ['meta_description']   = '';
        //$this->data['products'] = (new ProductModel)->getListProduct();
        $this->data['categories'] = (new CategoryModel())->getCategoriesNavbar('desc');
        //print_r($this->data['categories']); exit;

        $model = new ProductModel();
        $categoryModel = new CategoryModel();

        $this->data['products'] = $model->getPaginate(config('Ecommerce')->pagination, $this->request->getGet(), $pageLight->id);
        $this->data['pager'] = $model->pager;
        $this->data['countAll'] = $model->where('active', 1)->countAllResults();

        return view($this->get_current_theme_view('__template_part/ecommerce/my-shop', 'default'), $this->data);
    }

}
