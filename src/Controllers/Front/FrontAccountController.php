<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_ecommerce\Entities\Cart;
use Adnduweb\Ci4_ecommerce\Entities\Order;
use Adnduweb\Ci4_ecommerce\Entities\Carrier;
use Adnduweb\Ci4_ecommerce\Entities\Customer;
use Adnduweb\Ci4_ecommerce\Models\CartModel;
use Adnduweb\Ci4_ecommerce\Models\OrderModel;
use Adnduweb\Ci4_ecommerce\Models\CarriersModel;
use Adnduweb\Ci4_ecommerce\Models\CustomerModel;
use Adnduweb\Ci4_page\Libraries\PageDefault;

class FrontAccountController extends FrontEcommerceController
{

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {

        // Load Header
        $header_parameter = array(
            'title' => lang('Front_default.account'),
            'meta_title' => lang('Front_default.account_meta_title'),
            'meta_description' => lang('Front_default.account_meta_description'),
            'url' => [1 => ['slug' => 'my-account'], 2 => ['slug' => 'my-account']],
        );


        $this->data['page'] = new PageDefault($header_parameter);
        $this->data['no_follow_no_index'] = 'index follow';
        $this->data['id']  = 'authentification';
        $this->data['class'] = $this->data['class'] . ' authentification';
        $this->data['meta_title'] = '';
        $this->data['meta_description'] = '';
        $this->data['config'] = $this->config;

        return view($this->get_current_theme_view('__template_part/ecommerce/my-account', 'default'), $this->data);
    }
}
